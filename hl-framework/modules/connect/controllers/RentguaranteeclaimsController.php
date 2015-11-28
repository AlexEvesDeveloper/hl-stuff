<?php
require_once('ConnectAbstractController.php');
class Connect_RentguaranteeclaimsController extends ConnectAbstractController
{
    private $_stepMax = 4; // Number of form steps
    private $_claimReferenceNumber;

    public function init()
    {
        parent::init();

        // append onlineclaims javascript and style sheet
        $this->view->headLink()
                   ->appendStylesheet('/assets/vendor/jquery-cluetip/css/cluetip.css')
                   ->appendStylesheet('/assets/vendor/jquery-datepicker/css/datePicker.css');
        $this->view->headScript()->appendFile('/assets/vendor/jquery-date/js/date.js');
        $this->view->headScript()->appendFile('/assets/vendor/jquery-datepicker/js/jquery.datePicker.js');
        $this->view->headScript()->appendFile('/assets/vendor/jquery-cluetip/js/jquery.cluetip.js');
        $this->view->headScript()->appendFile('/assets/connect/js/rentguaranteeclaims/jquery.limitkeypress.min.js');
        $this->view->headScript()->appendFile('/assets/connect/js/rentguaranteeclaims/claim_form.js');
        $this->view->headScript()->appendFile('/assets/connect/js/rentguaranteeclaims/addressLookupClaims.js');

        $params = array();
        $params['url'] = $this->getRequest()->getRequestUri();
        $params['url'] = substr($params['url'],1);

        // Initiating sidebar for online claims
        $this->view->sidebar = $this->view->partial('partials/rent-guarantee-claim-sidebar.phtml');

        // Load session data into private variables
        $pageSession = new Zend_Session_Namespace('online_claims');
        if(isset($pageSession->ClaimReferenceNumber)) {
             $this->_claimReferenceNumber = $pageSession->ClaimReferenceNumber;
        }
    }

    public function indexAction()
    {
        // redirect to homepage of onlineclaims
        $this->_helper->redirector->gotoUrl('rentguaranteeclaims/home');
    }

    public function homeAction()
    {
        //Clear the session data
        $this->newAction();
        $pageForm = new Connect_Form_RentGuaranteeClaims_Home();
        //Fetch partial claim information.
        $partialClaimManager = new Manager_Insurance_RentGuaranteeClaim_Claim();

        $partialClaim = $partialClaimManager->fetchPartialClaim($this->_agentSchemeNumber);
        $this->view->partialClaim = $partialClaim;
        $this->view->pageTitle = 'Online Claims - Home';
        $this->view->form = $pageForm;
        Zend_Session::namespaceUnset('online_claims');
    }

    /**
     * Quick function that clears all cookies and re-directs to step 1. Used to force a new claim
     *
     * @params void
     * @return void
     */
    public function newAction()
    {
        Zend_Session::namespaceUnset('online_claims');
    }

    /**
     * Initialise the step 1 form
     *
     * @return void
     */
    public function step1Action() {
        $pageForm = new Connect_Form_RentGuaranteeClaims_Step1();
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 1;'
        );

        if ($this->getRequest()->isPost()) {
            // We need to validate and save the data
            $valid = $this->_formStepCommonValidate($pageForm, 1);
            $this->_formStepAgentPopulate($pageForm);

            if ($valid) {
                // Save the data and continue to next step
                $data = $pageForm->getValues();
                $claimManager = new Manager_Insurance_RentGuaranteeClaim_Claim();

                // Check to see if we have a session
                $pageSession = new Zend_Session_Namespace('online_claims');

                // Add agent to Keyhouse system if they are not already there
                $agentManager = new Manager_Core_Agent($this->_agentSchemeNumber);
                $agentManager->attemptAddToKeyhouse();

                // Claim ref number?
                if (!isset($this->_claimReferenceNumber)) {
                    // We don't have a session so we need to create a claim entry to save against
                    $claim = $claimManager->createNewClaim(
                        $this->_agentId, $this->_agentSchemeNumber
                    );

                    // Now get the reference number from the newly created claim
                    $claimReferenceNumber = $claim->getReferenceNumber();
                    $this->_claimReferenceNumber = $claimReferenceNumber;
                    $pageSession->ClaimReferenceNumber = $claimReferenceNumber;
                }
                else {
                    // We are in session so just instantiate the claim manager with the existing reference number
                    $claim = $claimManager->getClaim($this->_claimReferenceNumber, $this->_agentSchemeNumber);
                }

                $agentManager = new Manager_Core_Agent();
                $agentDetails = $agentManager->getAgent($this->_agentSchemeNumber);

                $claim->setAgentName($agentDetails->getName());
                $claim->setAgentContactName($data['agent_contact_name']);
                $claim->setAgentPostcode($data['agent_postcode']);

                $agentManager = new Manager_Core_Agent();
                $statusAbr = $agentManager->getFsaStatusCode($this->_agentSchemeNumber);
                $claim->setIsAr(in_array($statusAbr, array('NAR', 'AR')) == true ? 1 : 0);
                $claim->setIsDir(in_array($statusAbr, array('DIR')) == true ? 1 : 0);

                // get agent address
                $agentAddress = $claimManager->getPropertyAddress($data,'agent');
                $claim->setAgentHouseName($agentAddress['agent_housename']);
                $claim->setAgentStreet($agentAddress['agent_street']);
                $claim->setAgentTown($agentAddress['agent_town']);
                $claim->setAgentCity($agentAddress['agent_city']);

                $claim->setAgentTelephone($data['agent_telephone']);
                $claim->setAgentEmail($data['agent_email']);
                $claim->setLandlord1Name($data['landlord1_name']);
                $claim->setLandlordCompanyName($data['landlord_company_name']);
                $claim->setLandlordPostcode($data['landlord_postcode']);

                // get landlord address
                $landlordAddress = $claimManager->getPropertyAddress($data,'landlord');
                $claim->setLandlordHouseName($landlordAddress['landlord_housename']);
                $claim->setLandlordStreet($landlordAddress['landlord_street']);
                $claim->setLandlordTown($landlordAddress['landlord_town']);
                $claim->setLandlordCity($landlordAddress['landlord_city']);
                $claim->setLandlordAddressId($data['landlord_address']);

                $claim->setLandlordTelephone($data['landlord_telephone']);
                $claim->setLandlordEmail($data['landlord_email']);
                $claim->setSubmittedToKeyHouse(0);
                $claimManager->updateClaim($claim);

                $this->_formStepCommonNavigate(1);
                return;
            }
            else {
                // TODO: #2547 DIRTY FIX!  Contains copy and paste of some of
                //   the above code from the other side of the 'if'!
                // This is a special exception for the landlord address as
                //   incomplete or invalid forms with good landlord addresses
                //   have amnesia over the address when the page is redisplayed
                // Only do this if the landlord address is valid.
                if (
                    !$pageForm->getElement('landlord_postcode')->hasErrors() &&
                    !$pageForm->getElement('landlord_address')->hasErrors() &&
                    !$pageForm->getElement('landlord_housename')->hasErrors() &&
                    !$pageForm->getElement('landlord_street')->hasErrors() &&
                    !$pageForm->getElement('landlord_town')->hasErrors() &&
                    !$pageForm->getElement('landlord_city')->hasErrors()
                ) {
                    $data = $pageForm->getValues();
                    $claimManager = new Manager_Insurance_RentGuaranteeClaim_Claim();

                    // Check to see if we have a session
                    $pageSession = new Zend_Session_Namespace('online_claims');

                    // Claim ref number?
                    if (!isset($this->_claimReferenceNumber)) {
                        // We don't have a session so we need to create a claim entry to save against
                        $claim = $claimManager->createNewClaim(
                            $this->_agentId, $this->_agentSchemeNumber
                        );
                        // Now get the reference number from the newly created claim
                        $claimReferenceNumber = $claim->getReferenceNumber();
                        $this->_claimReferenceNumber = $claimReferenceNumber;
                        $pageSession->ClaimReferenceNumber = $claimReferenceNumber;
                    }
                    else {
                        // We are in session so just instantiate the claim manager with the existing reference number
                        $claim = $claimManager->getClaim($this->_claimReferenceNumber, $this->_agentSchemeNumber);
                    }

                    $landlordAddress = $claimManager->getPropertyAddress($data,'landlord');
                    $claim->setLandlordHouseName($landlordAddress['landlord_housename']);
                    $claim->setLandlordStreet($landlordAddress['landlord_street']);
                    $claim->setLandlordTown($landlordAddress['landlord_town']);
                    $claim->setLandlordCity($landlordAddress['landlord_city']);
                    $claim->setLandlordAddressId($data['landlord_address']);

                    $claimManager->updateClaim($claim);
                }
            }

            // Copy the landlords address id over on failed validation attempts
            $pageForm->landlord_address_id->setValue($data['landlord_address']);
        }
        else {
            $this->_formStepCommonPopulate($pageForm, 1);
        }

        // Render the page unless we have been redirected
        $this->view->form = $pageForm;
        $this->render('step1');
        $this->view->pageTitle = 'Rent Guarantee Claims - About you and Landlords';
    }

    /**
     * Initialise the step 2 form
     *
     * @return void
     */
    public function step2Action()
    {
        $pageForm = new Connect_Form_RentGuaranteeClaims_Step2();
        // Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 2;'
        );

        if ($this->getRequest()->isPost()) {
            if (isset($_POST['back'])) {
                $this->_formStepCommonNavigate(2);
                return;
            }

            // We need to validate and save the data
            $valid = $this->_formStepCommonValidate($pageForm, 2);
            $this->_formStepAgentPopulate($pageForm);
            $data = $pageForm->getValues();

            if ($valid) {
                // Instantiate the claim manager
                $claimManager = new Manager_Insurance_RentGuaranteeClaim_Claim($this->_claimReferenceNumber);
                $claim = $claimManager->getClaim($this->_claimReferenceNumber, $this->_agentSchemeNumber);
                $claim->setHousingActAdherence($data['housing_act_adherence']);
                $claim->setTenancyStartDate($data['tenancy_start_date']);
                $claim->setTenancyEndDate($data['tenancy_end_date']);
                $claim->setOriginalCoverStartDate($data['original_cover_start_date']);
                $claim->setRecentComplaints($data['recent_complaints']);

                if ($data['recent_complaints'] == 1) {
                    $claim->setRecentComplaintsDetails($data['recent_complaints_further_details']);
                }
                else {
                    $claim->setRecentComplaintsDetails(null);
                }

                // get property address
                $propertyAddress = $claimManager->getPropertyAddress($data,'tenancy');
                $claim->setTenancyAddressId($data['tenancy_address']);
                $claim->setTenancyHouseName($propertyAddress['tenancy_housename']);
                $claim->setTenancyStreet($propertyAddress['tenancy_street']);
                $claim->setTenancyTown($propertyAddress['tenancy_town']);
                $claim->setTenancyCity($propertyAddress['tenancy_city']);
                $claim->setTenancyPostcode($propertyAddress['tenancy_postcode']);
                $claim->setMonthlyRent($data['monthly_rent']);
                $claim->setDepositAmount($data['deposit_amount']);
                $claim->setRentArrears($data['rent_arrears']);

                $claim->setPolicyNumber($data['policy_number']);
                $claim->setGroundsForClaim($data['grounds_for_claim']);
                if ($data['grounds_for_claim'] == 'rent-arrears') {
                    $claim->setGroundsForClaimDetails(null);
                }
                else {
                    $claim->setGroundsForClaimDetails($data['grounds_for_claim_further_details']);
                }

                $claim->setTenantVacated($data['tenant_vacated']);

                if ($data['tenant_vacated'] == 1) {
                    if (isset($data['tenant_vacated_date']) && $data['tenant_vacated_date'] != '') {
                        $claim->setTenantVacatedDate($data['tenant_vacated_date']);
                    } else {
                        $claim->setTenantVacatedDate(null);
                    }

                    $claim->setArrearsAtVacantPossession($data['arrears_at_vacant_possession']);

                    $tenantsForwardingAddress = $claimManager->getPropertyAddress($data,'tenantsforwarding');
                    $claim->setTenantsForwardingAddressId($data['tenantsforwarding_address']);
                    $claim->setTenantsForwardingHouseName($tenantsForwardingAddress['tenantsforwarding_housename']);
                    $claim->setTenantsForwardingStreet($tenantsForwardingAddress['tenantsforwarding_street']);
                    $claim->setTenantsForwardingTown($tenantsForwardingAddress['tenantsforwarding_town']);
                    $claim->setTenantsForwardingCity($tenantsForwardingAddress['tenantsforwarding_city']);
                    $claim->setTenantsForwardingPostcode($data['tenantsforwarding_postcode']);

                    $claim->setTenantsOccupationOfPropertyConfirmedByTel(null);
                    $claim->setTenantsOccupationOfPropertyConfirmedByTelDate(null);
                    $claim->setTenantsOccupationOfPropertyConfirmedByTelContact(null);

                    $claim->setTenantsOccupationOfPropertyConfirmedByEmail(null);
                    $claim->setTenantsOccupationOfPropertyConfirmedByEmailDate(null);
                    $claim->setTenantsOccupationOfPropertyConfirmedByEmailContact(null);

                    $claim->setTenantsOccupationOfPropertyConfirmedByVisit(null);
                    $claim->setTenantsOccupationOfPropertyConfirmedByVisitDate(null);
                    $claim->setTenantsOccupationOfPropertyConfirmedByVisitContact(null);
                    $claim->setTenantsOccupationOfPropertyConfirmedByVisitIndividual(null);
                }
                else {
                    $claim->setTenantVacatedDate(null);

                    $claim->setTenantsForwardingAddressId(null);
                    $claim->setTenantsForwardingHouseName(null);
                    $claim->setTenantsForwardingStreet(null);
                    $claim->setTenantsForwardingTown(null);
                    $claim->setTenantsForwardingCity(null);
                    $claim->setTenantsForwardingPostcode(null);

                    $claim->setTenantsOccupationOfPropertyConfirmedByTel($data['tenant_occupation_confirmed_by_tel']);
                    if ($data['tenant_occupation_confirmed_by_tel']) {
                        $claim->setTenantsOccupationOfPropertyConfirmedByTelDate($data['tenant_occupation_confirmed_by_tel_dateofcontact']);
                        $claim->setTenantsOccupationOfPropertyConfirmedByTelContact($data['tenant_occupation_confirmed_by_tel_tenantname']);
                    }
                    else {
                        $claim->setTenantsOccupationOfPropertyConfirmedByTelDate(null);
                        $claim->setTenantsOccupationOfPropertyConfirmedByTelContact(null);
                    }

                    $claim->setTenantsOccupationOfPropertyConfirmedByEmail($data['tenant_occupation_confirmed_by_email']);
                    if ($data['tenant_occupation_confirmed_by_email']) {
                        $claim->setTenantsOccupationOfPropertyConfirmedByEmailDate($data['tenant_occupation_confirmed_by_email_dateofcontact']);
                        $claim->setTenantsOccupationOfPropertyConfirmedByEmailContact($data['tenant_occupation_confirmed_by_email_tenantname']);
                    }
                    else {
                        $claim->setTenantsOccupationOfPropertyConfirmedByEmailDate(null);
                        $claim->setTenantsOccupationOfPropertyConfirmedByEmailContact(null);
                    }

                    $claim->setTenantsOccupationOfPropertyConfirmedByVisit($data['tenant_occupation_confirmed_by_visit']);
                    if ($data['tenant_occupation_confirmed_by_visit']) {
                        $claim->setTenantsOccupationOfPropertyConfirmedByVisitDate($data['tenant_occupation_confirmed_by_visit_dateofvisit']);
                        $claim->setTenantsOccupationOfPropertyConfirmedByVisitContact($data['tenant_occupation_confirmed_by_visit_tenantname']);
                        $claim->setTenantsOccupationOfPropertyConfirmedByVisitIndividual($data['tenant_occupation_confirmed_by_visit_individualattending']);
                    }
                    else {
                        $claim->setTenantsOccupationOfPropertyConfirmedByVisitDate(null);
                        $claim->setTenantsOccupationOfPropertyConfirmedByVisitContact(null);
                        $claim->setTenantsOccupationOfPropertyConfirmedByVisitIndividual(null);
                    }
                }

                // Section 21 notice
                $claim->setS21NoticeServed($data['section21_served']);
                if ($data['section21_served'] == 1) {
                    $claim->setS21NoticeExpiry($data['section21_expiry']);

                    $claim->setS21NoticeMoneyDepositReceived($data['section21_moneydepositreceived']);
                    if ($data['section21_moneydepositreceived'] == 1) {
                        $claim->setS21NoticeMoneyDepositHeldUnderTdsScheme($data['section21_money_held_under_tds_deposit_scheme']);
                    }
                    else {
                        $claim->setS21NoticeMoneyDepositHeldUnderTdsScheme(null);
                    }

                    $claim->setS21NoticeTdsCompliedWith($data['section21_tds_complied_with']);
                    if ($data['section21_tds_complied_with'] == 1) {
                        $claim->setS21NoticeTdsPrescribedToTenant($data['section21_tds_prescribed_information_to_tenant']);
                        $claim->setS21NoticeLandlordDepositInPropertyForm($data['section21_landlord_deposit_in_property_form']);
                        $claim->setS21NoticePropertyReturnedAtNoticeServeDate($data['section21_returned_at_notice_serve_date']);
                    }
                    else {
                        $claim->setS21NoticeTdsPrescribedToTenant(null);
                        $claim->setS21NoticeLandlordDepositInPropertyForm(null);
                        $claim->setS21NoticePropertyReturnedAtNoticeServeDate(null);
                    }
                }
                else {
                    $claim->setS21NoticeExpiry(null);
                    $claim->setS21NoticeMoneyDepositReceived(null);
                    $claim->setS21NoticeMoneyDepositHeldUnderTdsScheme(null);
                    $claim->setS21NoticeTdsCompliedWith(null);
                    $claim->setS21NoticeTdsPrescribedToTenant(null);
                    $claim->setS21NoticeLandlordDepositInPropertyForm(null);
                    $claim->setS21NoticePropertyReturnedAtNoticeServeDate(null);
                }


                // Section 8 notice
                $claim->setS8NoticeServed($data['section8_served']);
                if ($data['section8_served'] == 1) {
                    $claim->setS8NoticeExpiry($data['section8_expiry']);
                }
                else {
                    $claim->setS8NoticeExpiry(null);
                }

                $claim->setS8NoticeDemandLetterSent($data['section8_demand_letter_sent']);
                $claim->setS8NoticeOver18Occupants($data['section8_over18_occupants']);

                $claim->setFirstArrearDate($data['first_arrear_date']);
                $claim->setDepositReceivedDate($data['deposit_received_date']);
                $claim->setTotalGuarantors($data['total_guarantors']);
                $claim->setTotalTenants($data['total_tenants']);
                $claimManager->updateClaim($claim);

                // Instantiate Guarantor Manager
                $guarantorManger = new Manager_Insurance_RentGuaranteeClaim_Guarantor();
                if ($data['total_guarantors'] !='' && $data['total_guarantors'] <= 4){
                    $guarantor = $guarantorManger->createGuarantors($data, $this->_claimReferenceNumber);
                }

                // Instantiate Tenant Manager
                $tenantManger = new Manager_Insurance_RentGuaranteeClaim_Tenant();
                if ($data['total_tenants'] !='' && $data['total_tenants'] <= 7){
                    $tenant = $tenantManger->createTenants($data, $this->_claimReferenceNumber);
                }

                // Everything has been saved ok so navigate to next step
                $this->_formStepCommonNavigate(2);
                return;
            }

            $pageForm->tenancy_address_id->setValue($data['tenancy_address']);

            if ($data['tenantsforwarding_address']) {
                $pageForm->tenantsforwarding_address_id->setValue($data['tenantsforwarding_address']);
            }
        }
        else {
            $this->_formStepCommonPopulate($pageForm, 2);
        }

        // Render the page unless we have been redirected
        $this->view->form = $pageForm;
        $this->render('step2');
        $this->view->pageTitle = 'Rent Guarantee Claims - About the tenant and property';
    }

    /**
     * Initialise the step 2 form
     *
     * @return void
     */
    public function step3Action()
    {
        $pageForm = new Connect_Form_RentGuaranteeClaims_Step3();

        $this->view->pageTitle = 'Online Claims - Step3 - Additional information and Rental payments';

        // Tell page to use AJAX validation as we go
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 3;'
        );

        if ($this->getRequest()->isPost()) {
            if (isset($_POST['back'])) {
                $this->_formStepCommonNavigate(3);
                return;
            }

            $this->_formStepAgentPopulate($pageForm);
            if ($this->_formStepCommonValidate($pageForm, 3)) {
                $data = $pageForm->getValues();

                // Instantiate the claim manager
                $claimManager = new Manager_Insurance_RentGuaranteeClaim_Claim($this->_claimReferenceNumber);
                $claim = $claimManager->getClaim($this->_claimReferenceNumber, $this->_agentSchemeNumber);
                $claim->setAdditionalInfo($data['additional_information']);

                // Claim payment bank acount details
                $claim->setClaimPaymentBankAccountName($data['dd_accountname']);
                $claim->setClaimPaymentBankAccountNumber($data['bank_account_number']);
                $claim->setClaimPaymentBankAccountSortCode($data['bank_sortcode_number']);

                $claimManager->updateClaim($claim);

                // TODO: The following code for the saving of the rent schedule
                //   data as a supporting doc needs to be moved into a manager,
                //   not be performed by a controller.

                // Get ready for saving arrears data as a supporting document
                $supportManager = new Manager_Insurance_RentGuaranteeClaim_SupportingDocument(
                    $this->_claimReferenceNumber,
                    $this->_agentSchemeNumber
                );
                // Prepare upload directories
                $supportManager->prepareUploadDirs();

                // Save the arrears data into a supporting document file
                $manager = new Manager_Insurance_RentGuaranteeClaim_RentalPayment();
                $supportManagerDs = new Datasource_Insurance_RentGuaranteeClaim_SupportingDocuments();
                $paymentDs = new Datasource_Insurance_RentGuaranteeClaim_RentalPayment();
                // Encode data
                $paymentDataEncoded = $paymentDs->processToCsv($this->_claimReferenceNumber);
                // Save data
                $path = $supportManager->getPath() . 'Rent_Schedule.csv'; // TODO: parameterise filename
                file_put_contents($path, $paymentDataEncoded);
                // Note saved data in DB if not already in it
                $files = $supportManagerDs->getByReferenceNumber($this->_claimReferenceNumber);
                $csvInDb = false;
                foreach($files as $file) {
                    if ($file->type == 'rent_schedule' && $file->name == 'Rent_Schedule.csv') {
                        $csvInDb = true;
                        break 1;
                    }
                }
                if (!$csvInDb) {
                    $shortPath = substr(
                        $path,
                        strlen(APPLICATION_PATH . '/../private/uploads/') // TODO: use parameterised path
                    );
                    $supportManagerDs->addSupportingDocument($this->_claimReferenceNumber, 'rent_schedule', $shortPath);
                }

                // Everything has been saved ok so navigate to next step
                $this->_formStepCommonNavigate(3);
                return;
            }
        }
        else {
            $this->_formStepCommonPopulate($pageForm, 3);
        }

        // Render the page unless we have been redirected
        $this->view->form = $pageForm;
        $this->render('step3');
        $this->view->pageTitle = 'Online Claims - Step3 - Additional Information and Rental Payments';
    }

    /**
     * Initialize the step 4 form
     *
     * @return void
     */
    public function step4Action()
    {
        $pageForm = new Connect_Form_RentGuaranteeClaims_Step4();
        $this->view->headScript()->appendScript(
            'var ajaxValidate = true; var ajaxValidatePage = 4;'
        );
        $supportManager = new Manager_Insurance_RentGuaranteeClaim_SupportingDocument(
            $this->_claimReferenceNumber,
            $this->_agentSchemeNumber
        );
        // Prepare upload directories
        $supportManager->prepareUploadDirs();

        if ($this->_request->isPost()) {
            // We need to validate the data
            $this->_formStepAgentPopulate($pageForm);
            $valid = $this->_formStepCommonValidate($pageForm, 4);

            if ($valid) {
                $request = $this->getRequest();
                $mode = $request->getParam('hd_type');
                $agentName = $request->getParam('doc_confirmation_agent_name');
                $landlordPropertyProprietor = $request->getParam('landlord_proprietor_of_property');
                $authConfirmation = $request->getParam('chk_confirm');
                $decConfirmation = $request->getParam('dec1_confirm');

                //update confirmation agent name
                $claimManager = new Manager_Insurance_RentGuaranteeClaim_Claim($this->_claimReferenceNumber);
                $claim = $claimManager->getClaim($this->_claimReferenceNumber, $this->_agentSchemeNumber);
                $claim->setDocConfirmationAgentName($agentName);
                $claim->setLandlordIsPropertyProprietor($landlordPropertyProprietor);
                $claim->setAuthorityConfirmed($authConfirmation);
                $claim->setDeclarationConfirmed($decConfirmation);

                if($mode == 2) { // submit claim details to KEYHOUSE DB
                    // Mark claim as at stage 1 (claim submitted but not transferred)
                    $claim->setDataComplete(1);
                    $claimManager->updateClaim($claim);

                    //Submit claim data to KEYHOUSE DB
                    $keyhouseManager = new Manager_Insurance_KeyHouse_Claim();
                    $keyhouseManager->submitClaim($this->_claimReferenceNumber);

                    // Display success message
                    $this->submitclaimAction($this->_claimReferenceNumber,'s');
                    return;
                } else {
                    // Mark claim as data incomplete
                    $claim->setDataComplete(0);
                    $claimManager->updateClaim($claim);
                    $this->_helper->redirector->gotoUrl('rentguaranteeclaims/saveclaim');
                }
            }
        }
        else {
            $this->_formStepCommonPopulate($pageForm, 4);
        }

        // Render the page unless we have been redirected
        $this->view->form = $pageForm;
        $this->view->pageTitle = 'Online Claims - Step4 - Supporting Documents';
        $this->view->headLink()->appendStylesheet('/assets/connect/css/rentguaranteeclaims_supporting_documents.css');
        $this->view->headLink()->appendStylesheet('/assets/vendor/connect/css/jquery.fileupload-ui.css');
        //Get Available document types
        $documentTypes = $supportManager->getDocumentTypes();
        $this->view->document_types = $documentTypes;
        $this->render('step4');
    }

   /**
    *   To display success message after made an entry with Key House Database
    *
    *   @return void
    */
    public function submitclaimAction($claimReferenceNum='', $claimStatus=null)
    {
        // OC Submit Claim Page
        $this->view->pageTitle = 'Online Claim Suite - Submit Claim';
        $pageForm = new Connect_Form_RentGuaranteeClaims_SubmitClaim();
        $this->view->status = $claimStatus;
        $this->view->ref_num = $claimReferenceNum;
        // Render the page unless we have been redirected
        $this->view->form = $pageForm;
        $this->render('submitclaim');
    }

    /**
    *   To display success message after made an entry with Connect - Rent Guarantee Database
    *
    *   @return void
    */
    public function saveclaimAction()
    {
        // OC Submit Claim Page
        $this->view->pageTitle = 'Online Claim Suite - Save Claim';
    }

    /**
    * To print the claim details in the fax header
    *
    * @return void
    */
    public function printFaxHeaderAction()
    {
        // Disable default layout/views/headers
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->getHelper('layout')->disableLayout();
        /* // This is the correct code, but over HTTPS IE doesn't like it.
        $this->getResponse()
            ->setHeader('Pragma', 'public') // required
            ->setHeader('Expires', '0')
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->setHeader('Cache-Control', 'private', false) // required for certain browsers
            ->setHeader('Content-Disposition', 'inline; filename=fax-header.pdf')
            ->setHeader('Content-type', 'application/pdf');
        */
        // This is the dirty way of doing it, but it works in IE.  IE sucks.
        header('Pragma: public'); // required
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false); // required for certain browsers
        header('Content-Disposition: inline; filename=fax-header.pdf');
        header('Content-type: application/pdf');

        if ($this->_request->isPost()) {
            $refNum = $this->_request->getParam('ref_num');

            if($refNum !='') {
                $claimManager = new Manager_Insurance_RentGuaranteeClaim_Claim();
                $FaxHeaderPath = $claimManager->populateAndOutputClaimFaxHeader($refNum);
                $claimManager->garbageCollect();
            } else {
                $this->_helper->redirector->gotoUrl('rentguaranteeclaims/home');
            }
        }
    }

    /**
    *   To print the claim details
    *
    *   @return void
    */
    public function printClaimAction()
    {
        $this->view->headLink()->appendStylesheet('/assets/connect/css/print.css', 'print');

        $this->view->pageTitle = 'Online Claim Suite - Print Claim';
        if ($this->_request->isPost()) {
            $refNum = $this->_request->getParam('ref_num');
            $mode = $this->_request->getParam('mode');
        } else {
            $refNum = $this->_claimReferenceNumber;
            $mode = "print";
        }
        if($refNum !='') {
            $claimManager = new Manager_Insurance_RentGuaranteeClaim_Claim();
            $rentalPaymentManager = new Manager_Insurance_RentGuaranteeClaim_RentalPayment();

            $claimData = $claimManager->getClaim($refNum, $this->_agentSchemeNumber);
            $rentPayments = $rentalPaymentManager->getRentalPayments($refNum);

            $this->view->claim_data = $claimData;
            $this->view->agent_scheme_number = $this->_agentSchemeNumber;
            //Get guarantors
            $this->view->guarantors = $claimManager->getGuarantorsByReferenceNumber($refNum);
            //Get Tenants
            $this->view->tenants = $claimManager->getTenantsByReferenceNumber($refNum);
            //Get Rent Payments
            $this->view->rent_payments = $rentPayments['data'];
            //Get Supporting Documents
            $this->view->documents = $claimManager->getSupportingDocumentsByReferenceNumber($refNum);
            $this->view->mode = $mode;
        } else {
            $this->_helper->redirector->gotoUrl('rentguaranteeclaims/home');
        }
    }

    /**
     *   To download existing stored supporting document
     *
     *   @return void
     */
    public function downloadAction()
    {
        $docId = $this->_request->getParam('d');
        $claim_reference_number = $this->_request->getParam('crn');
        // Check that this ASN owns this claim reference number and document ID
        //   for security before serving up file
        $claimManager = new Manager_Insurance_RentGuaranteeClaim_Claim();
        $claim = $claimManager->getClaim($claim_reference_number, $this->_agentSchemeNumber);
        if ($claim->getAgentSchemeNumber() == $this->_agentSchemeNumber) {
            // This claim is owned by this agent, check doc is owned by this claim
            $supportManager = new Manager_Insurance_RentGuaranteeClaim_SupportingDocument(
                $claim_reference_number,
                $this->_agentSchemeNumber
            );
            $documentList = $supportManager->getSupportingDocumentList();
            $documentInList = false;
            foreach($documentList as $document) {
                if ($document->id == $docId) {
                    $documentInList = true;
                    break 1;
                }
            }
            if ($documentInList) {
                // Doc is "owned" by this claim, serve it up
                if(!empty($claim_reference_number)) {
                    $supportManager->downloadSupportingDocument($docId);
                }
            }
        }
        exit;
    }

    /**
     *   To delete the claims
     *
     *   @return void
     */
    public function deleteClaimAction()
    {
        if ($this->_request->isPost()) {
            $referenceNumber = $this->_request->getParam('reference_num');
            $claimManager = new Manager_Insurance_RentGuaranteeClaim_Claim();
            $claimManager->deleteClaim($referenceNumber,$this->_agentSchemeNumber);
        }
        $this->_helper->redirector->gotoUrl('rentguaranteeclaims/home');
    }

    // TODO:    Need to re-factor
    public function deletefileAction()
    {
        $this->_helper->layout->disableLayout();    //disable layout
        $this->_helper->viewRenderer->setNoRender(); //suppress auto-rendering
        if ($this->_request->isPost()) {
            $filename = $this->_request->getParam('file_name');
            $docId = $this->_request->getParam('doc_id');
            $supportManager = new Manager_Insurance_RentGuaranteeClaim_SupportingDocument();
            $supportManager->deleteSupportingDocument($this->_agentSchemeNumber, $this->_claimReferenceNumber, $docId);
            $arrDocuments = $supportManager->getByReferenceNumber($this->_claimReferenceNumber)->toArray();
            $res = "";
            if(count($arrDocuments)>0)
                $documentTypes = $supportManager->getDocumentTypes();
                $resDocuments = array();
                foreach($arrDocuments as $documents) {
                    $resDocuments[$documents['supporting_document_name']][] = $documents;
                }
                foreach($resDocuments as $type => $documents) {
                    $res .= "<li><b>".$documentTypes[$type]."</b></li>";
                    foreach($documents as $document) {
                        $name = basename($document['attachment_filename']);
                        $filePath = "download?d=".$document['id']."&file=".$name;
                        $res .= '<li><img src="/assets/connect/images/cross.gif" onclick=fnDeleteFile("'.str_replace(" ","@@@",$name).'",'.$document['id'].') height="10" title="Delete" align="top"/> <a href="'.$filePath.'" target="_blank" title="'.$name.'">'.substr($name,0,32);
                        if(strlen($name)>32) {
                            $res .= "..";
                        }
                        $res .= "</a></li>";
                    }
                    $res .= "<li><div style='height:10px'></div></li>";
                }
            echo $res;
            exit;
        }
    }

    /**
     *   Display all the claims in the grid available for the agent scheme number
     *
     *   @return void
     */
    public function viewClaimsAction()
    {
        $this->view->pageTitle = 'Claim Summary';
        $pageForm = new Connect_Form_RentGuaranteeClaims_ViewClaims();
        $agentNumber = $this->_agentSchemeNumber;
        $keyhouseManager = new Manager_Insurance_KeyHouse_Claim();
        $this->view->claims = $keyhouseManager->getOpenClaims($agentNumber);
        $this->view->initialClaimStatus = strtolower($this->_params->connect->rentguaranteeclaims->initialClaimStatus);
        // Render the page unless we have been redirected
        $this->view->form = $pageForm;
    }

    public function claimPendingAction()
    {
        $this->_helper->layout->disableLayout();
    }

    /**
     *   To display claims in detail
     *
     *   @return void
     */
    public function claimDetailsAction()
    {
        //  die("here");
        if ($this->_request->isPost()) {
            $pageForm = new Connect_Form_RentGuaranteeClaims_ClaimDetails();
            $filters = array('*' => array('StringTrim','HtmlEntities','StripTags'));
            $validators = array('*' => array('allowEmpty' => true));
            $input['claimRefNo'] = $this->getRequest()->getParam('claimNumber');
            
            $validate = new Zend_Filter_Input($filters, $validators, $input);
            $claimRefNo = $validate->getEscaped('claimRefNo');
            $keyHouseManager = new Manager_Insurance_KeyHouse_Claim();
            $claimDetails = $keyHouseManager->getClaim(
                $claimRefNo, $this->_agentSchemeNumber
            );
            if(empty($claimDetails)) {
                $this->_helper->redirector->gotoUrl('rentguaranteeclaims/no-claim-data');
            }
            $this->view->pageTitle = 'Online Claim Suite - Claim Details';
            $this->view->latestDetail = $claimDetails[count($claimDetails) - 1];
            $this->view->claimsDetails = $claimDetails;
            $this->view->claimNumber = $claimRefNo;
            $this->view->form = $pageForm;
        } else  {
            $this->_helper->redirector->gotoUrl('rentguaranteeclaims/home');
        }
    }

    /**
     *   Send a message to email handler
     *
     *   @return void
     */
    public function sendMessageAction()
    {
        $pageForm = new Connect_Form_RentGuaranteeClaims_SendHandlerMessage();
        $filters = array('*' => array('StringTrim','HtmlEntities','StripTags'));
        $validators = array('*' => array('allowEmpty' => true));
        $input['claimRefNo'] = $this->_request->getParam('claimNumber');
        $validate = new Zend_Filter_Input($filters, $validators, $input);

        $claimRefNo = $validate->getEscaped('claimRefNo');
        $request = $this->getRequest();
        $formData = $request->getPost();
        $this->view->message = '';
        if (isset($formData['send'])) {
            $claimRefNo =   $formData['claimNumber'];
            $content    =   $formData['message'];
            $documents = new Zend_File_Transfer_Adapter_Http();
            $attachDocuments = $documents->getFileInfo();
            $attachmentCount = 0;
            $attachmentSize = 0;
            // remove slash (/) from claim number to create error free
            // directory for temporary attachments
            $claimRefDir =   explode('/',$claimRefNo);
            $claimManager = new Manager_Insurance_RentGuaranteeClaim_Claim();
            $agentManager = new Datasource_Core_Agent_UserAccounts();
            $agentManager = $agentManager->getUser(
                $this->_agentId,
                $this->_agentUserName,
                $this->_agentSchemeNumber
            );

            // validate the form before sending email
            if($pageForm->isValid($formData)) {
                // check the attachement
                if(count($attachDocuments) >= 1) {
                    foreach($attachDocuments as $handlerAttachment) {
                        if($handlerAttachment['name'] !='') {
                            $claimManager->addAttachments(
                                $claimRefDir[1],
                                $this->_agentSchemeNumber
                            );
                            $attachmentCount++;
                        }
                        $attachmentSize += $handlerAttachment['size'];
                    }
                }
                if ($attachmentCount > 0) {
                    $claimManager->notifyEmailHandlerWithAttachments(
                        $claimRefNo,
                        $content,
                        $this->_agentSchemeNumber,
                        1,
                        $agentManager->email->emailAddress
                    );
                    $claimManager->deleteAttachments(
                        $claimRefDir[1],
                        $this->_agentSchemeNumber
                    );
                    // Redirect to confirmation page, the redirect prevents
                    // multiple submissions if user refreshes browser
                    $this->_helper->redirector->gotoUrl('rentguaranteeclaims/callmesent');
                    return;
                } else {
                    $claimManager->notifyEmailHandlerWithAttachments(
                        $claimRefNo,
                        $content,
                        $this->_agentSchemeNumber,
                        0,
                        $agentManager->email->emailAddress
                    );
                    $this->view->message = 'Message sent successfully';
                    $this->_helper->redirector->gotoUrl('rentguaranteeclaims/callmesent');
                    return;
                }
            }
        }
        $this->view->headScript()->appendFile('/assets/vendor/jquery-form/js/jquery.form.js');
        $this->view->headScript()->appendFile('/assets/vendor/jquery-metadata/js/jquery.MetaData.js');
        $this->view->headScript()->appendFile('/assets/vendor/jquery-multifile/js/jquery.MultiFile.js');
        if(empty($claimRefNo)) {
            $this->_helper->redirector->gotoUrl('rentguaranteeclaims/view-claims');
        }
        $this->view->claimNumber   =  $claimRefNo;
        $this->view->pageTitle = 'Online Claim Suite - View Claims - Send Message';
        $this->view->form = $pageForm;
    }

    /**
     * Print Claim Status Report in PDF format
     *
     * @return void
     */
    public function claimStatusAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        /* // This is the original code, but over HTTPS IE doesn't like it.
        $this->getResponse()
                 ->setHeader('Content-Disposition', 'inline;')
                 ->setHeader('Content-type', 'application/pdf');
        */
        // This is the dirty way of doing it, but it works in IE.  IE sucks.
        header('Pragma: public'); // required
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false); // required for certain browsers
        header('Content-Disposition: inline;');
        header('Content-type: application/pdf');

        $claimRefNo = $this->_request->getParam('claimNumber');

        $claimManager = new Manager_Insurance_KeyHouse_Claim();

        if($claimRefNo != '') {
            echo $claimManager->populateAndOuputClaimStatusReport($claimRefNo,$this->_agentSchemeNumber);
        } else {
            $this->_helper->redirector->gotoUrl('rentguaranteeclaims/view-claims');
        }
    }

    public function noClaimDataAction()
    {
        $this->view->pageTitle = 'Online Claim Suite - No data available';
    }

    /**
     * Call back request successful screen
     *
     * @return void
     */
    public function callmesentAction()
    {
        $this->view->pageTitle = 'Online Claim Suite - View Claims - Send Message';
    }

    private function _formStepAgentPopulate($pageForm)
    {
        $agentManager = new Manager_Core_Agent();
        $agentDetails = $agentManager->getAgent($this->_agentSchemeNumber);

        $formData = array();

        $formData['agent_schemenumber'] = $this->_agentSchemeNumber;
        $formData['agent_name'] = $agentDetails->getName();

        $agentManager = new Manager_Core_Agent();
        $statusAbr = $agentManager->getFsaStatusCode($this->_agentSchemeNumber);

        $formData['agent_ar_by_barbon'] = in_array($statusAbr, array('NAR', 'AR')) == true ? 'Yes' : 'No';
        $formData['agent_dir_by_fca'] = in_array($statusAbr, array('DIR')) == true ? 'Yes' : 'No';

        $pageForm->populate($formData);
    }

    /**
     * Helper function to populate the zend form elements with database data
     *
     * @param Zend_Form $pageForm form definition for this step
     * @param int $stepNum current step number
     *
     * @return void
     */
    private function _formStepCommonPopulate($pageForm, $stepNum)
    {
        $pageSession = new Zend_Session_Namespace('online_claims');
        // First of all check that this form should be viewable and the user isn't trying to skip ahead

        $this->view->stepNum = $stepNum;
        $this->view->stepMax = $this->_stepMax;

        // Check to see if the user is trying to skip ahead in the claim
        $tooFarAhead = false;
        $lastCompleted=1;
        if ((!isset($pageSession->completed) || is_null($pageSession->completed)) && $stepNum != 1) {
            $tooFarAhead = true;
            $lastCompleted = 1;
        } elseif ($stepNum > 1) {
            // Check to see if any pages previous to the one the user's trying to get to are incomplete
            $tooFarAhead = false;
            for ($i = 1; $i < $stepNum; $i++) {
                if (!isset($pageSession->completed[$i]) || !$pageSession->completed[$i]) {
                    $tooFarAhead = true;
                    $lastCompleted = $i;
                    break;
                }
            }
        }

        if ($tooFarAhead) {
            // Drop user onto page that needs completing
            $response = $this->getResponse();
            $response->setRedirect('/rentguaranteeclaims/step' . ($lastCompleted));
            $response->sendResponse();
            return false;
        }

        $formData = array();

        $agentManager = new Manager_Core_Agent();
        $agentDetails = $agentManager->getAgent($this->_agentSchemeNumber);

        // Populate the agents details
        $formData['agent_schemenumber'] = $this->_agentSchemeNumber;
        $formData['agent_name'] = $agentDetails->getName();
        $formData['agent_postcode']    = $agentDetails->contact[0]->address->getPostCode();
        $formData['agent_housename'] = $agentDetails->contact[0]->address->getHouseName();

        $agentStreet = $agentDetails->contact[0]->address->getAddressLine1();
        if ($agentStreet) $agentStreet .= $agentStreet . ', ';
        $agentStreet .= $agentDetails->contact[0]->address->getAddressLine2();

        $formData['agent_street'] = $agentStreet;
        $formData['agent_town'] = $agentDetails->contact[0]->address->getTown();
        $phones = $agentDetails->contact[0]->phoneNumbers->getTelephone();
        $formData['agent_telephone'] = $phones['telephone1'];
        $formData['agent_email'] = $agentManager->getEmailAddressByCategory( Model_Core_Agent_EmailMapCategory::GENERAL);

        $agentManager = new Manager_Core_Agent();
        $statusAbr = $agentManager->getFsaStatusCode($this->_agentSchemeNumber);

        $formData['agent_ar_by_barbon'] = in_array($statusAbr, array('NAR', 'AR')) == true ? 'Yes' : 'No';
        $formData['agent_dir_by_fca'] = in_array($statusAbr, array('DIR')) == true ? 'Yes' : 'No';

        if (isset($pageSession->ClaimReferenceNumber)) {
            // Only populate from DB if we are in session and have a reference number
            $claimReferenceNumber = $pageSession->ClaimReferenceNumber;
            // Populate $formData with data from model, if available
            $claimManager = new Manager_Insurance_RentGuaranteeClaim_Claim();
            $claim = $claimManager->getClaim($claimReferenceNumber, $this->_agentSchemeNumber);

            // Override agents data with data from claim if available
            if ($claim->getAgentHousename() != '') {
                $formData['agent_housename'] = $claim->getAgentHousename();
            }

            if ($claim->getAgentStreet() != '') {
                $formData['agent_street'] = $claim->getAgentStreet();
            }

            if ($claim->getAgentTown() != '') {
                $formData['agent_town'] = $claim->getAgentTown();
            }

            if ($claim->getAgentPostcode() != '') {
                $formData['agent_postcode'] = $claim->getAgentPostcode();
            }

            if ($claim->getAgentTelephone() != '') {
                $formData['agent_telephone'] = $claim->getAgentTelephone();
            }

            if ($claim->getAgentEmail() != '') {
                $formData['agent_email'] = $claim->getAgentEmail();
            }

            switch ($stepNum) {
                case 1:
                    // You and your Landlord section
                    $formData['agent_contact_name']        =    $claim->getAgentContactName();
                    $formData['landlord1_name']            =    $claim->getLandlord1Name();
                    $formData['landlord_company_name']    =    $claim->getLandlordCompanyName();
                    $formData['landlord_postcode']        =    $claim->getLandlordPostcode();
                    $formData['landlord_address_id']    =    $claim->getLandlordAddressId();
                    $formData['landlord_address']        =    $claim->getLandlordAddressId();
                    $formData['landlord_housename']        =    $claim->getLandlordHouseName();
                    $formData['landlord_street']        =    $claim->getLandlordStreet();

                    $formData['landlord_city']            =    $claim->getLandlordCity();
                    $formData['landlord_town']            =    $claim->getLandlordTown();
                    $formData['landlord_telephone']        =    $claim->getLandlordTelephone();
                    $formData['landlord_email']            =    $claim->getLandlordEmail();

                    $pageForm->isValid($formData);
                    break;

                case 2:
                    if ((isset($pageSession->completed[$stepNum]) && $pageSession->completed[$stepNum])
                       || (isset($pageSession->identifier[$stepNum]) && $pageSession->identifier[$stepNum])) {
                            //set step2 identifier
                            $pageSession->identifier[$stepNum] = true;

                            // get tenant and property details
                            $formData['housing_act_adherence']        =    $claim->getHousingActAdherence();
                            $formData['tenancy_start_date']            =    $claim->getTenancyStartDate();
                            $formData['tenancy_end_date']        =    $claim->getTenancyEndDate();
                            $formData['original_cover_start_date']    =    $claim->getOriginalCoverStartDate();
                            $formData['monthly_rent']               =    $claim->getMonthlyRent();
                            $formData['tenancy_address_id']               =    $claim->getTenancyAddressId();
                            $formData['tenancy_postcode']           =    $claim->getTenancyPostcode();
                            $formData['tenancy_housename']           =    $claim->getTenancyHouseName();
                            $formData['tenancy_street']                   =    $claim->getTenancyStreet();
                            $formData['tenancy_town']                =    $claim->getTenancyTown();
                            $formData['tenancy_city']               =    $claim->getTenancyCity();
                            $formData['tenancy_postcode']              =    $claim->getTenancyPostcode();
                            $formData['tenancy_address']            =    $claim->getTenancyAddress();
                            $formData['tenancy_housename']            =    $claim->getTenancyHouseName();
                            $formData['tenancy_street']                =    $claim->getTenancyStreet();
                            $formData['tenancy_town']                =    $claim->getTenancyTown();
                            $formData['tenancy_city']                =    $claim->getTenancyCity();
                            $formData['deposit_amount']               =    $claim->getDepositAmount();
                            $formData['rent_arrears']               =    $claim->getRentArrears();
                            $formData['tenant_vacated']               =    $claim->getTenantVacated();
                            $formData['tenant_vacated_date']        =    $claim->getTenantVacatedDate();
                            $formData['first_arrear_date']           =    $claim->getFirstArrearDate();
                            $formData['deposit_received_date']        =    $claim->getDepositReceivedDate();

                            $formData['recent_complaints']                                  = $claim->getRecentComplaints();
                            $formData['recent_complaints_further_details']                  = $claim->getRecentComplaintsDetails();

                            $formData['policy_number']                                      = $claim->getPolicyNumber();
                            $formData['grounds_for_claim']                                  = $claim->getGroundsForClaim();
                            $formData['grounds_for_claim_further_details']                  = $claim->getGroundsForClaimDetails();

                            $formData['arrears_at_vacant_possession']                       = $claim->getArrearsAtVacantPossession();

                            $formData['tenantsforwarding_address_id']                       = $claim->getTenantsForwardingAddressId();
                            $formData['tenantsforwarding_housename']                        = $claim->getTenantsForwardingHouseName();
                            $formData['tenantsforwarding_street']                           = $claim->getTenantsForwardingStreet();
                            $formData['tenantsforwarding_town']                             = $claim->getTenantsForwardingTown();
                            $formData['tenantsforwarding_city']                             = $claim->getTenantsForwardingCity();
                            $formData['tenantsforwarding_postcode']                         = $claim->getTenantsForwardingPostcode();

                            $formData['tenant_occupation_confirmed_by_tel']                 = $claim->getTenantsOccupationOfPropertyConfirmedByTel();
                            $formData['tenant_occupation_confirmed_by_tel_dateofcontact']   = $claim->getTenantsOccupationOfPropertyConfirmedByTelDate();
                            $formData['tenant_occupation_confirmed_by_tel_tenantname']      = $claim->getTenantsOccupationOfPropertyConfirmedByTelContact();
                            $formData['tenant_occupation_confirmed_by_email']               = $claim->getTenantsOccupationOfPropertyConfirmedByEmail();
                            $formData['tenant_occupation_confirmed_by_email_dateofcontact'] = $claim->getTenantsOccupationOfPropertyConfirmedByEmailDate();
                            $formData['tenant_occupation_confirmed_by_email_tenantname']    = $claim->getTenantsOccupationOfPropertyConfirmedByEmailContact();
                            $formData['tenant_occupation_confirmed_by_visit']               = $claim->getTenantsOccupationOfPropertyConfirmedByVisit();
                            $formData['tenant_occupation_confirmed_by_visit_dateofvisit']   = $claim->getTenantsOccupationOfPropertyConfirmedByVisitDate();
                            $formData['tenant_occupation_confirmed_by_visit_individualattending'] = $claim->getTenantsOccupationOfPropertyConfirmedByVisitIndividual();
                            $formData['tenant_occupation_confirmed_by_visit_tenantname']    = $claim->getTenantsOccupationOfPropertyConfirmedByVisitContact();

                            $formData['section21_served']                                   = $claim->getS21NoticeServed();
                            $formData['section21_expiry']                                   = $claim->getS21NoticeExpiry();
                            $formData['section21_moneydepositreceived']                     = $claim->getS21NoticeMoneyDepositReceived();
                            $formData['section21_money_held_under_tds_deposit_scheme']      = $claim->getS21NoticeMoneyDepositHeldUnderTdsScheme();
                            $formData['section21_tds_complied_with']                        = $claim->getS21NoticeTdsCompliedWith();
                            $formData['section21_tds_prescribed_information_to_tenant']     = $claim->getS21NoticeTdsPrescribedToTenant();
                            $formData['section21_landlord_deposit_in_property_form']        = $claim->getS21NoticeLandlordDepositInPropertyForm();
                            $formData['section21_returned_at_notice_serve_date']            = $claim->getS21NoticePropertyReturnedAtNoticeServeDate();

                            $formData['section8_served']                                    = $claim->getS8NoticeServed();
                            $formData['section8_expiry']                                    = $claim->getS8NoticeExpiry();
                            $formData['section8_demand_letter_sent']                        = $claim->getS8NoticeDemandLetterSent();
                            $formData['section8_over18_occupants']                          = $claim->getS8NoticeOver18Occupants();

                            // get guarantor details
                            $guarantorManager =    new Manager_Insurance_RentGuaranteeClaim_Guarantor();
                            $getGuarantorInfo =    $guarantorManager->getGuarantors($claimReferenceNumber);
                            $formData['total_guarantors'] = count($getGuarantorInfo);
                            $formData['totalguarantors'] = count($getGuarantorInfo);

                            $createDynamicGuarantorElement=1;
                            foreach($getGuarantorInfo as $setGuarantorInfo)    {
                                Application_Core_FormUtils::createManualAddressInput(
                                    $pageForm,
                                    'guarantor_housename_' . $createDynamicGuarantorElement,
                                    'guarantor_street_' . $createDynamicGuarantorElement,
                                    'guarantor_town_' . $createDynamicGuarantorElement,
                                    'guarantor_city_' . $createDynamicGuarantorElement
                                );

                                $formData['guarantor_name_'.$createDynamicGuarantorElement]         = $setGuarantorInfo['guarantor_name'];
                                $formData['guarantor_hometelno_'.$createDynamicGuarantorElement]    = $setGuarantorInfo['hometelno'];
                                $formData['guarantor_worktelno_'.$createDynamicGuarantorElement]    = $setGuarantorInfo['worktelno'];
                                $formData['guarantor_mobiletelno_'.$createDynamicGuarantorElement]  = $setGuarantorInfo['mobiletelno'];
                                $formData['guarantor_email_'.$createDynamicGuarantorElement]        = $setGuarantorInfo['email'];
                                $formData['guarantors_dob_'.$createDynamicGuarantorElement]         = date('d/m/Y',strtotime($setGuarantorInfo['dob']));
                                $formData['guarantor_homeletrefno_'.$createDynamicGuarantorElement] = $setGuarantorInfo['homeletrefno'];

                                $formData['guarantor_housename_'.$createDynamicGuarantorElement]    = $setGuarantorInfo['house_name'];
                                $formData['guarantor_street_'.$createDynamicGuarantorElement]       = $setGuarantorInfo['street'];
                                $formData['guarantor_town_'.$createDynamicGuarantorElement]         = $setGuarantorInfo['town'];
                                $formData['guarantor_city_'.$createDynamicGuarantorElement]         = $setGuarantorInfo['city'];
                                $formData['guarantor_postcode_'.$createDynamicGuarantorElement]     = $setGuarantorInfo['postcode'];
                                $formData['guarantor_address_'.$createDynamicGuarantorElement]      = $setGuarantorInfo['address_id'];

                                $createDynamicGuarantorElement++;
                            }

                            // get tenant details
                            $tenantManager                  = new Manager_Insurance_RentGuaranteeClaim_Tenant();
                            $getTenantInfo                  = $tenantManager->getTenants($claimReferenceNumber);
                            $formData['total_tenants']      = count($getTenantInfo);
                            $formData['totaltenants']       = count($getTenantInfo);
                            $createDynamicTenantElement     = 1;

                            foreach ($getTenantInfo as $setTenantInfo) {
                                $formData['tenant_name_'.$createDynamicTenantElement]        = $setTenantInfo['tenant_name'];
                                $formData['tenant_hometelno_'.$createDynamicTenantElement]   = $setTenantInfo['tenant_hometelno'];
                                $formData['tenant_worktelno_'.$createDynamicTenantElement]   = $setTenantInfo['tenant_worktelno'];
                                $formData['tenant_mobiletelno_'.$createDynamicTenantElement] = $setTenantInfo['tenant_mobiletelno'];
                                $formData['tenant_email_'.$createDynamicTenantElement]       = $setTenantInfo['tenant_email'];
                                $formData['tenants_dob_'.$createDynamicTenantElement]        = date('d/m/Y',strtotime($setTenantInfo['tenant_dob']));
                                $formData['rg_policy_ref_'.$createDynamicTenantElement]      = $setTenantInfo['rg_policy_ref'];

                                $createDynamicTenantElement++;
                            }

                        $pageForm->isValid($formData);
                    }
                    break;

                case 3:
                    if ((isset($pageSession->completed[$stepNum]) && $pageSession->completed[$stepNum])
                       || (isset($pageSession->identifier[$stepNum]) && $pageSession->identifier[$stepNum])) {
                        //set step3 identifier
                        $pageSession->identifier[$stepNum] = true;
                        $formData['additional_information'] = $claim->getAdditionalInfo();
                        $formData['dd_accountname'] = $claim->getClaimPaymentBankAccountName();
                        $formData['bank_account_number'] = $claim->getClaimPaymentBankAccountNumber();
                        $formData['bank_sortcode_number'] = $claim->getClaimPaymentBankAccountSortCode();
                    }
                    break;

                case 4:
                    if ((isset($pageSession->completed[$stepNum]) && $pageSession->completed[$stepNum])
                       || (isset($pageSession->identifier[$stepNum]) && $pageSession->identifier[$stepNum])) {
                        $pageSession->identifier[$stepNum] = true;
                        $formData['doc_confirmation_agent_name'] = $claim->getDocConfirmationAgentName();
                        $formData['landlord_proprietor_of_property'] = $claim->getLandlordIsPropertyProprietor();
                        $formData['chk_confirm']                     = $claim->getAuthorityConfirmed();
                        $formData['dec_confirm']                     = $claim->getDeclarationConfirmed();

                        $formData['hd_type'] = $claim->getSubmittedToKeyHouse();
                    }
                    break;
            }
        } else {
            // Not in session but there are some defaults we need to set for step 1
            // TODO: Write the javascript better so we don't need to do fudges like this
            $this->view->headScript()->appendScript(
                "var sharersAllowed = 0;"
            );
        }

        $pageForm->populate($formData);
        $this->view->sidebar = $this->view->partial('partials/rent-guarantee-claim-sidebar.phtml',array('stepNum'=>$stepNum,'stepMax'=>$this->_stepMax));
        return true;
    }

    /**
     * Helper function for common work executed in each form step
     * Allows navigation between steps
     *
     * @param int $stepNum current step number
     *
     * @return void
     */
    private function _formStepCommonNavigate($stepNum)
    {
        $pageSession = new Zend_Session_Namespace('online_claims');
        $request = $this->getRequest();
        if ($request->isPost()) {
            // Handle moving backwards and forwards through the form
            $response = $this->getResponse();
            if (isset($_POST['back']) && $stepNum > 1) {
                $response->setRedirect('/rentguaranteeclaims/step' . ($stepNum - 1));
                $response->sendResponse();
            } elseif (isset($_POST['next']) && $stepNum < $this->_stepMax && $pageSession->completed[$stepNum]) {
                $response->setRedirect('/rentguaranteeclaims/step' . ($stepNum + 1));
                $response->sendResponse();
            }
        }
    }

    /**
     * Helper function for common work executed in each form step
     * Checks user is "allowed" on this step, redirecting if not
     * Returns result of form validation
     *
     * @param Zend_Form $pageForm form definition for this step
     * @param int $stepNum current step number
     *
     * @return array two elements: the modified Zend_Form and a boolean indicating validation success
     * @todo Needs a massive cleanup as it's way too slow for the ajax calls!
     */
    private function _formStepCommonValidate($pageForm, $stepNum)
    {
        $this->view->errorCount = 0;
        $this->view->stepNum = $stepNum;
        $this->view->stepMax = $this->_stepMax;

        $request = $this->getRequest();
        $pageSession = new Zend_Session_Namespace('online_claims');

        if ($pageForm->isValid($request->getPost())) {
            // Page is valid so set the session step to true
            $pageSession->completed[$stepNum] = true;
            return true;
        } else {
            $pageSession->completed[$stepNum] =false; // Mark page as invalid, so user must complete it to progress

            // Output errors to progress section in layout,
            //   and list out IDs that JS can use to highlight error fields
            $errorsJs = "var errorList = " . Zend_Json::encode($pageForm->getMessages()) . ";\n";
            $this->view->headScript()->appendScript($errorsJs, $type = 'text/javascript');

            $errorMessages = $pageForm->getMessages();
            $this->view->errorCount = count($errorMessages);

            $this->view->sidebar = $this->view->partial('partials/rent-guarantee-claim-sidebar.phtml',array(
                                    'errorsHtml' => $this->view->partial('partials/rent-guarantee-claim-error-list.phtml', array('errors' => $errorMessages)),
                                    'stepNum' => $stepNum,
                                    'stepMax' => $this->_stepMax));
            return false;
        }
    }

    public function continueAction()
    {   
        if($this->getRequest()->isPost())
        {
           
            // Sanitise ref_num
            $filters = array('*' => array('StringTrim','HtmlEntities','StripTags'));
            $validators = array('*' => array('allowEmpty' => true));
            $input['referenceNumber'] = $this->_request->getParam('ref_num');
            $validate = new Zend_Filter_Input($filters, $validators, $input);
             Zend_Debug::dump( $input['referenceNumber'] );
          
            $referenceNumber = $validate->getEscaped('referenceNumber');
            
            $pageSession = new Zend_Session_Namespace('online_claims');
            $pageSession->ClaimReferenceNumber = $referenceNumber;

            /*
            We are always going to go back to step1 for claims when continuing but we have to set the session variables up
            to know how far into the process we got. Otherwise this crap f*cking Sword code doesn't bother
            populating any of the forms with data already saved.

            I hate this - so much it gives me headache just thinking about it…
            */

            //Claim Manager
            $claimManager = new Manager_Insurance_RentGuaranteeClaim_Claim();

            //Identify the Step
            $pageSession->completed[1] = true;
            $pageSession->identifier[1] = true;
            
            $claimData = $claimManager->getClaim($referenceNumber,$this->_agentSchemeNumber);
            if($claimData->getTenancyStartDate() != ""){
                $step = 2;
                $pageSession->completed[2] = true;
                $pageSession->identifier[2] = true;
            }

            $rentPayments = $claimManager->getRentPaymentsByReferenceNumber($referenceNumber);
            if(count($rentPayments)>0) {
                $step = 3;
                $pageSession->completed[3] = true;
                $pageSession->identifier[3] = true;
            }

            $supportingDocs = $claimManager->getSupportingDocumentsByReferenceNumber($referenceNumber);
            if(count($supportingDocs)>0) {
                $step = 4;
                $pageSession->completed[4] = true;
                $pageSession->identifier[4] = true;
            }

            $this->_helper->redirector->gotoUrl('rentguaranteeclaims/step1');
        }
    }
}
?>
