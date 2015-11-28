<?php

use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use RRP\Criteria\SpecificationGroups\PolicyCriteria;
use RRP\Event\ReferredEvent;
use RRP\Event\RRPEvents;
use RRP\EventListener\DispatchReferralEmailListener;
use RRP\EventListener\LogReferralReasonsListener;
use RRP\Model\RentRecoveryPlusApplication;
use RRP\Model\RentRecoveryPlusReference;
use Symfony\Component\EventDispatcher\EventDispatcher;

require_once('RRPConnectAbstractController.php');

/**
 * Class Connect_RentguaranteeController
 *
 * @author April Portus <april.portus@barbon.com>
 * @author Alex Eves <alex.eves@barbon.com>
 */
class Connect_RentguaranteeController extends RRPConnectAbstractController
{
    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var RRP\Rate\RateDecorators\RentRecoveryPlus
     */
    protected $rateManager;

    /**
     * @var \RRP\Referral\RentRecoveryPlusReferral
     */
    protected $referralModel;

    /**
     * @var array
     */
    protected $referenceBasedReferralReasons = array();

    /**
     * @var array
     */
    protected $applicationBasedReferralReasons = array();

    /**
     * Set up member objects.
     */
    public function init()
    {
        parent::init();

        // We will be firing events from controllers.
        $this->eventDispatcher = new EventDispatcher();
        // So must attach our event listeners.
        $this->attachListeners();

        // Can get this only once, and use throughout the class.
        $this->referralModel = $this->getContainer()->get('rrp.referral');
        $this->referralModel->setAgent($this->_agentObj);
    }

    /**
     * Attach all event listeners to the $eventDispatcher.
     */
    private function attachListeners()
    {
        // Create the listeners.
        $logReferralReasonsListener = new LogReferralReasonsListener(new Datasource_Insurance_PolicyNotes());
        $dispatchReferralEmailListener = new DispatchReferralEmailListener($this->getContainer()->get('twig'), $this->_params, new Application_Core_Mail());

        // The above two listeners are both listening for the RRPEvents::POLICY_REFERRED event to be fired.
        $this->eventDispatcher->addListener(RRPEvents::POLICY_REFERRED, array($logReferralReasonsListener, 'onReferral'));
        $this->eventDispatcher->addListener(RRPEvents::POLICY_REFERRED, array($dispatchReferralEmailListener, 'onReferral'));
    }

    /**
     * Show Rent Guarantee icons in Connect
     * Rent Recovery Plus Insurance icon can only be displayed for certain agents
     *
     * @return $this
     */
    public function indexAction()
    {
        $this->setCanDisplayRRPI();

        return $this;
    }

    /**
     * Show Rent Guarantee icons for the policy wording in Connect
     * Rent Recovery Plus Policy Summary can only be displayed for certain agents
     *
     * @return $this
     */
    public function infoAction()
    {
        $this->setCanDisplayRRPI();

        return $this;
    }

    /**
     * Show Rent Guarantee e-flyer icons on Connect
     * Rent Recovery Plus Insurance e-flyer can only be displayed for certain agents
     *
     * @return $this
     */
    public function productsAction()
    {
        $this->setCanDisplayRRPI();

        return $this;
    }

    /**
     * Sets the view's 'canDisplayRRPI' flag
     *
     * @return $this
     */
    private function setCanDisplayRRPI()
    {
        $agent = new Datasource_Core_AgentUser();
        $canDisplayRRPI = $agent->canDisplayRRPI($this->_agentSchemeNumber, $this->_agentUserName);
        if ($canDisplayRRPI) {
            $this->view->canDisplayRRPI = true;
        }
        return $this;
    }

    /**
     * Absolute/Essential Rent Guarantee action
     *
     * Currently just ensures that that landing page with information about the
     * phasing-out of Absolute is accessible.
     *
     * @return void
     */
    public function absoluteAction()
    {
    }

    /**
     * Action the search
     */
    public function rentRecoveryPlusSearchAction()
    {
        $this->setCanDisplayRRPI();
        $this->renderTwigView('/rentguarantee/rent-recovery-plus-search.html.twig', array(
            'form' => $this->_getRentRecoveryPlusSearchForm()->createView(),
        ));
    }

    /**
     * Get search form
     *
     * @param array|null $data
     * @return \Symfony\Component\Form\FormInterface
     */
    private function _getRentRecoveryPlusSearchForm($data = null)
    {
        $searchType = $this->getContainer()->get('rrp.form.search');
        return $this->getFormFactory()->create($searchType, $data);
    }

    /**
     * Search RRP results
     *
     * @return void
     */
    public function rentRecoveryPlusSearchResultsAction()
    {
        $form = $this->_getRentRecoveryPlusSearchForm();

        $form->handleRequest($this->getSymfonyRequest());

        $pagination = null;

        if ($form->isValid()) {

            $request = $this->getSymfonyRequest();

            /** @var RRP\Model\RentRecoveryPlusSearchCriteria $criteria */
            $criteria = $form->getData();

            /** @var RRP\Search\RentRecoveryPlusSearch $search */
            $search = $this->getContainer()->get('rrp.search');

            $queryString = $criteria->getQueryString($form->getName());

            // Instantiate a new paginator service
            $paginator = $this->getContainer()->get('rrp.paginator');

            // Set some parameters (optional)
            $paginator
                ->setItemsPerPage($criteria->getResultsPerPage())
                ->setPagesInRange(5);

            $currentASN = $this->_agentSchemeNumber;

            // Pass our item total callback
            $paginator->setItemTotalCallback(function () use ($search, $criteria, $currentASN) {
                return $search->search($currentASN, $criteria, 0, 0)->getTotalRecords();
            });

            // Pass our slice callback
            $paginator->setSliceCallback(function ($offset, $length) use ($search, $criteria, $currentASN) {
                return $search->search($currentASN, $criteria, $offset, $length)->getRecords();
            });
            $pagination = $paginator->paginate(
                (int)$this->getSymfonyRequest()->query->get('page', 1)
            );

            $this->renderTwigView('/rentguarantee/rent-recovery-plus-search-results.html.twig', array(
                'form' => $form->createView(),
                'pagination' => $pagination,
                'queryString' => $queryString
            ));
        }
    }

    /**
     * search export
     */
    public function rentRecoveryPlusExportAction()
    {
        $form = $this->_getRentRecoveryPlusSearchForm();
        $request = $this->getSymfonyRequest();
        $data = $request->query->get($form->getName());

        $criteria = $this->getContainer()->get('rrp.model.search_criteria');
        if (array_key_exists('policyNumber', $data)) {
            $criteria->setPolicyNumber($data['policyNumber']);
        }
        if (array_key_exists('landlordName', $data)) {
            $criteria->setLandlordName($data['landlordName']);
        }
        if (array_key_exists('propertyPostcode', $data)) {
            $criteria->setPropertyPostcode($data['propertyPostcode']);
        }
        /** @var RRP\Search\RentRecoveryPlusSearch $search */
        $search = $this->getContainer()->get('rrp.search');

        $this->_sendCsvHeaders('rent-recovery-plus-export.csv');
        $totalCount = $search->search($this->_agentSchemeNumber, $criteria, 0, 0)->getTotalRecords();
        $this->view->reportData = null;
        if ($totalCount > 0) {
            $this->view->reportData = $search->search($this->_agentSchemeNumber, $criteria, 0, $totalCount);
        }
        $this->_helper->getHelper('layout')->disableLayout();
        $this->view->stdOut = fopen('php://output', 'a');
        $this->render('rent-recovery-plus-export-csv');
        fclose($this->view->stdOut);
    }

    /**
     * Private function to send IE-friendly headers for CSV file output over
     * HTTPS.
     *
     * @param string filename Name of CSV file being output.
     */
    private function _sendCsvHeaders($filename)
    {
        header('Pragma: public'); // required
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false); // required for certain browsers
        header("Content-Disposition: attachment; filename={$filename}");
        header('Content-type: text/csv');
    }

    /**
     * Reference summary
     *
     * @return void
     */
    public function rentRecoveryPlusSummaryAction()
    {
        $request = $this->getSymfonyRequest();

        if ($request->isMethod('GET')) {
            $policyNumber = $request->get('policyNumber');

            if ($policyNumber) {

                $searchClient = new Datasource_Insurance_RentRecoveryPlus_Search();
                $policySummaryData = $searchClient->searchForPolicyByNumber(
                    $this->_agentSchemeNumber,
                    $policyNumber
                );
                if ($policySummaryData) {
                    /** @var \RRP\Model\RentRecoveryPlusSummary $summary */
                    $summary = $this->getContainer()->get('rrp.model.summary');
                    $summary = $summary::hydrateFromRow($policySummaryData);

                    if ($summary->getPayStatus() ==
                        Model_Insurance_RentRecoveryPlus_LegacyPolicy::PAY_STATUS_REFERRED
                    ) {
                        $referred = 1;
                    }
                    else {
                        $referred = 0;
                    }
                    $viewDetails = array('summary' => $summary);
                    if (Manager_Core_PolicyNumber::isPolicy($policyNumber)) {
                        $renewalInvitePeriod = $this->_params->connect->settings->rentRecoveryPlus->renewalInvitePeriod;
                        $now = new \DateTime();
                        $mtaEndAt = new \DateTime($summary->getPolicyEndAt());
                        $mtaEndAt = $mtaEndAt->setTime(0, 0, 0)->sub(new \DateInterval($renewalInvitePeriod));
                        if ($now < $mtaEndAt) {
                            $viewDetails['cancelPolicy'] = 1;
                            $viewDetails['amendPolicy'] = 1;
                        }
                    }
                    else if ( ! $referred) {
                        $viewDetails['acceptPolicy'] = 1;
                    }

                    $this->renderTwigView('/rentguarantee/rent-recovery-plus-summary.html.twig', $viewDetails);
                    return;
                }
            }
        }
        $this->renderTwigView('/rentguarantee/rent-recovery-plus-error.html.twig');
    }

    /**
     * RRP Inception Form
     */
    public function rentRecoveryPlusAction()
    {
        // Ensure address lookup JS is available
        $this->view->headScript()->appendFile('/assets/connect/js/rentguaranteeclaims/addressLookup.js');
        // Ensure date picker CSS and JS are available
        $this->view->headLink()->appendStylesheet('/assets/vendor/jquery-datepicker/css/datePicker.css');
        $this->view->headScript()->appendFile('/assets/vendor/jquery-date/js/date.js');
        $this->view->headScript()->appendFile('/assets/vendor/jquery-datepicker/js/jquery.datePicker.js');

        $request = $this->getSymfonyRequest();

        $data = null;
        $policyNumberManager = new Manager_Core_PolicyNumber();

        if ($request->isMethod('GET')) {
            $quoteNumber = $request->get('policyNumber');
            if ($policyNumberManager->isRentRecoveryPlusQuote($quoteNumber)) {
                $applicationDecoratorClass = $this->getContainer()->get('rrp.application.decorator.class');
                /** @var RRP\Application\Decorators\RentRecoveryPlusQuote $quote */
                $quote = $applicationDecoratorClass::getDecorator('RentRecoveryPlusQuote');
                $data = $quote->getApplicationData($quoteNumber);
            }
        }

        $form = $this->_getRentRecoveryPlusApplicationForm($data, array('currentAsn' => $this->_agentSchemeNumber));

        if ($request->isMethod('POST')) {

            $form->submit($request);

            if ($form->isValid()) {

                if ($request->isXmlHttpRequest()) {
                    $update = true;
                }
                else {
                    $update = $form->get('update')->isClicked();
                }

                if ( ! $update) {
                    /** @var RRP\Model\RentRecoveryPlusApplication $application */
                    $application = $form->getData();

                    $applicationDecoratorClass = $this->getContainer()->get('rrp.application.decorator.class');
                    /** @var RRP\Application\Decorators\RentRecoveryPlusQuote $quote */
                    $quote = $applicationDecoratorClass::getDecorator('RentRecoveryPlusQuote');
                    $quote->setFromApplication($application);


                    if ($quote->getAppData()->getRefNo() === null) {

                        $customerManager = new Manager_Core_Customer();
                        $sudoEmailAddress = $customerManager->generateAgentSudoEmailAddress($this->_agentSchemeNumber);
                        $customer = $customerManager->getCustomerByEmailAddress($sudoEmailAddress);
                        $isNewCustomer = false;

                        if ( ! $customer) {
                            $isNewCustomer = true;
                            $customer = $customerManager->createNewCustomer(
                                $sudoEmailAddress,
                                Model_Core_Customer::AGENT,
                                true
                            );
                            $customer->setLastName($this->_agentObj->name);
                            if ($this->_agentObj->contact[0]->address->flatNumber) {
                                $line1 = $this->_agentObj->contact[0]->address->flatNumber . ' ';
                            }
                            else if ($this->_agentObj->contact[0]->address->houseName) {
                                $line1 = $this->_agentObj->contact[0]->address->houseName . ' ';
                            }
                            else if ($this->_agentObj->contact[0]->address->houseNumber) {
                                $line1 = $this->_agentObj->contact[0]->address->houseNumber . ' ';
                            }
                            else {
                                $line1 = '';
                            }
                            if (
                                $this->_agentObj->contact[0]->address->addressLine1 &&
                                $this->_agentObj->contact[0]->address->addressLine2
                            ) {
                                $line1 .=
                                    $this->_agentObj->contact[0]->address->addressLine1 . ', ' .
                                    $this->_agentObj->contact[0]->address->addressLine2;
                            }
                            else if ($this->_agentObj->contact[0]->address->addressLine1) {
                                $line1 .= $this->_agentObj->contact[0]->address->addressLine1;
                            }
                            else if ($this->_agentObj->contact[0]->address->addressLine2) {
                                $line1 .= $this->_agentObj->contact[0]->address->addressLine2;
                            }
                            $customer->setAddressLine(Model_Core_Customer::ADDRESSLINE1, $line1);
                            $customer->setAddressLine(
                                Model_Core_Customer::ADDRESSLINE2,
                                $this->_agentObj->contact[0]->address->town
                            );
                            $customer->setAddressLine(
                                Model_Core_Customer::ADDRESSLINE3,
                                $this->_agentObj->contact[0]->address->county
                            );
                            $customer->setPostCode($this->_agentObj->contact[0]->address->postCode);
                            $customer->setCountry($this->_agentObj->contact[0]->address->country);
                            $customerManager->updateCustomer($customer);
                        }

                        // Now get the reference number from the newly created customer
                        $refNo = $customer->getIdentifier(Model_Core_Customer::LEGACY_IDENTIFIER);
                        $quote->getAppData()->setRefNo($refNo);

                        if ($isNewCustomer) {
                            $customerManager->updateCustomerAgentSchemeNumber($this->_agentSchemeNumber, $refNo);
                        }

                        if ($application->getPolicyNumber()) {
                            $policyNumber = $application->getPolicyNumber();
                        }
                        else {
                            $policyNumber = $policyNumberManager->generateApplicationNumber(
                                Manager_Core_PolicyNumber::QUOTE_IDENTIFIER
                            );
                        }

                        $quote
                            ->setDefaults(
                                $policyNumber,
                                $application->getReferenceType(),
                                $application->getIsContinuationOfExistingPolicy(),
                                $application->getPropertyLetType(),
                                $application->getPropertyDeposit(),
                                Model_Insurance_RentRecoveryPlus_LegacyPolicy::STATUS_QUOTE
                            )
                            ->getAppData()
                                ->setAgentSchemeNumber($this->_agentSchemeNumber)
                                ->setUnderwritingQuestionSetID(
                                    $this->_params->connect->settings->rentRecoveryPlus->underwritingQuestionSetID
                                )
                                ->setRiskArea($this->_params->connect->settings->rentRecoveryPlus->riskArea);
                    }
                    else {
                        $policyNumber = $quote->getAppData()->getPolicyNumber();
                    }

                    $this->referralModel->setPolicyNumber($policyNumber);
                    $this->rateManager = $this->initialiseRateManager($application);

                    $quote
                        ->setPolicyOptions(
                            $application->getPropertyRental(),
                            $this->rateManager->getPremium(),
                            $this->rateManager->getNilExcessOption()
                        )
                        ->getAppData()
                            ->setPremium($this->rateManager->getPremium())
                            ->setIpt($this->rateManager->getIpt())
                            ->setQuote($this->rateManager->getQuote())
                            ->setRateSetID($this->rateManager->getRateSetID());

                    // Does the given reference satisfy the policy criteria for this particular reference?
                    $referralRequired = false;
                    $reference = $this->getReferenceFromSession($application->getReferenceNumber());
                    $referenceSatisfiesCriteria = $this->referenceSatisfiesCriteria($reference);

                    // Does the application satisfy the application criteria?
                    $this->referralModel->setFromApplication($application, $this->rateManager->getPremium());
                    $applicationSatisfiesCriteria = $this->applicationSatisfiesCriteria();

                    if ( ! $referenceSatisfiesCriteria || ! $applicationSatisfiesCriteria) {
                        // Fire a POLICY_REFERRED event to handle all referral related actions.
                        $referredEvent = new ReferredEvent($this->referralModel);
                        $this->eventDispatcher->dispatch(RRPEvents::POLICY_REFERRED, $referredEvent);

                        // Mark this application as referred.
                        $quote->getAppData()->setPayStatus(Model_Insurance_RentRecoveryPlus_LegacyPolicy::PAY_STATUS_REFERRED);
                        $referralRequired = true;
                    }

                    if ($application->getIsContinuationOfExistingPolicy()) {

                        $mailManager = new Application_Core_Mail();

                        if ($application->getIsExistingPolicyToBeCancelled()) {
                            $subject = str_replace(
                                '{$existingPolicyRef}',
                                $application->getExistingPolicyRef(),
                                $this->_params->connect->settings->rentRecoveryPlus->cancelExisting->emailSubject
                            );
                            $message = $this->getContainer()->get('twig')->render(
                                'rent-recovery-plus-cancel-existing-mail.plain.twig',
                                array(
                                    'agentName' => $this->_agentObj->name,
                                    'agentSchemeNumber' => $this->_agentSchemeNumber,
                                    'policyNumber' => $policyNumber
                                ));

                            $mailManager
                                ->setTo(
                                    $this->_params->connect->settings->rentRecoveryPlus->cancelExisting->emailToAddress,
                                    $this->_params->connect->settings->rentRecoveryPlus->cancelExisting->emailToName
                                )
                                ->setFrom(
                                    $this->_params->connect->settings->rentRecoveryPlus->cancelExisting->emailFromAddress,
                                    $this->_params->connect->settings->rentRecoveryPlus->cancelExisting->emailFromName
                                )
                                ->setSubject($subject)
                                ->setBodyText($message);
                        }
                        else {
                            $subject = str_replace(
                                '{$existingPolicyRef}',
                                $application->getExistingPolicyRef(),
                                $this->_params->connect->settings->rentRecoveryPlus->cancelExisting->emailSubject
                            );
                            $message = $this->getContainer()->get('twig')->render(
                                'rent-recovery-plus-keep-existing-mail.plain.twig',
                                array(
                                    'agentName' => $this->_agentObj->name,
                                    'agentSchemeNumber' => $this->_agentSchemeNumber,
                                    'existingPolicyRef' => $application->getExistingPolicyRef()
                                ));

                            $mailManager
                                ->setTo(
                                    $this->_params->connect->settings->rentRecoveryPlus->keepExisting->emailToAddress,
                                    $this->_params->connect->settings->rentRecoveryPlus->keepExisting->emailToName
                                )
                                ->setFrom(
                                    $this->_params->connect->settings->rentRecoveryPlus->keepExisting->emailFromAddress,
                                    $this->_params->connect->settings->rentRecoveryPlus->keepExisting->emailFromName
                                )
                                ->setSubject($subject)
                                ->setBodyText($message);
                            if (isset($this->_params->connect->settings->rentRecoveryPlus->keepExisting->emailCcAddress)) {
                                $mailManager->setCC(
                                    $this->_params->connect->settings->rentRecoveryPlus->keepExisting->emailCcAddress
                                );
                            }
                        }
                        $mailManager->send();
                    }

                    if ( ! $quote->save()) {
                        return $this->renderTwigView('/rentguarantee/rent-recovery-plus-error.html.twig');
                    }

                    if ($referralRequired) {
                        // Render the referral text to the user.
                        return $this->renderTwigView(
                            '/rentguarantee/rent-recovery-plus-referral.html.twig',
                            array('policyNumber' => $policyNumber)
                        );
                    }

                    if ($application->getIsPayMonthly()) {
                        $paymentDetails = sprintf(
                            'This will appear on your invoices as %d monthly payments of'
                            . ' £%.02f plus £%.02f (IPT at %d%%). Total monthly payment £%.02f. ',
                            $application->getPolicyLength(),
                            $this->rateManager->getPremium(),
                            $this->rateManager->getIpt(),
                            $this->rateManager->getIptPercent(),
                            $this->rateManager->getQuote()
                        );
                    }
                    else {
                        $paymentDetails = sprintf(
                            'This will appear on your next invoices as £%.02f plus £%.02f (IPT at %d%%). Total £%.02f. ',
                            $this->rateManager->getPremium(),
                            $this->rateManager->getIpt(),
                            $this->rateManager->getIptPercent(),
                            $this->rateManager->getQuote()
                        );
                    }

                    return $this->renderTwigView('/rentguarantee/rent-recovery-plus-quote.html.twig', array(
                        'policyNumber' => $policyNumber,
                        'paymentDetails' => $paymentDetails
                    ));
                }
            }
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
        }

        $agent = new Datasource_Core_AgentUser();
        if ($agent->canDisplayRRPI($this->_agentSchemeNumber, $this->_agentUserName)) {
            $this->renderTwigView('/rentguarantee/rent-recovery-plus-application.html.twig', array(
                'form' => $form->createView(),
            ));
        }
        else {
            $this->renderTwigView('/rentguarantee/rent-recovery-plus-information.html.twig');
        }
    }

    /**
     * @param null $data
     * @param null $options
     * @return \Symfony\Component\Form\FormInterface
     */
    private function _getRentRecoveryPlusApplicationForm($data = null, $options = null)
    {
        $applicationType = $this->getContainer()->get('rrp.form.application');
        return $this->getFormFactory()->create($applicationType, $data, $options);
    }

    /**
     * Accept the RRP Quote
     *
     * @return array
     */
    public function rentRecoveryPlusAcceptAction()
    {
        $request = $this->getSymfonyRequest();

        $quoteNumber = null;
        if ($request->isMethod('POST')) {
            $quoteNumber = $request->request->get('policyNumber');
        }
        else if ($request->isMethod('GET')) {
            $quoteNumber = $request->query->get('policyNumber');
        }

        if ($quoteNumber) {
            $applicationDecoratorClass = $this->getContainer()->get('rrp.application.decorator.class');
            /** @var RRP\Application\Decorators\RentRecoveryPlusQuote $quote */
            $quote = $applicationDecoratorClass::getDecorator('RentRecoveryPlusQuote');

            if ($quote->populateByPolicyNumber($quoteNumber)) {
                $referred = false;
                if (
                    $quote->getAppData()->getPayStatus() ==
                    Model_Insurance_RentRecoveryPlus_LegacyPolicy::PAY_STATUS_REFERRED
                ) {
                    $referred = true;
                }
                else {
                    $policyOptionsManagerClass = $this->getContainer()->get('rrp.utility.policy_options_manager.class');
                    $propertyRental = $policyOptionsManagerClass::getOption(
                        $quote->getAppData()->getPolicyOptions(),
                        Model_Insurance_RentRecoveryPlus_LegacyPolicy::POLICY_OPTION_RRP,
                        $quote->getAppData()->getAmountsCovered()
                    );

                    // Does the given reference satisfy the policy criteria?
                    $application = $quote->getApplicationData($quoteNumber);
                    $reference = $this->getReferenceFromSession($application->getReferenceNumber());
                    $referenceSatisfiesCriteria = $this->referenceSatisfiesCriteria($reference);

                    $this->referralModel->setFromRrpPolicy($quote, $propertyRental);
                    $applicationSatisfiesCriteria = $this->applicationSatisfiesCriteria();

                    if ( ! $referenceSatisfiesCriteria || ! $applicationSatisfiesCriteria) {
                        // Need to set the policy number on the referral object so that listeners can access it for whatever reason.
                        $this->referralModel->setPolicyNumber($quoteNumber);

                        // Fire a POLICY_REFERRED event to handle all referral related actions.
                        $referredEvent = new ReferredEvent($this->referralModel);
                        $this->eventDispatcher->dispatch(RRPEvents::POLICY_REFERRED, $referredEvent);

                        // Mark this application as referred.
                        $quote->getAppData()->setPayStatus(Model_Insurance_RentRecoveryPlus_LegacyPolicy::PAY_STATUS_REFERRED);
                        $quote->save();
                        $referred = true;
                    }
                }

                if ($referred) {
                    return $this->renderTwigView(
                        '/rentguarantee/rent-recovery-plus-referral.html.twig',
                        array('policyNumber' => $quoteNumber)
                    );
                }

                if ($this->_params->connect->settings->rentRecoveryPlus->manualReferenceCheck) {
                    $quote->getAppData()->setPayStatus(
                        Model_Insurance_RentRecoveryPlus_LegacyPolicy::PAY_STATUS_REFERRED
                    );
                    $quote->save();
                    $policyNote = new Datasource_Insurance_PolicyNotes();
                    $note = "Policy incepted by agent, passed to RG team to validate reference and place on risk.";
                    $policyNote->addNote($quoteNumber, $note);

                    // Send referral email
                    $subject = str_replace(
                        '{$policyNumber}',
                        $quoteNumber,
                        $this->_params->connect->settings->rentRecoveryPlus->referral->emailSubject
                    );
                    $message = $this->getContainer()->get('twig')->render(
                        'rent-recovery-plus-manual-reference-mail.plain.twig',
                        array(
                            'agentName' => $this->_agentObj->name,
                            'agentSchemeNumber' => $this->_agentSchemeNumber
                        ));

                    $mailManager = new Application_Core_Mail();
                    $mailManager
                        ->setTo(
                            $this->_params->connect->settings->rentRecoveryPlus->referral->emailToAddress,
                            $this->_params->connect->settings->rentRecoveryPlus->referral->emailToName
                        )
                        ->setFrom(
                            $this->_params->connect->settings->rentRecoveryPlus->referral->emailFromAddress,
                            $this->_params->connect->settings->rentRecoveryPlus->referral->emailFromName
                        )
                        ->setSubject($subject)
                        ->setBodyText($message);
                    $mailManager->send();

                    return $this->renderTwigView(
                        '/rentguarantee/rent-recovery-plus-manual-reference.html.twig',
                        array('policyNumber' => $quoteNumber)
                    );
                }

                $policyNumber = $quote->acceptQuote($quoteNumber);
                if ( ! $policyNumber) {
                    $subject = str_replace(
                        '{$policyNumber}',
                        $policyNumber,
                        $this->_params->connect->settings->rentRecoveryPlus->referral->emailSubject
                    );
                    $message = $this->getContainer()->get('twig')->render(
                        'rent-recovery-plus-error-mail.plain.twig',
                        array(
                            'agentName' => $this->_agentObj->name,
                            'agentSchemeNumber' => $this->_agentSchemeNumber,
                            'dateEntered' => date('Y-m-d H:i:s'),
                            'policyNumber' => $policyNumber
                        ));

                    $mailManager = new Application_Core_Mail();
                    $mailManager
                        ->setTo(
                            $this->_params->connect->settings->rentRecoveryPlus->referral->emailToAddress,
                            $this->_params->connect->settings->rentRecoveryPlus->referral->emailToName
                        )
                        ->setFrom(
                            $this->_params->connect->settings->rentRecoveryPlus->referral->emailFromAddress,
                            $this->_params->connect->settings->rentRecoveryPlus->referral->emailFromName
                        )
                        ->setSubject($subject)
                        ->setBodyText($message);
                    $mailManager->send();

                    $this->renderTwigView('/rentguarantee/rent-recovery-plus-error.html.twig');
                }
                else {
                    Manager_Insurance_Quote::sendInception(
                        $policyNumber,
                        null,
                        null,
                        $this->_params->connect->settings->rentRecoveryPlus->systemCsuID
                    );

                    return $this->renderTwigView('/rentguarantee/rent-recovery-plus-confirmation.html.twig', array(
                        'policyNumber' => $policyNumber,
                        'emailQueryAddress' => $this->_params->connect->settings->rentRecoveryPlus->queryEmailAddress
                    ));
                }
            }
        }
        $this->renderTwigView('/rentguarantee/rent-recovery-plus-error.html.twig');
    }

    /**
     * Cancel the policy
     */
    public function rentRecoveryPlusCancelAction()
    {
        $request = $this->getSymfonyRequest();

        $cancellationType = $this->getContainer()->get('rrp.form.cancellation');

        $policyNumber = null;
        $isConfirmed = false;
        if ($request->isMethod('GET')) {
            $policyNumber = $request->query->get('policyNumber');
        }
        else if ($request->isMethod('POST')) {
            $isConfirmed = $request->request->get('isConfirmed', 0);
            $postedData = $request->request->get($cancellationType->getName());
            if (array_key_exists('policyNumber', $postedData)) {
                $policyNumber = $postedData['policyNumber'];
            }
        }

        if ($policyNumber) {
            // Ensure date picker CSS and JS are available
            $this->view->headLink()->appendStylesheet('/assets/vendor/jquery-datepicker/css/datePicker.css');
            $this->view->headScript()->appendFile('/assets/vendor/jquery-date/js/date.js');
            $this->view->headScript()->appendFile('/assets/vendor/jquery-datepicker/js/jquery.datePicker.js');
            $message = null;

            $applicationDecoratorClass = $this->getContainer()->get('rrp.application.decorator.class');
            /** @var RRP\Application\Decorators\RentRecoveryPlusPolicy $policy */
            $policy = $applicationDecoratorClass::getDecorator('RentRecoveryPlusPolicy');

            $policy->populateByPolicyNumber($policyNumber);
            if (Manager_Core_PolicyNumber::isQuote($policyNumber)) {
                $message = 'Quotes do not need cancelling';
                $isConfirmed = false;
            }
            else if (
                $policy->getAppData()->getPayStatus() == Model_Insurance_RentRecoveryPlus_LegacyPolicy::PAY_STATUS_CANCELLED ||
                strtotime($policy->getAppData()->getCancelledDate()) > 0
            ) {
                $message = 'This policy has already been cancelled';
                $isConfirmed = false;
            }
            else {
                $cancellationPeriod = $this->_params->connect->settings->rentRecoveryPlus->cancellationPeriod;
                $now = new \DateTime();
                $cancellationEndAt = new \DateTime($policy->getAppData()->getStartDate());
                $cancellationEndAt = $cancellationEndAt->setTime(23, 59, 59)->add(new \DateInterval($cancellationPeriod));
                if ($cancellationEndAt <= $now) {
                    $claimsDataSource = new Datasource_Insurance_Claims();
                    $claims = $claimsDataSource->getByPolicyNumber($policyNumber);
                    if ($claims) {
                        if ($claims->getClaimstatus() == $claims::CLAIM_STATUS_ACTIVE) {
                            $message = 'This policy has claims outstanding';
                            $isConfirmed = false;
                        }
                    }
                }
            }

            /** @var \RRP\Model\RentRecoveryPlusCancellation $cancellation */
            $cancellation = $this->getContainer()->get('rrp.model.cancellation');
            $cancellation
                ->setPolicyNumber($policy->getAppData()->getPolicyNumber())
                ->setPolicyExpiresAt($policy->getAppData()->getEndDate());
            $form = $this->getFormFactory()->create($cancellationType, $cancellation);

            if ($isConfirmed) {
                $form->submit($request);

                if (!$form->isValid()) {
                    $isConfirmed = false;
                }
            }

            if ($isConfirmed) {
                /** @var \RRP\Model\RentRecoveryPlusCancellation $cancellation */
                $cancellation = $form->getData();

                $refundValue = $policy->cancel($cancellation->getPolicyEndAt());
                if ($refundValue !== null) {
                    Manager_Insurance_Quote::sendCancellation(
                        $policyNumber,
                        null,
                        null,
                        $this->_params->connect->settings->rentRecoveryPlus->systemCsuID,
                        array('refundValue' => $refundValue)
                    );

                    $this->renderTwigView('/rentguarantee/rent-recovery-plus-cancellation.html.twig', array(
                        'form' => $form->createView(),
                        'isConfirmed' => $policyNumber,
                        'queryPhoneNumber' => $this->_params->connect->settings->rentRecoveryPlus->queryPhoneNumber
                    ));
                    return;
                }
            }
            else {
                if (!empty($policyNumber)) {
                    $cancellationDetails = array(
                        'queryPhoneNumber' => $this->_params->connect->settings->rentRecoveryPlus->queryPhoneNumber
                    );
                    if ($message) {
                        $cancellationDetails['message'] = $message;
                    }
                    else {
                        $cancellationDetails['form'] = $form->createView();
                    }
                    $this->renderTwigView('/rentguarantee/rent-recovery-plus-cancellation.html.twig', $cancellationDetails);
                    return;
                }
            }
        }

        $this->renderTwigView('/rentguarantee/rent-recovery-plus-error.html.twig');
    }

    /**
     * RRP MTA Confirmation
     */
    public function rentRecoveryPlusMtaConfirmationAction()
    {
        $request = $this->getSymfonyRequest();

        $policyNumber = null;
        if ($request->isMethod('POST')) {
            $policyNumber = $request->get('policyNumber');
            $mtaID = $request->get('mtaID');

            if ($policyNumber && $mtaID) {
                $mtaDecoratorClass = $this->getContainer()->get('rrp.mta.decorator.class');

                /** @var RRP\Mta\Decorators\RentRecoveryPlusMta $mta */
                $mta = $mtaDecoratorClass::getDecorator('RentRecoveryPlusMta');

                if ($mta->accept($policyNumber, $mtaID)) {
                    Manager_Insurance_Quote::sendMta(
                        $policyNumber,
                        null,
                        null,
                        $this->_params->connect->settings->rentRecoveryPlus->systemCsuID,
                        array('mtaId' => $mtaID)
                    );

                    // Show confirmation of changes page
                    $this->renderTwigView('/rentguarantee/rent-recovery-plus-mta-confirmation.html.twig', array(
                        'policyNumber' => $policyNumber,
                        'emailQueryAddress' => $this->_params->connect->settings->rentRecoveryPlus->queryEmailAddress
                    ));
                    return;
                }
            }
        }
        $this->renderTwigView('/rentguarantee/rent-recovery-plus-error.html.twig');
    }

    /**
     * RRP MTA's
     */
    public function rentRecoveryPlusMtaAction()
    {
        $request = $this->getSymfonyRequest();

        $mtaType = $this->getContainer()->get('rrp.form.mta');

        $policyNumber = null;
        if ($request->isMethod('GET')) {
            $policyNumber = $request->get('policyNumber');
        }
        else if ($request->isMethod('POST')) {
            $postedData = $request->request->get($mtaType->getName());
            if (array_key_exists('policyNumber', $postedData)) {
                $policyNumber = $postedData['policyNumber'];
            }
        }

        if ( ! empty($policyNumber)) {
            /** @var object $applicationDecoratorClass */
            $applicationDecoratorClass = $this->getContainer()->get('rrp.application.decorator.class');

            /** @var RRP\Application\Decorators\RentRecoveryPlusPolicy $policy */
            $policy = $applicationDecoratorClass::getDecorator('RentRecoveryPlusPolicy');

            if ( ! $policy->populateByPolicyNumber($policyNumber)) {
                $this->renderTwigView('/rentguarantee/rent-recovery-plus-error.html.twig');
                return;
            }
            $policyOptionsManagerClass = $this->getContainer()->get('rrp.utility.policy_options_manager.class');
            $propertyRental = $policyOptionsManagerClass::getOption(
                $policy->getAppData()->getPolicyOptions(),
                Model_Insurance_RentRecoveryPlus_LegacyPolicy::POLICY_OPTION_RRP,
                $policy->getAppData()->getAmountsCovered()
            );
            $isNilExcess = $policyOptionsManagerClass::isOptionSet(
                $policy->getAppData()->getPolicyOptions(),
                Model_Insurance_RentRecoveryPlus_LegacyPolicy::POLICY_OPTION_RRP_NIL_EXCESS,
                $policy->getAppData()->getAmountsCovered()
            );
            /** @var RRP\Model\RentRecoveryPlusMta $mtaSetter */
            $defaultMta = $this->getContainer()->get('rrp.model.mta');
            $defaultMta
                ->setPolicyNumber($policy->getAppData()->getPolicyNumber())
                ->setPolicyStartedAt($policy->getAppData()->getStartDate())
                ->setPolicyExpiresAt($policy->getAppData()->getEndDate())
                ->setReferenceType($policy->getRrpData()->getReferenceType())
                ->setPropertyRental($propertyRental)
                ->setRefNo($policy->getAppData()->getRefNo());


            $referenceTypeClass = $this->getContainer()->get('rrp.reference_types.class');
            $isNilExcessAllowed = $referenceTypeClass::isNilExcessAllowed($policy->getRrpData()->getReferenceType());

            // Ensure date picker CSS and JS are available
            $this->view->headLink()->appendStylesheet('/assets/vendor/jquery-datepicker/css/datePicker.css');
            $this->view->headScript()->appendFile('/assets/vendor/jquery-date/js/date.js');
            $this->view->headScript()->appendFile('/assets/vendor/jquery-datepicker/js/jquery.datePicker.js');

            $form = $this->getFormFactory()->create($mtaType, $defaultMta);

            if ($request->isMethod('POST')) {
                $form->submit($request);

                if ($form->isValid()) {
                    // Check if only an update of the form to add/remove field. If so it will pass the isValid check
                    //  as it doesn't undergo full validation so we need catch it separately

                    if ($request->isXmlHttpRequest()) {
                        $update = true;
                    }
                    else {
                        $update = $form->get('update')->isClicked();
                    }

                    if ( ! $update) {
                        // Check if only an update of the form to add/remove field. If so it will pass the isValid check
                        //  as it doesn't undergo full validation so we need catch it separately

                        /** @var RRP\Model\RentRecoveryPlusMta $mta */
                        $mta = $form->getData();

                        /** @var RRP\Rate\RateDecorators\RentRecoveryPlus $rateManager */
                        $rateDecoratorClass = $this->getContainer()->get('rrp.rate.decorator.class');

                        $referralRequired = false;
                        try {
                            /** @var RRP\Rate\RateDecorators\RentRecoveryPlus $rateManager */
                            $rateManager = $rateDecoratorClass::getDecorator(
                                'RentRecoveryPlus',
                                $this->_agentsRateID,
                                $this->_params->connect->settings->rentRecoveryPlus->riskArea,
                                $this->_params->connect->settings->rentRecoveryPlus->iptPercent,
                                $mta->getPropertyRental(),
                                $isNilExcess,
                                $policy->getRrpData()->getReferenceType(),
                                $policy->getAppData()->getPolicyLength(),
                                $policy->getAppData()->isPayMonthly(),
                                DateTime::createFromFormat('Y-m-d', $policy->getAppData()->getStartDate())
                            );
                        } catch (Exception $ex) {
                            $referralRequired = true;
                        }

                        $mtaDecoratorClass = $this->getContainer()->get('rrp.mta.decorator.class');

                        /** @var RRP\Mta\Decorators\RentRecoveryPlusMta $mtaDecorator */
                        $mtaDecorator = $mtaDecoratorClass::getDecorator('RentRecoveryPlusMta');
                        $mtaID = $mtaDecorator->create(
                            $policy,
                            $mta,
                            $rateManager->getPremium(),
                            $rateManager->getQuote(),
                            $rateManager->getIpt(),
                            $rateManager->getNilExcessOption()
                        );

                        $proRataCalcClass = $this->getContainer()->get('rrp.utility.pro_rata_calcs.class');
                        /** @var RRP\Utility\ProRataCalculations $proRataCalcs */
                        $proRataCalcs = new $proRataCalcClass(
                            $mta->getPolicyStartedAt(),
                            $mta->getMtaEffectiveAt()
                        );
                        $premiumDelta = $proRataCalcs->getAdjustment(
                            $policy->getAppData()->getPolicyLength(),
                            $policy->getAppData()->getPolicyLength(),
                            $rateManager->getPremium(),
                            $policy->getAppData()->getPremium()
                        );
                        $iptDelta = $proRataCalcs->getAdjustment(
                            $policy->getAppData()->getPolicyLength(),
                            $policy->getAppData()->getPolicyLength(),
                            $rateManager->getIpt(),
                            $policy->getAppData()->getIpt()
                        );
                        $quoteDelta = $proRataCalcs->getAdjustment(
                            $policy->getAppData()->getPolicyLength(),
                            $policy->getAppData()->getPolicyLength(),
                            $rateManager->getQuote(),
                            $policy->getAppData()->getQuote()
                        );
                        $premiumProRata = $proRataCalcs->getProRata(
                            $policy->getAppData()->getPolicyLength(),
                            $policy->getAppData()->getPolicyLength(),
                            $rateManager->getPremium(),
                            $policy->getAppData()->getPremium()
                        );
                        $iptProRata = $proRataCalcs->getProRata(
                            $policy->getAppData()->getPolicyLength(),
                            $policy->getAppData()->getPolicyLength(),
                            $rateManager->getIpt(),
                            $policy->getAppData()->getIpt()
                        );
                        $quoteProRata = $proRataCalcs->getProRata(
                            $policy->getAppData()->getPolicyLength(),
                            $policy->getAppData()->getPolicyLength(),
                            $rateManager->getQuote(),
                            $policy->getAppData()->getQuote()
                        );

                        /** @var RRP\Referral\RentRecoveryPlusReferral $referral */
                        $referral = $this->getContainer()->get('rrp.referral');
                        $referral->setFromMta($mta, $rateManager->getPremium());

                        if ( ! $referralRequired) {
                            $referralRequired = $referral->isReferralRequired();
                        }
                        if ($referralRequired) {
                            $policy->getAppData()->setPayStatus(
                                Model_Insurance_RentRecoveryPlus_LegacyPolicy::PAY_STATUS_REFERRED
                            );

                            $policyNote = new Datasource_Insurance_PolicyNotes();
                            $note =
                                "This policy has been referred for the following reason(s)\n\n" .
                                implode("\n", $referral->getReferralReason());
                            $policyNote->addNote($policy->getAppData()->getPolicyNumber(), $note);

                            // Send referral email
                            $subject = str_replace(
                                '{$policyNumber}',
                                $policy->getAppData()->getPolicyNumber(),
                                $this->_params->connect->settings->rentRecoveryPlus->referral->emailSubject
                            );
                            $message = $this->getContainer()->get('twig')->render(
                                'rent-recovery-plus-mta-referral-mail.plain.twig',
                                array(
                                    'agentName' => $this->_agentObj->name,
                                    'agentSchemeNumber' => $this->_agentSchemeNumber,
                                    'referralReasons' => $referral->getReferralReason()
                                ));

                            $mailManager = new Application_Core_Mail();
                            $mailManager
                                ->setTo(
                                    $this->_params->connect->settings->rentRecoveryPlus->referral->emailToAddress,
                                    $this->_params->connect->settings->rentRecoveryPlus->referral->emailToName
                                )
                                ->setFrom(
                                    $this->_params->connect->settings->rentRecoveryPlus->referral->emailFromAddress,
                                    $this->_params->connect->settings->rentRecoveryPlus->referral->emailFromName
                                )
                                ->setSubject($subject)
                                ->setBodyText($message);
                            $mailManager->send();

                            $this->renderTwigView('/rentguarantee/rent-recovery-plus-mta-referral.html.twig');
                            return;
                        }
                        if ($premiumDelta == 0) {
                            $paymentDetails = 'We are pleased to confirm this has not affected your premium.';
                        } else if ($policy->getAppData()->isPayMonthly()) {
                            $paymentDetails = sprintf(
                                'From the date of the adjustment this will appear on your invoices as monthly'
                                . ' payments of £%.02f plus £%.02f (IPT at %d%%). Total monthly payment £%.02f. ',
                                $premiumProRata,
                                $iptProRata,
                                $this->_params->connect->settings->rentRecoveryPlus->iptPercent,
                                $quoteProRata
                            );
                        }
                        else if ($premiumDelta < 0) {
                            $paymentDetails = sprintf(
                                'This will appear on your next invoice as a refund of'
                                . ' £%.02f plus £%.02f (IPT at %d%%). Total £%.02f. ',
                                -$premiumDelta,
                                -$iptDelta,
                                $this->_params->connect->settings->rentRecoveryPlus->iptPercent,
                                -$quoteDelta
                            );
                        }
                        else {
                            $paymentDetails = sprintf(
                                'This will appear on your next invoice as £%.02f plus £%.02f (IPT at %d%%). Total £%.02f. ',
                                $premiumDelta,
                                $iptDelta,
                                $this->_params->connect->settings->rentRecoveryPlus->iptPercent,
                                $quoteDelta
                            );
                        }

                        // Show premium and ask for confirmation
                        $this->renderTwigView('/rentguarantee/rent-recovery-plus-mta-quote.html.twig', array(
                            'policyNumber' => $policyNumber,
                            'paymentDetails' => $paymentDetails,
                            'mtaID' => $mtaID
                        ));

                        return;
                    }
                }
            }

            if ($this->getRequest()->isXmlHttpRequest()) {
                $this->_helper->viewRenderer->setNoRender(true);
                $this->_helper->layout->disableLayout();
            }

            $this->renderTwigView('/rentguarantee/rent-recovery-plus-mta.html.twig', array(
                'form' => $form->createView(),
                'nilExcessAllowed' => $isNilExcessAllowed
            ));
            return;
        }

        $this->renderTwigView('/rentguarantee/rent-recovery-plus-error.html.twig');
    }

    /**
     * Controller action for Rent Guarantee Renewal Suite main screen
     *
     * @return void
     */
    public function renewalsAction()
    {
        $filters = array('*' => array('StringTrim','HtmlEntities','StripTags'));
        $validators = array('*' => array('allowEmpty' => true));
        $input['policynumber'] = $this->_request->getParam('policynumber');
        $validate = new Zend_Filter_Input($filters, $validators, $input);
        $this->view->policyNumber = $validate->policynumber;

        //Get Renewal invited data
        $dsRent = new Datasource_Connect_Rentguarantee();
        $this->view->invited = $dsRent->getRentGuaranteeRenewalInvites($this->_agentSchemeNumber, $this->view->policyNumber);
        $this->view->overdue = $dsRent->getRentGuaranteeRenewalOverdues($this->_agentSchemeNumber, $this->view->policyNumber);
        $this->view->expirestodaycount = $dsRent->getExpiresTodayCount($this->_agentSchemeNumber, $this->view->policyNumber);

        if ($this->getRequest()->isPost() && $this->getRequest()->getParam('export') == "yes") // Export data
        {
            /* // This is the correct code, but over HTTPS IE doesn't like it.
            $this->getResponse()->setHeader('Content-Type', 'application/vnd.ms-excel');
            $this->getResponse()->setHeader('Content-Disposition', 'attachment; filename="PRGI-Renewals.xls"');
            */
            // This is the dirty way of doing it, but it works in IE.  IE sucks.
            header('Pragma: public'); // required
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false); // required for certain browsers
            header('Content-Disposition: attachment; filename="PRGI-Renewals.xls"');
            header('Content-type: application/vnd.ms-excel');

            // Disable layouts and render spreadsheet format
            $this->_helper->getHelper('layout')->disableLayout();
            $this->render('renewals-csv');
            return;
        }
    }

    /**
     * Display landlords consent form, used by IO and IAR status agents
     *
     * @return void
     */
    public function confirmLandlordsConsentAction()
    {
        $request = $this->getRequest();

        if ($this->getRequest()->isPost())
        {
            // Redirect request
            if ($request->getParam('formsubmit') == 'Continue')
            {
                // Go forward
                $this->_redirect
                (
                    $this->getRequest()->getControllerName() .
                    '/renew-policy?policynumber=' . $request->getParam('policynumber')
                );

                return;
            }
            else if ($request->getParam('formsubmit') == 'Back')
            {
                // Go back
                $this->_redirect
                (
                    $this->getRequest()->getControllerName() .
                    '/renewals'
                );

                return;
            }
        }
    }

    /**
     * Controller action for Rent Guarantee Policy renewal process.
     * Integrates into referencing via redirects.
     *
     * @return void
     */
    public function renewPolicyAction()
    {
        $request = $this->getRequest();
        $policynumber = $request->getParam('policynumber');

        $renewform = new Connect_Form_RentGuarantee_Renew();
        $renewform->populate($request->getPost());

        if ($request->isPost())
        {
            if ($renewform->getElement('formsubmit_back')->getValue()) // Back button pressed
            {
                // Go back
                // Back to renewals overview page
                $this->_redirect
                (
                    $this->getRequest()->getControllerName() . '/renewals'
                );
            }
            else if ($renewform->isValid($request->getPost()) && $renewform->getElement('formsubmit_continue')->getValue())
            {
                // Parse the form, curl the premrenewal.pl script and return the response
                $params = Zend_Registry::get('params');
                $referencingServer = $params->connect->baseUrl->referencing;

                // Make call to legacy referencing
                $requesturl = $referencingServer . '/cgi-bin/premrenewal.pl?';
                $requesturl .= 'policynumber=' . $renewform->getElement('policynumber')->getValue() . '&';
                $requesturl .= 'pollength=' . $renewform->getElement('pollength')->getValue() . '&';
                $requesturl .= 'title=' . str_replace(' ','%20',$renewform->getElement('title')->getValue()) . '&';
                $requesturl .= 'firstname=' .str_replace(' ','%20',$renewform->getElement('firstname')->getValue()) . '&';
                $requesturl .= 'lastname=' . str_replace(' ','%20',$renewform->getElement('lastname')->getValue()) . '&';
                $requesturl .= 'riskaddress=' . str_replace(' ','%20',$renewform->getElement('riskaddress')->getValue()) . '&';
                $requesturl .= 'risktown=' . str_replace(' ','%20',$renewform->getElement('risktown')->getValue()) . '&';
                $requesturl .= 'riskpc=' . str_replace(' ','%20',$renewform->getElement('riskpc')->getValue()) . '&';
                $requesturl .= 'date=' . $renewform->getElement('date')->getValue() . '&';
                $requesturl .= 'signature=' .str_replace(' ','%20',$renewform->getElement('signature')->getValue()) . '&';
                $requesturl .= 'term=' . $renewform->getElement('term')->getValue() . '&';
                $requesturl .= 'rent=' . $renewform->getElement('rent')->getValue() . '&';
                $requesturl .= 'fsastatus=' . $renewform->getElement('fsastatus')->getValue() . '&';
                $requesturl .= 'tenancytype=' . $renewform->getElement('tenancytype')->getValue() . '&';
                $requesturl .= 'rgoffer=' . $renewform->getElement('rgoffer')->getValue() . '&';
                $requesturl .= 'rentshare=' . $renewform->getElement('rentshare')->getValue() . '&';
                $requesturl .= 'how=connect&';
                $requesturl .= 'connectuser=1&';
                $requesturl .= 'brand=connect&';
                $requesturl .= 'message=Renew';

                $httpclient = curl_init();
                curl_setopt($httpclient, CURLOPT_URL, $requesturl);
                curl_setopt($httpclient, CURLOPT_HEADER, 0);
                curl_setopt($httpclient, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($httpclient, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($httpclient, CURLOPT_SSL_VERIFYHOST, 0);

                $httpresponse = curl_exec($httpclient);
                curl_close($httpclient);

                //Check for errors
                if (preg_match('/rpc server error|software error/i', $httpresponse)) {

                    $config = Zend_Registry::get('params');
                    $this->view->helpLine = $config->connect->rentguaranteerenewals->helpLine;
                    $this->render('renew-policy-fail');
                    return;
                }

                // Extract paragraph tag from response - should contain certificate link
                $httpresponse = str_replace("\n", '', $httpresponse); // Remove line breaks for simple regex
                // Extract <p>...</p> content
                preg_match('/(<p>.*<\/p>)/i', $httpresponse, $matches);
                $responseSnippet = (isset($matches[1])) ? $matches[1] : '<p>A problem occurred.</p>';

                $this->view->responseHtml = $responseSnippet;
                $this->render('renew-policy-success');
                return;
            }
        }

        $rgmanager = new Manager_Insurance_Policy_Rentguarantee();
        $policydetails = $rgmanager->getPolicyDetails($policynumber);
        $referencedetails = $rgmanager->getReference($policynumber);

        $this->view->policynumber = $policydetails->policyNumber;

        $this->view->startdate = new Zend_Date($policydetails->startDate, Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        $this->view->enddate = new Zend_Date($policydetails->endDate, Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);

        $this->view->tenancyterm = $referencedetails->propertyLease->tenancyTerm;
        $this->view->rent = $referencedetails->propertyLease->rentPerMonth;
        $this->view->rentshare = $referencedetails->referenceSubject->shareOfRent;

        $this->view->title = $referencedetails->referenceSubject->name->title;
        $this->view->firstname = $referencedetails->referenceSubject->name->firstName;
        $this->view->lastname = $referencedetails->referenceSubject->name->lastName;

        $this->view->housenumber = $referencedetails->propertyLease->address->houseNumber;
        $this->view->housename = $referencedetails->propertyLease->address->houseName;
        $this->view->addressline1 = $referencedetails->propertyLease->address->addressLine1;
        $this->view->addressline2 = $referencedetails->propertyLease->address->addressLine2;
        $this->view->town = $referencedetails->propertyLease->address->town;
        $this->view->postcode = $referencedetails->propertyLease->address->postCode;
        $this->view->county = $referencedetails->propertyLease->address->county;
        $this->view->country = $referencedetails->propertyLease->address->country;

        $this->view->productlength = 0;
        $this->view->productname = $referencedetails->productSelection->product->name;
        $this->view->tenancyterm = $referencedetails->productSelection->duration;

        // 6 month premium
        $this->view->premium6month = $rgmanager->getRenewalPremium(6, $referencedetails->internalId);
        $this->view->premium = $this->view->premium6month;

        // 12 month premium
        $this->view->premium12month = $rgmanager->getRenewalPremium(12, $referencedetails->internalId);
        $this->view->premium = $this->view->premium12month;

        $this->view->renewform = $renewform;
    }

    /**
     * Renewal document selection page, for non IO and IAR
     * typed agents
     *
     * @return void
     */
    public function selectRenewalDocumentAction()
    {
        $request = $this->getRequest();
        $selectrenewaldocumentform = new Connect_Form_RentGuarantee_SelectRenewalDocument();
        $selectrenewaldocumentform->populate($request->getPost());

        $policynumber = $request->getParam('policynumber');
        $session = new Zend_Session_Namespace('connect_rg');
        $session->policynumber = $policynumber;

        unset($session->landlords_email);
        unset($session->landlords_name);
        unset($session->agent_contact_number);
        unset($session->policy_premium);
        unset($session->policy_term);
        unset($session->agent_job_title);

        if ($request->isPost())
        {
            if ($request->getPost('formsubmit_back'))
            {
                $this->_redirect
                (
                    $this->getRequest()->getControllerName() . '/renewals'
                );
            }
            else if ($selectrenewaldocumentform->isValid($request->getPost()) && $request->getPost('formsubmit_continue'))
            {
                $documenttype = $selectrenewaldocumentform->getElement('documenttype')->getValue();
                $session->documenttype = $documenttype;
                $session->agentinvite = $selectrenewaldocumentform->getElement('agentinvite')->getValue();

                if (isset($documenttype) && $documenttype != '')
                {
                    $this->_redirect
                    (
                        $this->getRequest()->getControllerName() .
                        '/configure-renewal-document'
                    );
                }
                else
                {
                    $this->_redirect
                    (
                        $this->getRequest()->getControllerName() .
                        '/send-renewal-document'
                    );
                }
            }
        }

        $this->view->selectrenewaldocumentform = $selectrenewaldocumentform;
        $this->view->policynumber = $this->getRequest()->getParam('policynumber');
    }

    /**
     * Displays confirmation page for sending the agent renewal invitation.
     * Useful for IO and IAR typed agents, who can only send this document.
     *
     * @return void
     */
    public function confirmAgentRenewalDocumentAction()
    {
        $request = $this->getRequest();
        $policynumber = $request->getParam('policynumber');
        $session = new Zend_Session_Namespace('connect_rg');
        $session->policynumber = $policynumber;
        $session->agentinvite = 1;

        if ($request->isPost())
        {
            if ($request->getPost('formsubmit_back'))
            {
                $this->_redirect
                (
                    $this->getRequest()->getControllerName() .
                    '/renewals'
                );
            }
            else if ($request->getPost('formsubmit_continue'))
            {
                $this->_redirect
                (
                    $this->getRequest()->getControllerName() .
                    '/send-renewal-document'
                );
            }
        }

        $this->view->policynumber = $this->getRequest()->getParam('policynumber');
    }

    /**
     * Page to provide agent with option to configure the landlords renewal document
     * with the landlords name and other details, and choose a sending method
     *
     * @return void
     */
    public function configureRenewalDocumentAction()
    {
        $request = $this->getRequest();
        $session = new Zend_Session_Namespace('connect_rg');

        $configuredocumentform = new Connect_Form_RentGuarantee_ConfigureRenewalDocument();

        // Pre-populate the form with data from the session, if the form has been posted
        // it will get overridden by the post data next.
        $configuredocumentform->getElement('landlords_email')->setValue($session->landlords_email);
        $configuredocumentform->getElement('landlords_name')->setValue($session->landlords_name);
        $configuredocumentform->getElement('policy_premium')->setValue($session->policy_premium);
        $configuredocumentform->getElement('policy_term')->setValue($session->policy_term);
        $configuredocumentform->getElement('agent_job_title')->setValue($session->agent_job_title);
        $configuredocumentform->getElement('agent_contact_number')->setValue($session->agent_contact_number);

        $configuredocumentform->populate($request->getPost());

        if ($request->isPost())
        {
            if ($request->getParam('formsubmit_back'))
            {
                $this->_redirect
                (
                    $this->getRequest()->getControllerName() . '/renewals'
                );
            }

            // Store data to session
            $session->landlords_email = $configuredocumentform->getElement('landlords_email')->getValue();
            $session->landlords_name = $configuredocumentform->getElement('landlords_name')->getValue();
            $session->policy_premium = $configuredocumentform->getElement('policy_premium')->getValue();
            $session->policy_term = $configuredocumentform->getElement('policy_term')->getValue();
            $session->agent_job_title = $configuredocumentform->getElement('agent_job_title')->getValue();
            $session->agent_contact_number = $configuredocumentform->getElement('agent_contact_number')->getValue();

            $isValid = $configuredocumentform->isValid($request->getPost());
            if ($request->getParam('formsubmit_email') && $isValid)
            {
                $session->sendmethod = 'email';

                $this->_redirect
                (
                    $this->getRequest()->getControllerName() . '/send-renewal-document'
                );
            }
            else if ($request->getParam('formsubmit_post') && $isValid)
            {
                $session->sendmethod = 'post';

                $this->_redirect
                (
                    $this->getRequest()->getControllerName() . '/select-document-address'
                );
            }
        }

        $templateview = new Zend_View();

        foreach ($this->view->getScriptPaths() as $scriptpath)
            $templateview->addScriptPath($scriptpath);

        // Add default template values
        $templateview->landlords_name = '[LandlordName]';
        $templateview->tenant_name = '[TenantName]';

        $templateview->agent_contact_number = '[ContactNumber]';
        $templateview->policy_premium = '[Premium]';
        $templateview->policy_term = '[Term]';

        $templateview->agent_name = $this->_agentrealname;
        $templateview->agent_job_title = '[JobTitle]';


        // Tenant name
        $rgmanager = new Manager_Insurance_Policy_Rentguarantee();
        $policydetails = $rgmanager->getPolicyDetails($session->policynumber);
        $templateview->enddate = new Zend_Date($policydetails->endDate, Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);

        $referencedetails = $rgmanager->getReference($session->policynumber);
        $templateview->tenant_name = $referencedetails->referenceSubject->name->title . ' ' .
            $referencedetails->referenceSubject->name->firstName . ' ' .
            $referencedetails->referenceSubject->name->lastName;

        if ($configuredocumentform->getElement('landlords_name')->getValue())
            $templateview->landlords_name = $configuredocumentform->getElement('landlords_name')->getValue();

        if ($configuredocumentform->getElement('agent_contact_number')->getValue())
            $templateview->agent_contact_number = $configuredocumentform->getElement('agent_contact_number')->getValue();

        if ($configuredocumentform->getElement('policy_premium')->getValue())
            $templateview->policy_premium = $configuredocumentform->getElement('policy_premium')->getValue();

        if ($configuredocumentform->getElement('policy_term')->getValue())
            $templateview->policy_term = $configuredocumentform->getElement('policy_term')->getValue();

        if ($configuredocumentform->getElement('agent_job_title')->getValue())
            $templateview->agent_job_title = $configuredocumentform->getElement('agent_job_title')->getValue();

        $viewname = $session->documenttype . '-email.phtml';
        $template = $templateview->render('rentguarantee/letters/' . $viewname);

        $this->view->configuredocumentform = $configuredocumentform;
        $this->view->document_template = $template;
    }

    /**
     * Enter the postal address to send the document to
     *
     * @return void
     */
    public function selectDocumentAddressAction()
    {
        $session = new Zend_Session_Namespace('connect_rg');
        $request = $this->getRequest();
        $documentaddress = new Connect_Form_RentGuarantee_DocumentAddress();
        $documentaddress->populate($request->getPost());

        if ($request->isPost())
        {
            if ($request->getParam('formsubmit_back'))
            {
                $this->_redirect
                (
                    $this->getRequest()->getControllerName() . '/configure-renewal-document'
                );
            }
            else if ($documentaddress->isValid($request->getPost()))
            {
                // Capture target address, obtain data from the postcode
                // lookup from the database and store the data to the session
                $postcode = new Manager_Core_Postcode();
                $postcodedata = $postcode->getPropertyByID($documentaddress->getElement('cor_address')->getValue(), false);
                $session->documentaddress = $postcodedata;

                $this->_redirect
                (
                    $this->getRequest()->getControllerName() . '/send-renewal-document'
                );
            }
        }

        $this->view->documentaddress = $documentaddress;
    }

    /**
     * Send the agents renewal invitation letter, by email, to the agent
     *
     * @return void
     */
    public function sendRenewalDocumentAction()
    {
        $session = new Zend_Session_Namespace('connect_rg');
        $this->view->agentinvite = $session->agentinvite;

        // Agents invitation
        if ($session->agentinvite == 1)
        {
            // Send agent document by email
            $params = Zend_Registry::get('params');
            $referencingServer = $params->connect->baseUrl->referencing;

            $httpclient = curl_init();
            curl_setopt($httpclient, CURLOPT_URL, $referencingServer . '/cgi-bin/premrenewal.pl?refsend=email&policynumber=' . $session->policynumber);
            curl_setopt($httpclient, CURLOPT_HEADER, 0);
            curl_setopt($httpclient, CURLOPT_RETURNTRANSFER , 1);

            $httpresponse = curl_exec($httpclient);

            curl_close($httpclient);
        }


        // Landlords documents
        if (isset($session->documenttype) && $session->documenttype != '')
        {
            // Landlords document
            $templateview = new Zend_View();

            foreach ($this->view->getScriptPaths() as $scriptpath)
                $templateview->addScriptPath($scriptpath);

            // Add default template values
            $templateview->landlords_name = '[LandlordName]';
            $templateview->tenant_name = '[TenantName]';

            $templateview->agent_contact_number = '[ContactNumber]';
            $templateview->policy_premium = '[Premium]';
            $templateview->policy_term = '[Term]';

            $templateview->agent_name = $this->_agentrealname;
            $templateview->agent_job_title = '[JobTitle]';
            $templateview->documentaddress = $session->documentaddress;

            // Tenant name
            $rgmanager = new Manager_Insurance_Policy_Rentguarantee();
            $policydetails = $rgmanager->getPolicyDetails($session->policynumber);
            $templateview->enddate = new Zend_Date($policydetails->endDate, Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);

            $referencedetails = $rgmanager->getReference($session->policynumber);
            $templateview->tenant_name = $referencedetails->referenceSubject->name->title . ' ' .
                $referencedetails->referenceSubject->name->firstName . ' ' .
                $referencedetails->referenceSubject->name->lastName;

            // Apply data from session
            $templateview->landlords_name = $session->landlords_name;
            $templateview->policy_premium = $session->policy_premium;
            $templateview->policy_term = $session->policy_term;
            $templateview->agent_job_title = $session->agent_job_title;
            $templateview->agent_contact_number = $session->agent_contact_number;

            $viewname = $session->documenttype . '-' . $session->sendmethod . '.phtml';
            $content = $templateview->render('rentguarantee/letters/' . $viewname);

            // Emailing document
            if ($session->documenttype == 'landlordinvite')
            {
                $documenttype = "invitation";
                $this->view->documenttype = 'invitation';
            }
            else if ($session->documenttype == 'landlordreminder')
            {
                $documenttype = "reminder";
                $this->view->documenttype = 'reminder';
            }

            $this->view->sendmethod = $session->sendmethod;

            // Send document
            if ($session->sendmethod == 'post')
            {
                // Post document
                // Generate document using legacy fileserver interface
                $fileserver = new Application_Document_Legacy_Fileserver();
                $filename = $fileserver->makePDF($content, 'HL');

                if ($filename !== false)
                {
                    // Retrieve and store, and delete remote pdf
                    $pdfcontent = $fileserver->storePDF($filename);
                    $fileserver->deletePDF($filename);

                    // Strip slashes
                    $filename = preg_replace('/\//', '', $filename);
                    $requesthash = preg_replace('/\.pdf/', '', $filename);

                    $params = Zend_Registry::get('params');
                    $localcache_path = null;

                    if (@isset($params->dms->localcache->directory) && $params->dms->localcache->directory != '')
                        $localcache_path = $params->dms->localcache->directory;

                    $full_filepath = realpath(APPLICATION_PATH . '/../' . $localcache_path) . '/' . $filename;

                    $fh = fopen($full_filepath, 'w+');

                    if (!is_resource($fh))
                    {
                        // Failed to open file
                        $this->render('send-renewal-document-failed');
                        return;
                    }

                    flock($fh, LOCK_EX);
                    fwrite($fh, $pdfcontent);
                    flock($fh, LOCK_UN);
                    fclose($fh);

                    // Check the homelet domain parameter is set
                    if (@isset($params->homelet->domain) && $params->homelet->domain != '')
                        $homeletdomain = $params->homelet->domain;

                    $this->view->documentpath =  $homeletdomain . '/document/?h=' . $requesthash . '&m=' . self::_generateAuthKey($requesthash);

                    $this->render('send-renewal-document');
                    return;
                }
                else
                {
                    $this->render('send-renewal-document-failed');
                    return;
                }
            }
            else if ($session->sendmethod == 'email')
            {
                // Send email and set status in to view
                $landlordsemail = $session->landlords_email;
                $this->view->landlordsemail = $session->landlords_email;

                if ($this->_emailDocumentContent($content, $documenttype, $landlordsemail, $this->_agentrealname))
                {
                    $this->render('send-renewal-document');
                    return;
                }
                else
                {
                    $this->render('send-renewal-document-failed');
                }
            }
        }
    }

    /**
     * Generate the mac key name. Must be the same function as used in the InsuranceFunctions.php
     *
     * @param string $requesthash Request hash of request
     * @return string
     * @throws Exception
     */
    private function _generateAuthKey($requesthash)
    {
        $config = Zend_Registry::get('params');
        $secret = null;

        // Capture HMAC secret key
        if (@isset($config->dms->localcache->hmacsecret) && $config->dms->localcache->hmacsecret != '')
            $secret = $config->dms->localcache->hmacsecret;

        if ($secret == null)
            throw new Exception('hmac secret not set');

        return strtoupper(Zend_Crypt_Hmac::compute($secret, 'sha256', $requesthash));
    }

    /**
     * Support method to send a template via email
     *
     * @param string $content Mail content
     * @param string $documenttype Document type name - invitation or reminder
     * @param string $to Target email address
     * @param string $agentname Agents full name
     */
    private function _emailDocumentContent($content, $documenttype, $to, $agentname)
    {
        $agentModel = new Datasource_Core_Agent_Emailaddresses();
        $agentsEmailAddresses = $agentModel->getEmailAddresses($this->_agentSchemeNumber);
        $generalEmailAddress = "";
        $rgEmailAddresss = "";

        foreach($agentsEmailAddresses as $j => $objEmail)
        {
            if($j == 0)
                $generalEmailAddress = $objEmail->emailAddress->emailAddress;

            if($objEmail->category == 4)
            {
                $rgEmailAddresss = $objEmail->emailAddress->emailAddress;
                break;
            }
        }

        if($rgEmailAddresss == "")
            $rgEmailAddresss = $generalEmailAddress;

        $agentsEmailAddress = str_replace("\r", '', $rgEmailAddresss);
        $agentsEmailAddress = str_replace("\n", '', $agentsEmailAddress);
        $agentname = str_replace("\r", '', $agentname);
        $agentname = str_replace("\n", '', $agentname);

        $mail = new Zend_Mail();
        $mail->setBodyHtml($content);
        $mail->setFrom($agentsEmailAddress, $agentname);
        $mail->addTo($to);
        $mail->setSubject("Rent Guarantee renewal $documenttype");

        return $mail->send();
    }

    /**
     * Controller action for Rent Guarantee 'Do not Renew'
     *
     * @return void
     */
    public function declineRenewalAction()
    {

        $filters = array('*' => array('StringTrim','HtmlEntities','StripTags'));
        $validators = array('*' => array('allowEmpty' => true));
        $input['policynumber'] = $this->_request->getParam('policynumber');
        $validate = new Zend_Filter_Input($filters, $validators, $input);

        $policynumber = $validate->policynumber;
        $this->view->policynumber = $policynumber;

        $rgmanager = new Manager_Insurance_Policy_Rentguarantee();

        if($rgmanager->isPolicyDeclined($policynumber))
        {
            $this->render('decline-renewal-alreadydeclined');
            return;
        }

        $declinerenewalform = new Connect_Form_RentGuarantee_DeclineRenewal();
        $declinerenewalform->applyReasonCodes($this->_fsastatusabbr);
        $declinerenewalform->populate($this->getRequest()->getPost());

        if ($this->getRequest()->isPost())
        {
            if ($declinerenewalform->getElement('formsubmit_back')->getValue())
            {
                $this->_redirect
                (
                    $this->getRequest()->getControllerName() . '/renewals'
                );
            }
            else if ($declinerenewalform->isValid($this->getRequest()->getPost())) // Valid form, process the submission
            {
                $reason = $declinerenewalform->getElement('nonrenewal_reason')->getValue();
                $why = null;

                // Choose from other text boxes, depending on reason chosen
                if ($reason == 'other_product')
                {
                    $why = $declinerenewalform->getElement('other_product')->getValue();
                    $reason = 'other_product';
                }
                else if ($reason == 'other')
                {
                    $why = $declinerenewalform->getElement('other_reason')->getValue();
                    $reason = 'other_reason';
                }
                else
                {
                    $why = $reason; // Use the reason chosen if nothing of interest in the other text boxes
                    $reason = 'nonrenewal_reason';
                }

                // Decline renewal
                $rgmanager->declinePolicyRenewal($policynumber, $reason, $why, $this->_agentrealname, 'Connect');

                // Get end date for view logic
                $policydetails = $rgmanager->getPolicyDetails($policynumber);
                $this->view->enddate = new Zend_Date($policydetails->endDate, Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);

                // Render success page
                $this->render('decline-renewal-declined');
                return;
            }
        }

        $this->view->declinerenewalform = $declinerenewalform;
    }

    /**
     * Get a particular reference from the array of references in the session.
     *
     * @return mixed
     */
    private function getReferenceFromSession($referenceNumber)
    {
        $session = new \Zend_Session_Namespace('rrp_policy_application');
        $sessionKey = sprintf('%s.rrp_references', $this->_agentSchemeNumber);
        $references = unserialize($session->{$sessionKey});

        return $references[$referenceNumber];
    }

    /**
     * Evaluate a reference against a determined set of busines criteria, determined by PolicyCriteria.
     *
     * @param RentRecoveryPlusReference $reference
     * @return bool
     */
    private function referenceSatisfiesCriteria(RentRecoveryPlusReference $reference)
    {
        $policyCriteria = new PolicyCriteria($reference, $this->getContainer()->get('rrp.utility.rrp_guarantor_reference_creator'));
        $referenceSatisfiesCriteria = $policyCriteria->isSatisfiedBy($reference);

        if ( ! $referenceSatisfiesCriteria) {
            $this->referralModel->setReferenceBasedReferralReasons($policyCriteria->getNotSatisfiedText());
        }

        return $referenceSatisfiesCriteria;
    }

    /**
     * Evaluate the application against business rules and return the result.
     *
     * @return bool
     */
    private function applicationSatisfiesCriteria()
    {
        $applicationSatisfiesCriteria = ! $this->referralModel->isReferralRequired();

        if ( ! $applicationSatisfiesCriteria) {
            $this->applicationBasedReferralReasons = $this->referralModel->getReferralReason();
        }

        return $applicationSatisfiesCriteria;
    }

    /**
     * Initialise $rateManager.
     *
     * @param RentRecoveryPlusApplication $application
     * @return \RRP\Rate\RateDecorators\RentRecoveryPlus
     */
    private function initialiseRateManager(RentRecoveryPlusApplication $application)
    {
        /** @var RRP\Rate\RateDecoratorFactory $rateDecoratorClass */
        $rateDecoratorClass = $this->getContainer()->get('rrp.rate.decorator.class');

        /** @var RRP\Rate\RateDecorators\RentRecoveryPlus $rateManager */
        $rateManager = $rateDecoratorClass::getDecorator(
            'RentRecoveryPlus',
            $this->_agentsRateID,
            $this->_params->connect->settings->rentRecoveryPlus->riskArea,
            $this->_params->connect->settings->rentRecoveryPlus->iptPercent,
            $application->getPropertyRental(),
            $application->getIsNilExcess(),
            $application->getReferenceType(),
            $application->getPolicyLength(),
            $application->getIsPayMonthly(),
            $application->getPolicyStartAt()
        );

        return $rateManager;
    }
}