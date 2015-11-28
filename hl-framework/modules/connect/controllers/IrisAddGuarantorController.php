<?php

require_once __DIR__ . '/IrisConnectAbstractController.php';

use Barbondev\IRISSDK\Common\Exception\ValidationException;
use Barbondev\IRISSDK\Common\Model\FinancialReferee;
use Iris\IndividualApplication\DocumentUploader;
use Iris\Referencing\Form\Type\RootStepType;
use Iris\Referencing\Form\Type\UploadedFileCollectionItemType;
use Iris\Referencing\FormSet\Form\Type\AdditionalDetailsType;
use Iris\Referencing\FormSet\Form\Type\AddressHistoryType;
use Iris\Referencing\FormSet\Form\Type\FinancialRefereesType;
use Iris\Referencing\FormSet\Form\Type\GuarantorDetailsType;
use Iris\Referencing\FormSet\Form\Type\ProductType;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Referencing\FormSet\Form\Type\SummaryType;
use Iris\Referencing\FormSet\Form\Type\TermsAndConditionsType;
use Barbondev\IRISSDK\Common\Enumeration\CompletionMethodsOptions;
use Iris\Utility\DeclarationOwnership\DeclarationOwnership;

/**
 * Class Connect_IrisAddGuarantorController
 *
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class Connect_IrisAddGuarantorController extends IrisConnectAbstractController
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
        if ($this->getSymfonyRequest()->query->has('uuid')) {

            $applicationUuId = $this->getSymfonyRequest()->query->get('uuid', null);

            // Get referencing case. Needed for the summary page
            $applicant = $this
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
                    'referencingCaseUuId' => $applicant->getReferencingCaseUuId(),
                ))
            ;

            $referencingApplication = new ReferencingApplication();
            $referencingApplication
                ->setProductId($applicant->getProductId())
                ->setPolicyLength($applicant->getPolicyLength())
            ;

            $this
                ->getAgentGuarantorProgressiveStore()
                ->setApplicantUuId($applicationUuId)
                ->clearPrototypes()
                ->addPrototype($case)
                ->addPrototype($referencingApplication)
                ->storePrototypes()
            ;
        }

        $form = $this->getFormFactory()->create(
            new RootStepType(new ProductType($this->getAgentGuarantorProgressiveStore())),
            $this->getAgentGuarantorProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS),
            array(
                'removeBack' => true,
                'stepTypeOptions' => array(
                    'userLabel' => 'Guarantor',
                ),
            )
        );

        // Don't need productId for guarantors
        $form
            ->get('step')
            ->remove('productId')
            ->remove('update')
        ;

        if ($this->getSymfonyRequest()->isMethod('POST')) {

            $form->submit($this->getSymfonyRequest());

            $this->getFormFlow()->setForm($form);

            if ($form->isValid()) {

                $object = null;

                try {
                    $object = $this
                        ->getAgentGuarantorProgressiveStore()
                        ->store($form->getData())
                    ;
                }
                catch (ValidationException $e) {
                    $this->getFormValidationErrorBinder()->bind($form, $e);
                    $this->renderTwigView('/iris-add-guarantor/generic-step.html.twig', array(
                        'bodyTitle' => 'Guarantor Details',
                        'form' => $form->createView(),
                    ));
                    return;
                }

                // If we're completing by email
                if (CompletionMethodsOptions::COMPLETE_BY_EMAIL == $object->getCompletionMethod()) {

                    /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\Model\ReferencingCase $case */
                    $case = $this
                        ->getAgentGuarantorProgressiveStore()
                        ->getPrototypeByClass(self::MODEL_CASE_CLASS)
                    ;

                    /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $case */
                    $application = $this
                        ->getAgentGuarantorProgressiveStore()
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
                                ->getAgentGuarantorProgressiveStore()
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
                        $this->_helper->redirector->gotoUrlAndExit('/iris-add-guarantor/email-to-guarantor-success');
                    }
                    else {

                        // Something went wrong
                        $this->_helper->redirector->gotoUrlAndExit(
                            sprintf('/iris-referencing/failed-to-submit-application?uuid=%s', $application->getReferencingApplicationUuId())
                        );
                    }
                }

                if ($object) {
                    if ($form->get('next')->isClicked()) {
                        $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getNextUrl());
                    }
                }
            }
        }

        $this->renderTwigView('/iris-add-guarantor/generic-step.html.twig', array(
            'bodyTitle' => 'Guarantor Details',
            'form' => $form->createView(),
        ));
    }

    /**
     * Email to tenant success action
     *
     * @return void
     */
    public function emailToGuarantorSuccessAction()
    {
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $case */
        $application = $this
            ->getAgentGuarantorProgressiveStore()
            ->getPrototypeByClass(self::MODEL_APPLICATION_CLASS)
        ;

        // Clear and add existing prototypes
        $this
            ->getAgentGuarantorProgressiveStore()
            ->clearPrototypes()
            ->initialisePrototypes()
            ->storePrototypes()
        ;

        $this->renderTwigView('/iris-add-guarantor/email-to-guarantor-success.html.twig', array(
            'bodyTitle' => 'Email Sent to Guarantor',
            'application' => $application,
        ));
    }

    /**
     * Guarantor details action
     *
     * @return void
     */
    public function guarantorDetailsAction()
    {
        $form = $this->getFormFactory()->create(
            new RootStepType(new GuarantorDetailsType()),
            $this->getAgentGuarantorProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS)
        );

        // Don't need productId for guarantors
        // todo: check with TBL as this field is required by IRIS for guarantors...
//        $form
//            ->get('step')
//            ->remove('residentialStatus')
//        ;

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
                        ->getAgentGuarantorProgressiveStore()
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

        $this->renderTwigView('/iris-add-guarantor/generic-step.html.twig', array(
            'bodyTitle' => 'Guarantor Details',
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
            $this->getAgentGuarantorProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS)
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
                        ->getAgentGuarantorProgressiveStore()
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

        $this->renderTwigView('/iris-add-guarantor/generic-step.html.twig', array(
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
            $this->getAgentGuarantorProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS)
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
                            ->getAgentGuarantorProgressiveStore()
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

        $this->renderTwigView('/iris-add-guarantor/generic-step.html.twig', array(
            'bodyTitle' => 'Financial Referee',
            'form' => $form->createView(),
        ));
    }

    /**
     * Landlord action - dummy action only present for form flow resolution.
     *
     * @return void
     */
    public function landlordAction()
    {
    }

    /**
     * Letting referee action - dummy action only present for form flow resolution.
     *
     * @return void
     */
    public function lettingRefereeAction()
    {
    }

    /**
     * Get note handler
     *
     * @return \Iris\IndividualApplication\AdditionalInformationNote\NoteHandler
     */
    private function getNoteHandler()
    {
        return $this->getContainer()->get('iris.additional_information_note_handler.agent_guarantor');
    }

    /**
     * Additional details
     *
     * @return void
     */
    public function additionalDetailsAction()
    {
        $application = $this->getAgentGuarantorProgressiveStore()->getPrototypeByClass(self::MODEL_APPLICATION_CLASS);

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

        $this->renderTwigView('/iris-add-guarantor/additional-details-step.html.twig', array(
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
                        ->getAgentGuarantorProgressiveStore()
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

        $case = $this->getAgentGuarantorProgressiveStore()->getPrototypeByClass(self::MODEL_CASE_CLASS);
        $application = $this->getAgentGuarantorProgressiveStore()->getPrototypeByClass(self::MODEL_APPLICATION_CLASS);

        $this->renderTwigView('/iris-add-guarantor/summary-step.html.twig', array(
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
            $this->getAgentGuarantorProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS)
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
                        ->getAgentGuarantorProgressiveStore()
                        ->store($form->getData())
                    ;
                }
                catch (ValidationException $e) {
                    $this->getFormValidationErrorBinder()->bind($form, $e);
                }

                /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
                $application = $this
                    ->getAgentGuarantorProgressiveStore()
                    ->getPrototypeByClass(self::MODEL_APPLICATION_CLASS)
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

                if ($object) {
                    if ($form->get('next')->isClicked()) {
                        $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getNextUrl());
                    }
                }
            }
        }
       
        $declarationOwnership = new DeclarationOwnership();
        $canDisplayDeclaration = $declarationOwnership->canDisplayDeclaration($this->_agentSchemeNumber); 
        $this->renderTwigView('/iris-add-guarantor/generic-step.html.twig', array(
            'bodyTitle' => 'Terms and Conditions',
            'formTheme' => 'terms-and-conditions-guarantor-widgets.html.twig',
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
        $application = $this
            ->getAgentGuarantorProgressiveStore()
            ->getPrototypeByClass(self::MODEL_APPLICATION_CLASS)
        ;

        // Clear and add existing prototypes
        $this
            ->getAgentGuarantorProgressiveStore()
            ->clearPrototypes()
            ->initialisePrototypes()
            ->storePrototypes()
        ;

        $this->renderTwigView('/iris-add-guarantor/submit-step.html.twig', array(
            'bodyTitle' => 'Complete',
            'application' => $application,
        ));
    }

    /**
     * Continue guarantor application action
     *
     * @throws Zend_Exception
     * @return void
     */
    public function guarantorContinueApplicationAction()
    {
        $applicationUuId = $this->getSymfonyRequest()->query->get('uuid', null);

        if (null !== $applicationUuId) {

            // If we're not coming from self::agentGuarantorEmailContinueReferenceAction() then reset flag
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
                ->getAgentGuarantorProgressiveStore()
                ->clearPrototypes()
                ->addPrototype($case)
                ->addPrototype($application)
                ->storePrototypes()
            ;

            // Do we have a form location defined in URL?
            $formUrl = '/iris-add-guarantor?continue=1';
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
    public function agentGuarantorEmailContinueReferenceAction()
    {
        $applicationUuId = $this->getSymfonyRequest()->query->get('uuid', null);

        if (null !== $applicationUuId) {

            $this->setIsAgentReturningFromNotification($applicationUuId, true);

            $url = sprintf(
                '/iris-add-guarantor/guarantor-continue-application?uuid=%s&isFromNotification=1&formUrl=/iris-add-guarantor/summary',
                $applicationUuId
            );

            $this->_helper->redirector->gotoUrlAndExit($url);
        }

        throw new \Zend_Exception('Uuid was not passed to continue reference');
    }

    /**
     * Get progressive store for agents
     *
     * @return \Iris\Referencing\FormSet\ProgressiveStore\AgentGuarantorProgressiveStore
     */
    protected function getAgentGuarantorProgressiveStore()
    {
        return $this->getContainer()->get('iris.referencing.form_set.progressive_store.agent_guarantor_progressive_store');
    }

    /**
     * Get form flow
     *
     * @return \Iris\FormFlow\AbstractFormFlow
     */
    protected function getFormFlow()
    {
        return $this->getContainer()->get('iris.referencing.form_flow.connect_add_guarantor_form_flow');
    }
}
