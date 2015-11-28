<?php

require_once __DIR__ . '/IrisConnectAbstractController.php';

use Barbondev\IRISSDK\Common\Exception\ValidationException;
use Iris\IndividualApplication\DocumentUploader;
use Iris\Referencing\Form\Type\RootStepType;
use Iris\Referencing\Form\Type\UploadedFileCollectionItemType;
use Iris\Referencing\FormSet\Form\Type\AdditionalDetailsType;
use Iris\Referencing\FormSet\Form\Type\AddressHistoryType;
use Iris\Referencing\FormSet\Form\Type\FinancialRefereesType;
use Iris\Referencing\FormSet\Form\Type\LettingRefereeType;
use Iris\Referencing\FormSet\Form\Type\ProductType;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Referencing\FormSet\Form\Type\SummaryType;
use Iris\Referencing\FormSet\Form\Type\TenantDetailsType;
use Iris\Referencing\FormSet\Form\Type\TermsAndConditionsType;
use Barbondev\IRISSDK\Common\Enumeration\CompletionMethodsOptions;
use Iris\Referencing\FormSet\Form\Type\ProspectiveLandlordType;
use Iris\Utility\DeclarationOwnership\DeclarationOwnership;

/**
 * Class Connect_IrisAddTenantController
 *
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class Connect_IrisAddTenantController extends IrisConnectAbstractController
{
    /**
     * Case model class name
     */
    const MODEL_CASE_CLASS = 'Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase';

    /**
     * Application model class name
     */
    const MODEL_APPLICATION_CLASS = 'Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication';

    /**
     * Initialise controller
     */
    public function init()
    {
        parent::init();

        $this->getFormFlow()->setCanAgentCompleteProspectiveLandlord(
            $this->canAgentCompleteProspectiveLandlord()
        );

        $this->getFormFlow()->run();
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        if (
            $this->getSymfonyRequest()->query->has('uuid')
            && $this->getSymfonyRequest()->isMethod('GET')
        ) {

            $caseUuId = $this->getSymfonyRequest()->query->get('uuid', null);

            $case = $this
                ->getIrisAgentContext()
                ->getReferencingCaseClient()
                ->getReferencingCase(array(
                    'referencingCaseUuId' => $caseUuId,
                ))
            ;

            $this
                ->getAgentProgressiveStore()
                ->clearPrototypes()
                ->addPrototype($case)
                ->addPrototype(new ReferencingApplication())
                ->storePrototypes()
            ;
        }

        $form = $this->getFormFactory()->create(
            new RootStepType(new ProductType()),
            $this->getAgentProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS),
            array(
                'removeBack' => true
            )
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            $this->getFormFlow()->setForm($form);

            if ($form->get('next')->isClicked()) {

                if ($form->isValid()) {

                    $object = null;

                    try {
                        $object = $this
                            ->getAgentProgressiveStore()
                            ->store($form->getData())
                        ;
                    }
                    catch (ValidationException $e) {
                        $this->getFormValidationErrorBinder()->bind($form, $e);
                        $this->renderTwigView('/iris-add-tenant/generic-step.html.twig', array(
                            'bodyTitle' => 'Product',
                            'form' => $form->createView(),
                            'difference' => $this->getSymfonyRequest()->query->get('difference', null),
                            'numberOfTenants' => $this->getSymfonyRequest()->query->get('numberOfTenants', null),
                        ));
                        return;
                    }

                    /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
                    $application = $this->getAgentProgressiveStore()->getPrototypeByClass(self::MODEL_APPLICATION_CLASS);

                    // If we're completing by email and it's not a rent guarantee product

                    if ((CompletionMethodsOptions::COMPLETE_BY_EMAIL == $object->getCompletionMethod() &&
                        !$application->getProduct()->getHasRentGuarantee()) ||
                        (CompletionMethodsOptions::COMPLETE_BY_EMAIL == $object->getCompletionMethod() &&
                        !$this->canAgentCompleteProspectiveLandlord())) {
                        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase $case */
                        $case = $this
                            ->getAgentProgressiveStore()
                            ->getPrototypeByClass(self::MODEL_CASE_CLASS)
                        ;

                        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $case */
                        $application = $this
                            ->getAgentProgressiveStore()
                            ->getPrototypeByClass(self::MODEL_APPLICATION_CLASS)
                        ;

                        // Submit application
                        $submitSuccess = $this
                            ->getContainer()
                            ->get('iris.referencing.application.submission.application_submitter')
                            ->submit($application->getReferencingApplicationUuId(), '/iris-referencing/submission-error?errorCode={error_code}')
                        ;

                        // Was the submission a success?
                        if ($submitSuccess) {

                            // Do we need to collect another tenant?
                            $difference = $this
                                ->getContainer()
                                ->get('iris.referencing.application.application_counter')
                                ->getApplicantCountDifference($application->getReferencingApplicationUuId(), $case->getNumberOfTenants())
                            ;

                            // Check the difference
                            if ($difference > 0) {

                                // Clear and add existing prototypes
                                $this
                                    ->getAgentProgressiveStore()
                                    ->clearPrototypes()
                                    ->initialisePrototypes()
                                    ->storePrototypes()
                                ;

                                // Yes, we need to collect another tenant, redirect
                                $this->_helper->redirector->gotoUrlAndExit(
                                    sprintf(
                                        '/iris-add-tenant?uuid=%s&difference=%d&numberOfTenants=%d',
                                        $case->getReferencingCaseUuId(),
                                        $difference,
                                        $case->getNumberOfTenants()
                                    )
                                );
                            }
                            // Redirect to success
                            $this->_helper->redirector->gotoUrlAndExit(
                                sprintf(
                                    '/iris-add-tenant/email-to-tenant-success?uuid=%s',
                                    $case->getReferencingCaseUuId()
                                )
                            );
                        }
                        else {

                            // Something went wrong
                            $this->_helper->redirector->gotoUrlAndExit(
                                sprintf('/iris-referencing/failed-to-submit-application?uuid=%s', $application->getReferencingApplicationUuId())
                            );
                        }
                    }

                    if ($object) {
                        $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getNextUrl());
                    }
                }
            }
        }

        $this->renderTwigView('/iris-add-tenant/generic-step.html.twig', array(
            'bodyTitle' => 'Product',
            'form' => $form->createView(),
            'difference' => $this->getSymfonyRequest()->query->get('difference', null),
            'numberOfTenants' => $this->getSymfonyRequest()->query->get('numberOfTenants', null),
        ));
    }

    /**
     * Email to tenant success action
     *
     * @return void
     */
    public function emailToTenantSuccessAction()
    {
        $caseUuId = $this->getSymfonyRequest()->query->get('uuid', null);
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $case */
        $application = $this
            ->getAgentProgressiveStore()
            ->getPrototypeByClass(self::MODEL_APPLICATION_CLASS)
        ;

        // Clear and add existing prototypes
        $this
            ->getAgentProgressiveStore()
            ->clearPrototypes()
            ->initialisePrototypes()
            ->storePrototypes()
        ;
       
       $caseApplications = $this
            ->getIrisAgentContext()
            ->getReferencingCaseClient()
            ->getApplications(array(
                'referencingCaseUuId' => $caseUuId,
            ))
        ;
        
        $this->renderTwigView('/iris-add-tenant/email-to-tenant-success.html.twig', array(
            'bodyTitle' => 'Email Sent to Tenant',
            'caseApplications' => $caseApplications,
        ));
    }

    /**
     * Tenant details action
     *
     * @return void
     */
    public function tenantDetailsAction()
    {
        $form = $this->getFormFactory()->create(
            new RootStepType(new TenantDetailsType()),
            $this->getAgentProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS)
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            $this->getFormFlow()->setForm($form);

            if ($form->get('back')->isClicked()) {
                $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getBackUrl());
            }

            if ($form->isValid()) {

                $object = null;

                try {
                    $object = $this
                        ->getAgentProgressiveStore()
                        ->store($form->getData())
                    ;
                }
                catch (ValidationException $e) {
                    $this->getFormValidationErrorBinder()->bind($form, $e);
                }

                if ($object) {
                    if ($form->get('next')->isClicked()) {
                        $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getNextUrl());
                    }
                }
            }
        }

        $this->renderTwigView('/iris-add-tenant/generic-step.html.twig', array(
            'bodyTitle' => 'Tenant Details',
            'form' => $form->createView(),
        ));
    }

    /**
     * Address history action
     *
     * @return void
     */
    public function addressHistoryAction()
    {
        $form = $this->getFormFactory()->create(
            new RootStepType(new AddressHistoryType()),
            $this->getAgentProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS)
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            $this->getFormFlow()->setForm($form);

            if ($form->get('back')->isClicked()) {
                $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getBackUrl());
            }

            if ($form->isValid()) {

                $object = null;

                try {
                    $object = $this
                        ->getAgentProgressiveStore()
                        ->store($form->getData())
                    ;
                }
                catch (ValidationException $e) {
                    $this->getFormValidationErrorBinder()->bind($form, $e);
                }

                if ($object) {
                    if ($form->get('next')->isClicked()) {
                        $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getNextUrl());
                    }
                }
            }
        }

        $this->renderTwigView('/iris-add-tenant/generic-step.html.twig', array(
            'bodyTitle' => 'Previous Address',
            'form' => $form->createView(),
        ));
    }

    /**
     * Financial referee action
     *
     * @return void
     */
    public function financialRefereeAction()
    {
        $form = $this->getFormFactory()->create(
            new RootStepType(new FinancialRefereesType()),
            $this->getAgentProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS)
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            $this->getFormFlow()->setForm($form);

            if ($form->get('back')->isClicked()) {
                $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getBackUrl());
            }

            if ($form->get('next')->isClicked()) {

                if ($form->isValid()) {

                    $object = null;

                    try {
                        $object = $this
                            ->getAgentProgressiveStore()
                            ->store($form->getData());
                    }
                    catch (ValidationException $e) {
                        // todo: fix recursive error binding
                        //$this->getFormValidationErrorBinder()->bind($form, $e);
                    }

                    if ($object) {
                        $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getNextUrl());
                    }
                }
            }
        }

        $this->renderTwigView('/iris-add-tenant/generic-step.html.twig', array(
            'bodyTitle' => 'Financial Referee',
            'form' => $form->createView(),
        ));
    }

    /**
     * Landlord action
     *
     * @return void
     */
    public function landlordAction()
    {
        $form = $this->getFormFactory()->create(
            new RootStepType(new ProspectiveLandlordType()),
            $this->getAgentProgressiveStore()->fetch(self::MODEL_CASE_CLASS)
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            $this->getFormFlow()->setForm($form);

            if ($form->get('back')->isClicked()) {
                $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getBackUrl());
            }

            if ($form->isValid()) {

                $object = null;

                try {
                    $object = $this
                        ->getAgentProgressiveStore()
                        ->store($form->getData())
                    ;
                }
                catch (ValidationException $e) {
                    $this->getFormValidationErrorBinder()->bind($form, $e);
                }

                /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
                $application = $this->getAgentProgressiveStore()->getPrototypeByClass(self::MODEL_APPLICATION_CLASS);

                // If this is a continue by email to tenant, submit the case
                if (CompletionMethodsOptions::COMPLETE_BY_EMAIL == $application->getCompletionMethod()) {

                    /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase $case */
                    $case = $this
                        ->getAgentProgressiveStore()
                        ->getPrototypeByClass(self::MODEL_CASE_CLASS)
                    ;

                    // Submit application
                    $submitSuccess = $this
                        ->getContainer()
                        ->get('iris.referencing.application.submission.application_submitter')
                        ->submit($application->getReferencingApplicationUuId(), '/iris-referencing/submission-error?errorCode={error_code}')
                    ;

                    // Was the submission a success?
                    if ($submitSuccess) {

                        // Do we need to collect another tenant?
                        $difference = $this
                            ->getContainer()
                            ->get('iris.referencing.application.application_counter')
                            ->getApplicantCountDifference($application->getReferencingApplicationUuId(), $case->getNumberOfTenants());

                        // Check the difference
                        if ($difference > 0) {

                            // Clear and add existing prototypes
                            $this
                                ->getAgentProgressiveStore()
                                ->clearPrototypes()
                                ->initialisePrototypes()
                                ->storePrototypes();

                            // Yes, we need to collect another tenant, redirect
                            $this->_helper->redirector->gotoUrlAndExit(
                                sprintf(
                                    '/iris-add-tenant?uuid=%s&difference=%d&numberOfTenants=%d',
                                    $case->getReferencingCaseUuId(),
                                    $difference,
                                    $case->getNumberOfTenants()
                                )
                            );
                        }

                        // Redirect to success
                        $this->_helper->redirector->gotoUrlAndExit('/iris-add-tenant/email-to-tenant-success');
                    }
                }

                if ($object) {
                    if ($form->get('next')->isClicked()) {
                        $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getNextUrl());
                    }
                }
            }
        }

        $this->renderTwigView('/iris-new-reference/generic-step.html.twig', array(
            'bodyTitle' => 'Prospective Landlord',
            'form' => $form->createView(),
        ));
    }

    /**
     * Current letter details (agent or landlord)
     *
     * @return void
     */
    public function lettingRefereeAction()
    {
        $form = $this->getFormFactory()->create(
            new RootStepType(new LettingRefereeType()),
            $this->getAgentProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS)
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            $this->getFormFlow()->setForm($form);

            if ($form->get('back')->isClicked()) {
                $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getBackUrl());
            }

            if ($form->isValid()) {

                $object = null;

                try {
                    $object = $this
                        ->getAgentProgressiveStore()
                        ->store($form->getData())
                    ;
                }
                catch (ValidationException $e) {
                    $this->getFormValidationErrorBinder()->bind($form, $e);
                }

                if ($object) {
                    if ($form->get('next')->isClicked()) {
                        $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getNextUrl());
                    }
                }
            }
        }

        $this->renderTwigView('/iris-add-tenant/generic-step.html.twig', array(
            'bodyTitle' => 'Current Letting/Estate Agent',
            'form' => $form->createView(),
        ));
    }

    /**
     * Get note handler
     *
     * @return \Iris\IndividualApplication\AdditionalInformationNote\NoteHandler
     */
    private function getNoteHandler()
    {
        return $this->getContainer()->get('iris.additional_information_note_handler.agent_tenant');
    }

    /**
     * Additional details
     *
     * @return void
     */
    public function additionalDetailsAction()
    {
        $application = $this->getAgentProgressiveStore()->getPrototypeByClass(self::MODEL_APPLICATION_CLASS);

        $form = $this->getFormFactory()->create(
            new RootStepType(new AdditionalDetailsType()),
            array(
                'step' => array(
                    'additionalInfo' => $this->getNoteHandler()->getNoteMessage($application),
                ),
            )
        );

        // Get client context and application required to view and synchronise files
        $applicationClient = $this->getIrisAgentContext()->getReferencingApplicationClient();

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());
            $this->getFormFlow()->setForm($form);

            if ($form->get('back')->isClicked()) {
                $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getBackUrl());
            }

            if ($form->isValid()) {

                // Handle note persistence
                $this->getNoteHandler()->handleNotePersistence($form->getData(), $application);

                // Synchronise the files in IRIS with the files submitted
                $fileCollection = $form->get('step')->get('uploadFileCollection');

                // Perform file synchronisation with backend system
                $documentUploader = new DocumentUploader($applicationClient, $application);
                $documentUploader->sync(
                    $fileCollection,
                    /* existing files not yet implemented */ null,
                    function($parameters) {
                        return array('description' => 'Unknown document uploaded by agent via Connect');
                    }
                );

                // TODO: Don't currently have a place to store the additional details text
//                $object = null;
//
//                try {
//                    $object = $this
//                        ->getAgentProgressiveStore()
//                        ->store($form->getData())
//                    ;
//                }
//                catch (ValidationException $e) {
//                    $this->getFormValidationErrorBinder()->bind($form, $e);
//                }

                if ($form->get('step')->get('attachFile')->isClicked()) {
                    // Reset form to force re-initialisation of the uploaded file collection
                    // As the form has already been submitted, we have to recreated it anew
                    $form = $this->getFormFactory()->create(new RootStepType(new AdditionalDetailsType()));
                }

                // TODO: Don't currently have a place to store the additional details text
//                else if ($object) {
                else {
                    if ($form->get('next')->isClicked()) {
                        $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getNextUrl());
                    }
                }
            }
        }

        if (!$form->isSubmitted()) {
            // Populate list of already uploaded files
            $upstreamDocuments = $applicationClient->getDocuments(array(
                'referencingApplicationUuId' => $application->getReferencingApplicationUuId(),
            ));

            $fileCollection = $form->get('step')->get('uploadedFileCollection');
            $fileCollectionSize = count($fileCollection);

            foreach ($upstreamDocuments as $upstreamDocument) {
                $fileCollection->add($fileCollectionSize++, new UploadedFileCollectionItemType(), array(
                    'data' => array('fileItem' => $upstreamDocument->getName()),
                    'label' => false,
                ));
            }
        }

        $this->renderTwigView('/iris-add-tenant/additional-details-step.html.twig', array(
            'bodyTitle' => 'Additional Details',
            'form' => $form->createView(),
        ));
    }

    /*
     * Reference summary
     *
     * @return void
     */
    public function summaryAction()
    {
        $form = $this->getFormFactory()->create(new RootStepType(new SummaryType()));

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            $this->getFormFlow()->setForm($form);

            if ($form->get('back')->isClicked()) {
                $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getBackUrl());
            }

            if ($form->isValid()) {

                $object = null;

                try {
                    $object = $this
                        ->getAgentProgressiveStore()
                        ->store($form->getData())
                    ;
                }
                catch (ValidationException $e) {
                    $this->getFormValidationErrorBinder()->bind($form, $e);
                }

                if ($object) {
                    if ($form->get('next')->isClicked()) {
                        $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getNextUrl());
                    }
                }
            }
        }

        $case = $this->getAgentProgressiveStore()->getPrototypeByClass(self::MODEL_CASE_CLASS);
        $application = $this->getAgentProgressiveStore()->getPrototypeByClass(self::MODEL_APPLICATION_CLASS);

        $this->renderTwigView('/iris-add-tenant/summary-step.html.twig', array(
            'bodyTitle' => 'Summary',
            'form' => $form->createView(),
            'application' => $application,
            'case' => $case,
        ));
    }

    /**
     * Terms and conditions
     *
     * @return void
     */
    public function termsAndConditionsAction()
    {
        $form = $this->getFormFactory()->create(
            new RootStepType(new TermsAndConditionsType()),
            null
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            $this->getFormFlow()->setForm($form);

            if ($form->get('back')->isClicked()) {
                $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getBackUrl());
            }

            if ($form->isValid()) {

                $object = null;

                try {
                    $object = $this
                        ->getAgentProgressiveStore()
                        ->store($form->getData())
                    ;
                }
                catch (ValidationException $e) {
                    $this->getFormValidationErrorBinder()->bind($form, $e);
                }

                /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
                $application = $this
                    ->getAgentProgressiveStore()
                    ->getPrototypeByClass(self::MODEL_APPLICATION_CLASS)
                ;

                /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase $case */
                $case = $this
                    ->getAgentProgressiveStore()
                    ->getPrototypeByClass(self::MODEL_CASE_CLASS)
                ;

                // If we have the correct number of applicants, submit the application
                $submitSuccess = $this
                    ->getContainer()
                    ->get('iris.referencing.application.submission.application_submitter')
                    ->submit($application->getReferencingApplicationUuId(), '/iris-referencing/submission-error?errorCode={error_code}')
                ;

                // Something went wrong
                if (!$submitSuccess) {
                    $this->_helper->redirector->gotoUrlAndExit(
                        sprintf('/iris-referencing/failed-to-submit-application?uuid=%s', $application->getReferencingApplicationUuId())
                    );
                }

                // Do we need to collect another tenant?
                $difference = $this
                    ->getContainer()
                    ->get('iris.referencing.application.application_counter')
                    ->getApplicantCountDifference($application->getReferencingApplicationUuId(), $case->getNumberOfTenants())
                ;

                // If the difference is greater than zero, then we need to collect more tenants
                if ($difference > 0) {

                    // Clear and add existing prototypes
                    $this
                        ->getAgentProgressiveStore()
                        ->clearPrototypes()
                        ->initialisePrototypes()
                        ->storePrototypes()
                    ;

                    // Forward to add tenant controller
                    $this->_helper->redirector->gotoUrlAndExit(
                        sprintf(
                            '/iris-add-tenant?uuid=%s&difference=%d&numberOfTenants=%d',
                            $case->getReferencingCaseUuId(),
                            $difference,
                            $case->getNumberOfTenants()
                        )
                    );
                }

                if ($object) {
                    if ($form->get('next')->isClicked()) {
                        $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getNextUrl());
                    }
                }
            }
        }

        $declarationOwnership = new DeclarationOwnership();
        $canDisplayDeclaration = $declarationOwnership->canDisplayDeclaration($this->_agentSchemeNumber); 
        $this->renderTwigView('/iris-add-tenant/generic-step.html.twig', array(
            'bodyTitle' => 'Terms and Conditions',
            'formTheme' => 'terms-and-conditions-widgets.html.twig',
            'form' => $form->createView(),
            'canDisplayDeclaration' => $canDisplayDeclaration, 
        ));
    }

    /**
     * Submit action
     *
     * @return void
     */
    public function submitAction()
    {
        $application = $this
            ->getAgentProgressiveStore()
            ->getPrototypeByClass(self::MODEL_APPLICATION_CLASS)
        ;

        // Clear and add existing prototypes
        $this
            ->getAgentProgressiveStore()
            ->clearPrototypes()
            ->initialisePrototypes()
            ->storePrototypes()
        ;

        $this->renderTwigView('/iris-add-tenant/submit-step.html.twig', array(
            'bodyTitle' => 'Complete',
            'application' => $application,
        ));
    }

    /**
     * Get progressive store for agents
     *
     * @return \Iris\Referencing\FormSet\ProgressiveStore\AgentProgressiveStore
     */
    protected function getAgentProgressiveStore()
    {
        return $this->getContainer()->get('iris.referencing.form_set.progressive_store.agent_progressive_store');
    }

    /**
     * Get form flow
     *
     * @return \Iris\FormFlow\AbstractFormFlow
     */
    protected function getFormFlow()
    {
        return $this->getContainer()->get('iris.referencing.form_flow.connect_add_tenant_form_flow');
    }
}
