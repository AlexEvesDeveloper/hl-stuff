<?php

require_once __DIR__ . '/IrisConnectAbstractController.php';

use Barbondev\IRISSDK\Common\Model\FinancialReferee;
use Iris\IndividualApplication\DocumentUploader;
use Iris\Referencing\Form\Type\RootStepType;
use Iris\Referencing\Form\Type\UploadedFileCollectionItemType;
use Iris\Referencing\FormSet\Form\Type\LettingRefereeType;
use Iris\Referencing\FormSet\Form\Type\PropertyType;
use Iris\Referencing\FormSet\Form\Type\ProductType;
use Iris\Referencing\FormSet\Form\Type\TenantDetailsType;
use Iris\Referencing\FormSet\Form\Type\AddressHistoryType;
use Iris\Referencing\FormSet\Form\Type\ProspectiveLandlordType;
use Iris\Referencing\FormSet\Form\Type\FinancialRefereesType;
use Iris\Referencing\FormSet\Form\Type\TermsAndConditionsType;
use \Iris\Referencing\FormSet\Form\Type\AdditionalDetailsType;
use \Iris\Referencing\FormSet\Form\Type\SummaryType;
use Guzzle\Service\Exception\ValidationException;
use Barbondev\IRISSDK\Common\Enumeration\CompletionMethodsOptions;
use Barbondev\IRISSDK\Common\Exception\DefaultException;
use Iris\Utility\DeclarationOwnership\DeclarationOwnership;

/**
 * Class Connect_IrisNewReferenceController
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Connect_IrisNewReferenceController extends IrisConnectAbstractController
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

        // If this agent cannot perform referencing then kick them to an error action
        if (true !== $this->_canPerformReferencing) {
            $this->_helper->redirector->gotoUrlAndExit('/iris-referencing/cannot-perform-referencing-error');
        }

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
        // If we're not continuing, flush the session
        if (!$this->getSymfonyRequest()->query->has('continue')) {
            $this
                ->getAgentProgressiveStore()
                ->clearPrototypes()
                ->initialisePrototypes()
                ->storePrototypes()
            ;
        }

        $form = $this->getFormFactory()->create(
            new RootStepType(new PropertyType()),
            $this->getAgentProgressiveStore()->fetch(self::MODEL_CASE_CLASS),
            array(
                'removeBack' => true
            )
        );

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            $this->getFormFlow()->setForm($form);

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

        $this->renderTwigView('/iris-new-reference/generic-step.html.twig', array(
            'bodyTitle' => 'Address of property to let',
            'form' => $form->createView(),
        ));
    }

    /**
     * Product action
     *
     * @return void
     */
    public function productAction()
    {
        $form = $this->getFormFactory()->create(
            new RootStepType(new ProductType()),
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
                            ->store($form->getData())
                        ;
                    }
                    catch (ValidationException $e) {
                        $this->getFormValidationErrorBinder()->bind($form, $e);
                        $this->renderTwigView('/iris-new-reference/generic-step.html.twig', array(
                            'bodyTitle' => 'Product selection',
                            'form' => $form->createView(),
                        ));
                        return;
                    }

                    /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
                    $application = $this->getAgentProgressiveStore()->getPrototypeByClass(self::MODEL_APPLICATION_CLASS);

                    // If we're completing by email and it's not a rent guarantee product OR
                    // if completion by email and the agent can't complete the prospective landlord form
                    if ((CompletionMethodsOptions::COMPLETE_BY_EMAIL == $object->getCompletionMethod() &&
                        !$application->getProduct()->getHasRentGuarantee()) ||
                        (CompletionMethodsOptions::COMPLETE_BY_EMAIL == $object->getCompletionMethod() &&
                        !$this->canAgentCompleteProspectiveLandlord())) {

                        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase $case */
                        $case = $this
                            ->getAgentProgressiveStore()
                            ->getPrototypeByClass(self::MODEL_CASE_CLASS)
                        ;

                        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
                        $application = $this
                            ->getAgentProgressiveStore()
                            ->getPrototypeByClass(self::MODEL_APPLICATION_CLASS)
                        ;

                        // Submit case
                        $submitSuccess = $this
                            ->getContainer()
                            ->get('iris.referencing.application.submission.case_submitter')
                            ->submit($case->getReferencingCaseUuId(), '/iris-referencing/submission-error?errorCode={error_code}')
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
                            $this->_helper->redirector->gotoUrlAndExit('/iris-new-reference/email-to-tenant-success');
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

        $this->renderTwigView('/iris-new-reference/generic-step.html.twig', array(
            'bodyTitle' => 'Product selection',
            'form' => $form->createView(),
        ));
    }

    /**
     * Email to tenant success action
     *
     * @return void
     */
    public function emailToTenantSuccessAction()
    {
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

        $this->renderTwigView('/iris-new-reference/email-to-tenant-success.html.twig', array(
            'bodyTitle' => 'Email Sent to Tenant',
            'application' => $application,
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

        $this->renderTwigView('/iris-new-reference/generic-step.html.twig', array(
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

        $this->renderTwigView('/iris-new-reference/generic-step.html.twig', array(
            'bodyTitle' => 'Address History',
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

        $this->renderTwigView('/iris-new-reference/generic-step.html.twig', array(
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

                    // Submit case
                    $submitSuccess = $this
                        ->getContainer()
                        ->get('iris.referencing.application.submission.case_submitter')
                        ->submit($case->getReferencingCaseUuId(), '/iris-referencing/submission-error?errorCode={error_code}')
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
                        $this->_helper->redirector->gotoUrlAndExit('/iris-new-reference/email-to-tenant-success');
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

        $this->renderTwigView('/iris-new-reference/generic-step.html.twig', array(
            'bodyTitle' => 'Current Estate Agent/Landlord/Management Details',
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
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
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
                        return array(
                            'description' => 'Unknown document uploaded by agent via Connect',
                        );
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

        $this->renderTwigView('/iris-new-reference/additional-details-step.html.twig', array(
            'bodyTitle' => 'Additional Details',
            'form' => $form->createView(),
        ));
    }

    /**
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

        $this->renderTwigView('/iris-new-reference/summary-step.html.twig', array(
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
            $this->getAgentProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS)
        );

        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $applicationFromFormData */
        $applicationFromFormData = $this->getReferencingModelFromFormData($form->getData());
        $isAgentReturningFromNotification = false;
        if ($applicationFromFormData) {
            $isAgentReturningFromNotification = $this->getIsAgentReturningFromNotification(
                $applicationFromFormData->getReferencingApplicationUuId()
            );
        }

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

                $submitSuccess = false;
                $isSubmittingApplicationOnly = false;

                // If we have the correct number of applicants, submit the case
                try {

                    // If awaiting agent review then submit application
                    if (7 == $application->getStatus()) {

                        $submitSuccess = $this
                            ->getContainer()
                            ->get('iris.referencing.application.submission.application_submitter')
                            ->submit($application->getReferencingApplicationUuId())
                        ;

                        $isSubmittingApplicationOnly = true;
                    }
                    else {

                        // Submit case
                        $submitSuccess = $this
                            ->getContainer()
                            ->get('iris.referencing.application.submission.case_submitter')
                            ->submit($case->getReferencingCaseUuId())
                        ;
                    }

                }
                catch (DefaultException $e) {

                    // If we're unable to submit the case, try submitting the application (i.e. continuation of a second tenant)
                    // 1012 is the "Referencing case is already submitted" error from IRIS
                    if (1012 == $e->getCode()) {

                        $submitSuccess = $this
                            ->getContainer()
                            ->get('iris.referencing.application.submission.application_submitter')
                            ->submit($application->getReferencingApplicationUuId(), '/iris-referencing/submission-error?errorCode={error_code}')
                        ;

                        $isSubmittingApplicationOnly = true;
                    }
                }

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
                if ($difference > 0 && !$isSubmittingApplicationOnly) {

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
        $this->renderTwigView('/iris-new-reference/generic-step.html.twig', array(
            'bodyTitle' => 'Terms and Conditions',
            'formTheme' => 'terms-and-conditions-widgets.html.twig',
            'isAgentReturningFromNotification' => $isAgentReturningFromNotification,
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
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
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

        $this->renderTwigView('/iris-new-reference/submit-step.html.twig', array(
            'bodyTitle' => 'Complete',
            'application' => $application,
        ));
    }

    /**
     * Continue application as tenant
     *
     * @throws Zend_Exception
     * @return void
     */
    public function tenantContinueApplicationAction()
    {
        $applicationUuId = $this->getSymfonyRequest()->query->get('uuid', null);

        if (null !== $applicationUuId) {

            // If we're not coming from self::agentTenantEmailContinueReferenceAction() then reset flag
            if (!$this->getSymfonyRequest()->query->get('isFromNotification', false)) {
                $this->setIsAgentReturningFromNotification($applicationUuId, false);
            }

            // Init session with hydrated prototypes
            $application = $this
                ->getIrisAgentContext()
                ->getReferencingApplicationClient()
                ->getReferencingApplication(array(
                    'referencingApplicationUuId' => $applicationUuId,
                ))
            ;

            $case = $this
                ->getIrisAgentContext()
                ->getReferencingCaseClient()
                ->getReferencingCase(array(
                    'referencingCaseUuId' => $application->getReferencingCaseUuId(),
                ))
            ;

            if (get_class($application) != self::MODEL_APPLICATION_CLASS) {
                throw new \Zend_Exception(
                    sprintf('Could not locate application with uuid %s', $applicationUuId)
                );
            }

            if (get_class($case) != self::MODEL_CASE_CLASS) {
                throw new \Zend_Exception(
                    sprintf('Could not locate case with uuid %s', $application->getReferencingCaseUuId())
                );
            }

            // Modify dates to become \DateTime objects
            if ($application->getBirthDate()) {
                $application->setBirthDate(new \DateTime($application->getBirthDate()));
            }

            if ($application->getFirstCompletionAt()) {
                $application->setFirstCompletionAt(new \DateTime($application->getFirstCompletionAt()));
            }

            foreach ($application->getAddressHistories() as $previousAddress) {
                $previousAddress
                    ->setStartedAt(new \DateTime($previousAddress->getStartedAt()))
                ;
            }

            $canEmploymentChangeDuringTenancy = false;
            $hasMultipleJobOrPension = false;

            /** @var FinancialReferee $financialReferee */
            foreach ($application->getFinancialReferees() as $financialReferee) {

                $financialReferee
                    ->setEmploymentStartDate(new \DateTime($financialReferee->getEmploymentStartDate()))
                ;

                if (2 == $financialReferee->getFinancialRefereeStatus()) {
                    $hasMultipleJobOrPension = true;
                }

                if (3 == $financialReferee->getFinancialRefereeStatus()) {
                    $canEmploymentChangeDuringTenancy = true;
                }
            }

            /** @var FinancialReferee $financialReferee */
            foreach ($application->getFinancialReferees() as $financialReferee) {

                if (1 == $financialReferee->getFinancialRefereeStatus()) {

                    $financialReferee
                        ->setEmploymentChangeDuringTenancy($canEmploymentChangeDuringTenancy)
                        ->setMultipleJobOrPension($hasMultipleJobOrPension)
                    ;
                }
            }

            // Modify dates to become \DateTime objects
            $case
                ->setTenancyStartDate(new \DateTime($case->getTenancyStartDate()))
            ;

            // Clear and add existing prototypes
            $this
                ->getAgentProgressiveStore()
                ->clearPrototypes()
                ->addPrototype($case)
                ->addPrototype($application)
                ->storePrototypes()
            ;

            // Do we have a form location defined in URL?
            $formUrl = '/iris-new-reference?continue=1';
            if ($this->getSymfonyRequest()->query->has('formUrl')) {
                $formUrl = $this->getSymfonyRequest()->query->get('formUrl');
            }

            // Redirect out to first step
            // Pass continue=1 for first step so session is not lost
            $this->_helper->redirector->gotoUrlAndExit($formUrl);

            return;
        }

        throw new \Zend_Exception('Uuid was not passed to continue reference');
    }

    /**
     * When an agent sends an email to tenant and they complete and submit, a
     * notification is sent to the agent instructing them to continue the reference.
     * When the agent clicks on the link in the notification, they will be taken
     * to this action
     *
     * @throws Zend_Exception
     * @return void
     */
    public function agentTenantEmailContinueReferenceAction()
    {
        $applicationUuId = $this->getSymfonyRequest()->query->get('uuid', null);

        if (null !== $applicationUuId) {

            $this->setIsAgentReturningFromNotification($applicationUuId, true);

            $url = sprintf(
                '/iris-new-reference/tenant-continue-application?uuid=%s&isFromNotification=1&formUrl=/iris-new-reference/summary',
                $applicationUuId
            );

            $this->_helper->redirector->gotoUrlAndExit($url);
        }

        throw new \Zend_Exception('Uuid was not passed to continue reference');
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
        return $this->getContainer()->get('iris.referencing.form_flow.connect_new_reference_form_flow');
    }
}
