<?php

require_once __DIR__ . '/IrisContinueReferenceAbstractController.php';

use Barbondev\IRISSDK\Common\Exception\ValidationException;
use Iris\IndividualApplication\DocumentUploader;
use Iris\Referencing\Form\Type\RootStepType;
use Iris\Referencing\Form\Type\UploadedFileCollectionItemType;
use Iris\Referencing\FormSet\Form\Type\AdditionalDetailsType;
use Iris\Referencing\FormSet\Form\Type\AddressHistoryType;
use Iris\Referencing\FormSet\Form\Type\FinancialRefereesType;
use Iris\Referencing\FormSet\Form\Type\LettingRefereeType;
use Iris\Referencing\FormSet\Form\Type\SummaryType;
use Iris\Referencing\FormSet\Form\Type\TenantDetailsType;
use Iris\Referencing\FormSet\Form\Type\TermsAndConditionsType;
use Iris\Referencing\FormSet\Model\LinkRefHolder;
use Iris\ProgressiveStore\Exception\PrototypeNotFoundException;
use Iris\Utility\DeclarationOwnership\DeclarationOwnership;

/**
 * Class IrisContinueReference_IndexController
 *
 * @author Simon Paulger <simon.paulger@barbon.com>
 */
class IrisContinueReference_IndexController extends IrisContinueReferenceAbstractController
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

        // If this is index, initialise everything before form flow is run
        if ('index' == $this->getRequest()->getActionName()) {

            if ($this->linkRef) {

                $application = $this
                    ->getIrisSystemContext()
                    ->getSystemApplicationClient()
                    ->getReferencingApplication(array(
                        'linkRef' => $this->linkRef,
                    ))
                ;

                $case = $this
                    ->getIrisSystemContext()
                    ->getSystemApplicationClient()
                    ->getReferencingCase(array(
                        'referencingCaseUuId' => $application->getReferencingCaseUuId()
                    ))
                ;

                // Convert datetime type
                $case
                    ->setTenancyStartDate(new \DateTime($case->getTenancyStartDate()))
                ;

                // Convert datetime type
                $application
                    ->setBirthDate(new \DateTime($application->getBirthDate()))
                    ->setCreatedAt(new \DateTime($application->getCreatedAt()))
                ;

                $canEmploymentChangeDuringTenancy = false;
                $hasMultipleJobOrPension = false;

                /** @var \Barbondev\IRISSDK\Common\Model\FinancialReferee $financialReferee */
                foreach ($application->getFinancialReferees() as $financialReferee) {

                    $financialReferee->setEmploymentStartDate(new \DateTime($financialReferee->getEmploymentStartDate()));

                    if (2 == $financialReferee->getFinancialRefereeStatus()) {
                        $hasMultipleJobOrPension = true;
                    }

                    if (3 == $financialReferee->getFinancialRefereeStatus()) {
                        $canEmploymentChangeDuringTenancy = true;
                    }
                }

                /** @var \Barbondev\IRISSDK\Common\Model\FinancialReferee $financialReferee */
                foreach ($application->getFinancialReferees() as $financialReferee) {

                    if (1 == $financialReferee->getFinancialRefereeStatus()) {

                        $financialReferee
                            ->setEmploymentChangeDuringTenancy($canEmploymentChangeDuringTenancy)
                            ->setMultipleJobOrPension($hasMultipleJobOrPension)
                        ;
                    }
                }

                $this
                    ->getSystemProgressiveStore()
                    ->clearPrototypes()
                    ->addPrototype($case)
                    ->addPrototype($application)
                    ->addPrototype(new LinkRefHolder($this->linkRef))
                    ->storePrototypes()
                ;
            }
        }

        $this->getFormFlow()->run();
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $form = $this->getFormFactory()->create(
            new RootStepType(new TenantDetailsType()),
            $this->getSystemProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS),
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
                        ->getSystemProgressiveStore()
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

        $this->renderTwigView('/iris-continue-reference/generic-step.html.twig', array(
            'bodyTitle' => 'Your Details',
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
            $this->getSystemProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS)
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
                        ->getSystemProgressiveStore()
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

        $this->renderTwigView('/iris-continue-reference/generic-step.html.twig', array(
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
            $this->getSystemProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS)
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
                            ->getSystemProgressiveStore()
                            ->store($form->getData());
                    } catch (ValidationException $e) {
                        // todo: had to disable for now due to update (until Simon looks at it)
                        // $this->getFormValidationErrorBinder()->bind($form, $e);
                    }

                    if ($object) {
                        $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getNextUrl());
                    }
                }
            }
        }

        $this->renderTwigView('/iris-continue-reference/generic-step.html.twig', array(
            'bodyTitle' => 'Financial Referee',
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
            $this->getSystemProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS),
            array(
                'stepTypeOptions' => array(
                    'is_agent_context' => false,
                ),
            )
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
                        ->getSystemProgressiveStore()
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

        $this->renderTwigView('/iris-continue-reference/generic-step.html.twig', array(
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
        return $this->getContainer()->get('iris.additional_information_note_handler.system');
    }

    /**
     * Additional details action
     *
     * @return void
     */
    public function additionalDetailsAction()
    {
        $application = $this->getSystemProgressiveStore()->getPrototypeByClass(self::MODEL_APPLICATION_CLASS);

        $form = $this->getFormFactory()->create(
            new RootStepType(new AdditionalDetailsType()),
            array(
                'step' => array(
                    'additionalInfo' => $this->getNoteHandler()->getNoteMessage($application),
                ),
            )
        );

        // Get client context and application required to view and synchronise files
        $applicationClient = $this->getIrisSystemContext()->getSystemApplicationClient();

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

        $this->renderTwigView('/iris-continue-reference/additional-details-step.html.twig', array(
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
                        ->getSystemProgressiveStore()
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

        $case = $this->getSystemProgressiveStore()->getPrototypeByClass(self::MODEL_CASE_CLASS);
        $application = $this->getSystemProgressiveStore()->getPrototypeByClass(self::MODEL_APPLICATION_CLASS);

        $this->renderTwigView('/iris-continue-reference/summary-step.html.twig', array(
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
            $this->getSystemProgressiveStore()->fetch(self::MODEL_APPLICATION_CLASS)
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
                    /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $object */
                    $object = $this
                        ->getSystemProgressiveStore()
                        ->store($form->getData())
                    ;
                }
                catch (ValidationException $e) {
                    $this->getFormValidationErrorBinder()->bind($form, $e);
                }

                if ($object) {

                    // Perform submission
                    $this
                        ->getIrisSystemContext()
                        ->getSystemApplicationClient()
                        ->submitApplication(array(
                            'referencingApplicationUuId' => $object->getReferencingApplicationUuId(),
                        ))
                    ;

                    if ($form->get('next')->isClicked()) {
                        $this->_helper->redirector->gotoUrlAndExit($this->getFormFlow()->getNextUrl());
                    }
                }
            }
        }
        
        $sysAppclientContext = $this->getIrisSystemContext()->getSystemApplicationClient();
        $declarationOwnership = new DeclarationOwnership();
        $agentSchemeNumber = $declarationOwnership->getAgentSchemeNumberByLinkRef($sysAppclientContext, $this->authSession->linkRef);
        $canDisplayDeclaration = $declarationOwnership->canDisplayDeclaration($agentSchemeNumber); 
        $this->renderTwigView('/iris-continue-reference/generic-step.html.twig', array(
            'bodyTitle' => 'Terms and Conditions',
            'formTheme' => 'form/continue-terms-and-conditions-widgets.html.twig',
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
        // Get application from session before it's destroyed
        try {
            $application = $this->getSystemProgressiveStore()->getPrototypeByClass(self::MODEL_APPLICATION_CLASS);
        }
        catch (PrototypeNotFoundException $e) {
            exit('Your application has already been submitted');
        }

        // Process completed, flush session store
        $this
            ->getSystemProgressiveStore()
            ->clearPrototypes()
            ->storePrototypes()
        ;

        // Display success page
        $this->renderTwigView('/iris-continue-reference/submit.html.twig', array(
            'bodyTitle' => 'Thank You',
            'application' => $application,
        ));
    }

    /**
     * Find addresses
     *
     * @return void
     */
    public function findAddressesAction()
    {
        // Turn off layout and renderer
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();

        /** @var \Iris\Utility\AddressFinder\AddressFinder $addressFinder */
        $addressFinder = $this->getContainer()->get('iris.address_finder');

        $postcode = $this->getSymfonyRequest()->query->get('postcode');
        $addresses = $addressFinder->find($postcode);

        $addressesJson = array();

        /** @var \Iris\Utility\AddressFinder\Model\Address $address */
        foreach ($addresses as $address) {
            $addressesJson[] = $address->toArray();
        }

        $this
            ->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Cache-Control', 'no-cache, must-revalidate')
            ->setHeader('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT')
        ;

        $this->getResponse()->appendBody(json_encode($addressesJson));
    }

    /**
     * Get progressive store for agents
     *
     * @return \Iris\Referencing\FormSet\ProgressiveStore\SystemProgressiveStore
     */
    protected function getSystemProgressiveStore()
    {
        return $this->getContainer()->get('iris.referencing.form_set.progressive_store.system_progressive_store');
    }

    /**
     * Get form flow
     *
     * @return \Iris\FormFlow\AbstractFormFlow
     */
    protected function getFormFlow()
    {
        return $this->getContainer()->get('iris.referencing.form_flow.applicant_continue_reference_form_flow');
    }
}
