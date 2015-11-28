<?php
/**
* Model definition for the rent guarantee claims table.
*/
class Datasource_Insurance_RentGuaranteeClaim_Claim extends Zend_Db_Table_Multidb {
    protected $_name = 'rent_guarantee_claims';
    protected $_referenceNumber = 'reference_number';
    protected $_multidb = 'db_homelet_connect';

    /**
     * Load an existing partial claims from the database into the object
     *
     * @param int $agentschemeno
     *
     * @return array
     */
    public function getPartialClaim($agentschemeno) {
        $claim = new Model_Insurance_RentGuaranteeClaim_Claim();
        $select = $this->select()
                ->where("agentschemenumber='" . $agentschemeno .
                    "' AND data_complete = 0 AND submitted_to_keyhouse = 0")
                ->order('last_updated_date DESC');
        $partialClaim = $this->fetchAll($select);
        if(count($partialClaim) == 0) {
            // No warning given as this is a common/normal scenario
            //$returnVal = null;
            $returnVal = array();
        } else {
            $partialClaimArray = $partialClaim->toArray();
            $returnVal = array();
            foreach ($partialClaimArray as $claim) {
                $referenceNumber = explode("_",$claim['reference_number']);
                $arr_address = array();
                if($claim['tenancy_housename']!="")
                    $arr_address[] = $claim['tenancy_housename'];
                if($claim['tenancy_street']!="")
                    $arr_address[] = $claim['tenancy_street'];
                if($claim['tenancy_postcode']!="")
                    $arr_address[] = $claim['tenancy_postcode'];

                array_push($returnVal, array(
                    'referenceNumber'       => $referenceNumber[0],
                    'lastUpdatedDate'       => $claim['last_updated_date'],
                    'submittedToKeyHouse'   => $claim['submitted_to_keyhouse'],
                    'tenancyAddress'        => implode(", ",$arr_address)
                ));
            }
        }
        return $returnVal;
    }

    /**
     * Gets all claims ids where data input is complete and not yet transferred
     * to keyhouse via the file transfer
     *
     * @param void
     * @return array Claims ids
     */
    public function getDataCompleteClaimsIds()
    {
        $claim = new Model_Insurance_RentGuaranteeClaim_Claim();
        $select = $this->select()
            ->where("submitted_to_keyhouse = 0 AND data_complete = 1")
            ->order('last_updated_date DESC');
        $dataComplete = $this->fetchAll($select);
        if(count($dataComplete) == 0) {
            $returnData = array();
        } else {
            $dataCompleteArray = $dataComplete->toArray();
            $returnData = array();
            foreach ($dataCompleteArray as $claim) {
                $referenceNumber = explode("_",$claim['reference_number']);
                array_push($returnData,
                    array(
                        'asn' => $claim['agentschemenumber'],
                        'refno' => $referenceNumber[0]
                    )
                );
            }
        }
        return $returnData;
    }

    /**
     * Gets the next available claim id from SQL Server
     *
     * @return int Next id
     */
    public function getNextNumber()
    {
        $claim = new Datasource_Insurance_KeyHouse_Claim();
        return $claim->getNextNumber();
    }


    /**
     * Inserts a new claim.
     *
     * @param int $agentID, int $agentSchemeNumber
     *
     * @return array
     */

    public function insertClaim($agentId,$agentSchemeNumber) {
        $data = array(
         'reference_number'     => $this->getNextNumber(),
         'entry_date'           => date('Y-m-d'),
         'agentid'              => $agentId,
         'agentschemenumber'    => $agentSchemeNumber,
        );
        $returnValue = $this->insert($data);
        return $returnValue;
    }

    /**
     * Updates a claim.
     *
     * @param array
     *
     * @return void
     */
    public function updateClaim($claim)
    {
        // These dates are not mandatory
        $tenancyEndDate = $claim-> getTenancyEndDate()?
            Application_Core_Utilities::ukDateToMysql($claim->getTenancyEndDate()): null;
        $tenantVacatedDate = $claim->getTenantVacatedDate() ?
            Application_Core_Utilities::ukDateToMysql($claim->getTenantVacatedDate()): null;

        $confirmedByTelDate = $claim->getTenantsOccupationOfPropertyConfirmedByTelDate() ?
            Application_Core_Utilities::ukDateToMysql($claim->getTenantsOccupationOfPropertyConfirmedByTelDate()) : null;

        $confirmedByEmailDate = $claim->getTenantsOccupationOfPropertyConfirmedByEmailDate() ?
            Application_Core_Utilities::ukDateToMysql($claim->getTenantsOccupationOfPropertyConfirmedByEmailDate()) : null;

        $confirmedByVisitDate = $claim->getTenantsOccupationOfPropertyConfirmedByVisitDate() ?
            Application_Core_Utilities::ukDateToMysql($claim->getTenantsOccupationOfPropertyConfirmedByVisitDate()) : null;

        $s21NoticeExpiryDate = $claim->getS21NoticeExpiry() ?
            Application_Core_Utilities::ukDateToMysql($claim->getS21NoticeExpiry()) : null;

        $s8NoticeExpiryDate = $claim->getS8NoticeExpiry() ?
            Application_Core_Utilities::ukDateToMysql($claim->getS8NoticeExpiry()) : null;

        $data = Array(
            'last_updated_date'                                             => date('Y-m-d'),
            'agent_name'                                                    => $claim->getAgentName(),
            'agent_contact_name'                                            => $claim->getAgentContactName(),
            'agent_postcode'                                                => $claim->getAgentPostcode(),
            'agent_housename'                                               => $claim->getAgentHousename(),
            'agent_street'                                                  => $claim->getAgentStreet(),
            'agent_town'                                                    => $claim->getAgentTown(),
            'agent_city'                                                    => $claim->getAgentCity(),
            'agent_telephone'                                               => $claim->getAgentTelephone(),
            'agent_fax'                                                     => $claim->getAgentFax(),
            'agent_email'                                                   => $claim->getAgentEmail(),
            'agent_isar'                                                    => $claim->getIsAr(),
            'agent_isdir'                                                   => $claim->getIsDir(),
            'housing_act_adherence'                                         => $claim->getHousingActAdherence(),
            'tenancy_start_date'                                            => Application_Core_Utilities::ukDateToMysql($claim->getTenancyStartDate()),
            'tenancy_renewal_date'                                          => $tenancyEndDate,
            'original_cover_start_date'                                     => Application_Core_Utilities::ukDateToMysql($claim->getOriginalCoverStartDate()),
            'tenancy_address_id'                                            => $claim->getTenancyAddressId(),
            'tenancy_postcode'                                              => $claim->getTenancyPostcode(),
            'tenancy_housename'                                             => $claim->getTenancyHouseName(),
            'tenancy_street'                                                => $claim->getTenancyStreet(),
            'tenancy_town'                                                  => $claim->getTenancyTown(),
            'tenancy_city'                                                  => $claim->getTenancyCity(),
            'monthly_rent'                                                  => $claim->getMonthlyRent(),
            'deposit_amount'                                                => $claim->getDepositAmount(),
            'rent_arrears'                                                  => ($claim->getRentArrears()?$claim->getRentArrears():NULL),
            'tenant_vacated_date'                                           => $tenantVacatedDate,
            'first_arrear_date'                                             => ($claim->getFirstArrearDate()?Application_Core_Utilities::ukDateToMysql($claim->getFirstArrearDate()):NULL),
            'deposit_received_date'                                         => Application_Core_Utilities::ukDateToMysql($claim->getDepositReceivedDate()),
            'landlord1_name'                                                => $claim->getLandlord1Name(),
            'landlord_company_name'                                         => $claim->getLandlordCompanyName(),
            'landlord_address_id'                                           => $claim->getLandlordAddressId(),
            'landlord_postcode'                                             => $claim->getLandlordPostcode(),
            'landlord_housename'                                            => $claim->getLandlordHouseName(),
            'landlord_street'                                               => $claim->getLandlordStreet(),
            'landlord_town'                                                 => $claim->getLandlordTown(),
            'landlord_city'                                                 => $claim->getLandlordCity(),
            'landlord_telephone'                                            => $claim->getLandlordTelephone(),
            'landlord_fax'                                                  => $claim->getLandlordFax(),
            'landlord_email'                                                => $claim->getLandlordEmail(),
            'cheque_payable_to'                                             => $claim->getChequePayableTo(),
            'additional_information'                                        => $claim->getAdditionalInfo(),
            'doc_confirmation_agent_name'                                   => $claim->getDocConfirmationAgentName(),
            'submitted_to_keyhouse'                                         => $claim->getSubmittedToKeyHouse(),
            'housing_benefit_applied'                                       => $claim->getHousingBenefitApplied(),
            'data_complete'                                                 => $claim->getDataComplete(),
            'tenant_vacated'                                                => $claim->getTenantVacated(),
            'recent_complaints'                                             => $claim->getRecentComplaints(),
            'recent_complaints_further_details'                             => $claim->getRecentComplaintsDetails(),
            'policy_number'                                                 => $claim->getPolicyNumber(),
            'grounds_for_claim'                                             => $claim->getGroundsForClaim(),
            'grounds_for_claim_further_details'                             => $claim->getGroundsForClaimDetails(),

            'arrears_at_vacant_possession'                                  => $claim->getArrearsAtVacantPossession(),

            'tenant_occupation_confirmed_by_tel'                            => $claim->getTenantsOccupationOfPropertyConfirmedByTel(),
            'tenant_occupation_confirmed_by_tel_dateofcontact'              => $confirmedByTelDate,
            'tenant_occupation_confirmed_by_tel_tenantname'                 => $claim->getTenantsOccupationOfPropertyConfirmedByTelContact(),
            'tenant_occupation_confirmed_by_email'                          => $claim->getTenantsOccupationOfPropertyConfirmedByEmail(),
            'tenant_occupation_confirmed_by_email_dateofcontact'            => $confirmedByEmailDate,
            'tenant_occupation_confirmed_by_email_tenantname'               => $claim->getTenantsOccupationOfPropertyConfirmedByEmailContact(),
            'tenant_occupation_confirmed_by_visit'                          => $claim->getTenantsOccupationOfPropertyConfirmedByVisit(),
            'tenant_occupation_confirmed_by_visit_dateofvisit'              => $confirmedByVisitDate,
            'tenant_occupation_confirmed_by_visit_individualattending'      => $claim->getTenantsOccupationOfPropertyConfirmedByVisitIndividual(),
            'tenant_occupation_confirmed_by_visit_tenantname'               => $claim->getTenantsOccupationOfPropertyConfirmedByVisitContact(),

            // Tenants forwarding address
            'tenantsforwarding_address_id'                                  => $claim->getTenantsForwardingAddressId(),
            'tenantsforwarding_housename'                                   => $claim->getTenantsForwardingHouseName(),
            'tenantsforwarding_street'                                      => $claim->getTenantsForwardingStreet(),
            'tenantsforwarding_town'                                        => $claim->getTenantsForwardingTown(),
            'tenantsforwarding_city'                                        => $claim->getTenantsForwardingCity(),
            'tenantsforwarding_postcode'                                    => $claim->getTenantsForwardingPostcode(),

            // s21 notice status
            'section21_served'                                              => $claim->getS21NoticeServed(),
            'section21_expiry'                                              => $s21NoticeExpiryDate,
            'section21_moneydepositreceived'                                => $claim->getS21NoticeMoneyDepositReceived(),
            'section21_money_held_under_tds_deposit_scheme'                 => $claim->getS21NoticeMoneyDepositHeldUnderTdsScheme(),
            'section21_tds_complied_with'                                   => $claim->getS21NoticeTdsCompliedWith(),
            'section21_tds_prescribed_information_to_tenant'                => $claim->getS21NoticeTdsPrescribedToTenant(),
            'section21_landlord_deposit_in_property_form'                   => $claim->getS21NoticeLandlordDepositInPropertyForm(),
            'section21_returned_at_notice_serve_date'                       => $claim->getS21NoticePropertyReturnedAtNoticeServeDate(),

           // s8 notice status
            'section8_served'                                               => $claim->getS8NoticeServed(),
            'section8_expiry'                                               => $s8NoticeExpiryDate,
            'section8_demand_letter_sent'                                   => $claim->getS8NoticeDemandLetterSent(),
            'section8_over18_occupants'                                     => $claim->getS8NoticeOver18Occupants(),

           // Claim payment details
           'payment_account_name'                                           => $claim->getClaimPaymentBankAccountName(),
           'payment_account_number'                                         => $claim->getClaimPaymentBankAccountNumber(),
           'payment_account_sortcode'                                       => $claim->getClaimPaymentBankAccountSortCode(),

            // Agent declaration
            'landlord_property_proprietor'                                  => $claim->getLandlordIsPropertyProprietor(),

            // Authority and declaration confirmation
            'authority_confirmed'                                           => $claim->getAuthorityConfirmed(),
            'dec_confirmed'                                                 => $claim->getDeclarationConfirmed(),
        );
        $where = $this->getAdapter()->quoteInto('reference_number = ?', $claim->_referenceNumber);
        $this->update($data,$where);
    }




    /**
     * Retrieves the specified claim record, encapsulates the details in a
     * Claim object and returns this. Impoved to include agents scheme number, 
     * 
     *
     * @param int $referenceNumber
     * Identifies the claim record in the rent_guarantee_claims table.
     * @param int $agentSchemeNumber
     * Identifies the agent to prevent horizontal privledge escalation 
     * 
     * @return Model_Insurance_RentGuaranteeClaim_Claim
     * The claim details encapsulated in a Claim object.
     */
    public function getClaim($referenceNumber, $agentSchemeNumber=null) {
         //Retrieve the claim record.
        $select = $this->select();
        $select->where("reference_number = ?", $referenceNumber);
        if(!is_null($agentSchemeNumber)){
            $select->where('agentschemenumber = ?', $agentSchemeNumber);
        }
        
        $claimRow = $this->fetchRow($select);

        if($claimRow) {
            //Populate the details into a Claim object.
            $claim = new Model_Insurance_RentGuaranteeClaim_Claim();
            $claimManager = new Manager_Insurance_RentGuaranteeClaim_Claim();
            $claim->setReferenceNumber($referenceNumber);

            $claim->setAgentName($claimRow->agent_name);
            $claim->setAgentSchemeNumber($claimRow->agentschemenumber);
            $claim->setAgentContactName($claimRow->agent_contact_name);
            $claim->setAgentAddress($claimManager->concatDetails(
                array(
                    $claimRow->agent_housename, $claimRow->agent_street,
                    $claimRow->agent_town, $claimRow->agent_city
                )
            ));
            $claim->setAgentHousename($claimRow->agent_housename);
            $claim->setAgentStreet($claimRow->agent_street);
            $claim->setAgentTown($claimRow->agent_town);
            $claim->setAgentCity($claimRow->agent_city);
            $claim->setAgentPostcode($claimRow->agent_postcode);
            $claim->setAgentTelephone($claimRow->agent_telephone);
            $claim->setAgentFax($claimRow->agent_fax);
            $claim->setAgentEmail($claimRow->agent_email);
            $claim->setIsAr($claimRow->agent_isar);
            $claim->setIsDir($claimRow->agent_isdir);

            $claim->setLandlord1Name($claimRow->landlord1_name);
            $claim->setLandlordCompanyName($claimRow->landlord_company_name);
            $claim->setLandlordAddress($claimManager->concatDetails(
                array(
                    $claimRow->landlord_housename, $claimRow->landlord_street,
                    $claimRow->landlord_town, $claimRow->landlord_city
                )
            ));
            $claim->setLandlordHouseName($claimRow->landlord_housename);
            $claim->setLandlordStreet($claimRow->landlord_street);
            $claim->setLandlordTown($claimRow->landlord_town);
            $claim->setLandlordCity($claimRow->landlord_city);
            $claim->setLandlordAddressId($claimRow->landlord_address_id);
            $claim->setLandlordPostcode($claimRow->landlord_postcode);
            $claim->setLandlordTelephone($claimRow->landlord_telephone);
            $claim->setLandlordFax($claimRow->landlord_fax);
            $claim->setLandlordEmail($claimRow->landlord_email);

            $claim->setRecentComplaints($claimRow->recent_complaints);
            $claim->setRecentComplaintsDetails($claimRow->recent_complaints_further_details);

            $claim->setPolicyNumber($claimRow->policy_number);
            $claim->setGroundsForClaim($claimRow->grounds_for_claim);
            $claim->setGroundsForClaimDetails($claimRow->grounds_for_claim_further_details);
            $claim->setArrearsAtVacantPossession($claimRow->arrears_at_vacant_possession);

            $claim->setTenantsOccupationOfPropertyConfirmedByTel($claimRow->tenant_occupation_confirmed_by_tel);

            if ($claimRow->tenant_occupation_confirmed_by_tel_dateofcontact != null) {
                $claim->setTenantsOccupationOfPropertyConfirmedByTelDate(date('d/m/Y',strtotime($claimRow->tenant_occupation_confirmed_by_tel_dateofcontact)));
            }

            $claim->setTenantsOccupationOfPropertyConfirmedByTelContact($claimRow->tenant_occupation_confirmed_by_tel_tenantname);
            $claim->setTenantsOccupationOfPropertyConfirmedByEmail($claimRow->tenant_occupation_confirmed_by_email);

            if ($claimRow->tenant_occupation_confirmed_by_email_dateofcontact != null) {
                $claim->setTenantsOccupationOfPropertyConfirmedByEmailDate(date('d/m/Y',strtotime($claimRow->tenant_occupation_confirmed_by_email_dateofcontact)));
            }

            $claim->setTenantsOccupationOfPropertyConfirmedByEmailContact($claimRow->tenant_occupation_confirmed_by_email_tenantname);
            $claim->setTenantsOccupationOfPropertyConfirmedByVisit($claimRow->tenant_occupation_confirmed_by_visit);

            if ($claimRow->tenant_occupation_confirmed_by_visit_dateofvisit != null) {
                $claim->setTenantsOccupationOfPropertyConfirmedByVisitDate(date('d/m/Y',strtotime($claimRow->tenant_occupation_confirmed_by_visit_dateofvisit)));
            }

            $claim->setTenantsOccupationOfPropertyConfirmedByVisitIndividual($claimRow->tenant_occupation_confirmed_by_visit_individualattending);
            $claim->setTenantsOccupationOfPropertyConfirmedByVisitContact($claimRow->tenant_occupation_confirmed_by_visit_tenantname);

            $claim->setS21NoticeServed($claimRow->section21_served);

            if ($claimRow->section21_expiry != null) {
                $claim->setS21NoticeExpiry(date('d/m/Y',strtotime($claimRow->section21_expiry)));
            }

            $claim->setS21NoticeMoneyDepositReceived($claimRow->section21_moneydepositreceived);
            $claim->setS21NoticeMoneyDepositHeldUnderTdsScheme($claimRow->section21_money_held_under_tds_deposit_scheme);
            $claim->setS21NoticeTdsCompliedWith($claimRow->section21_tds_complied_with);
            $claim->setS21NoticeTdsPrescribedToTenant($claimRow->section21_tds_prescribed_information_to_tenant);
            $claim->setS21NoticeLandlordDepositInPropertyForm($claimRow->section21_landlord_deposit_in_property_form);
            $claim->setS21NoticePropertyReturnedAtNoticeServeDate($claimRow->section21_returned_at_notice_serve_date);

            $claim->setS8NoticeServed($claimRow->section8_served);

            if ($claimRow->section8_expiry != null) {
                $claim->setS8NoticeExpiry(date('d/m/Y',strtotime($claimRow->section8_expiry)));
            }

            $claim->setS8NoticeDemandLetterSent($claimRow->section8_demand_letter_sent);
            $claim->setS8NoticeOver18Occupants($claimRow->section8_over18_occupants);

            $claim->setLandlordIsPropertyProprietor($claimRow->landlord_property_proprietor);

            $claim->setChequePayableTo($claimRow->cheque_payable_to);
            $claim->setSubmittedToKeyHouse($claimRow->submitted_to_keyhouse);
            $claim->setDataComplete($claimRow->data_complete);
            $claim->setHousingActAdherence($claimRow->housing_act_adherence);

            // Prevent strtotime make the date 1970-01-01 when parsing an empty date or when date string is zeros
            if($claimRow->tenancy_start_date != "" ||
                $claimRow->tenancy_start_date == '0000-00-00') {
                $claim->setTenancyStartDate(
                    date('d/m/Y',strtotime($claimRow->tenancy_start_date))
                );
            }

            if($claimRow->tenancy_renewal_date != "" ||
                $claimRow->tenancy_renewal_date == '0000-00-00') {
                $claim->setTenancyEndDate(
                    date('d/m/Y',strtotime($claimRow->tenancy_renewal_date))
                );
            }

            if($claimRow->original_cover_start_date != "" ||
                $claimRow->original_cover_start_date == '0000-00-00') {
                $claim->setOriginalCoverStartDate(
                    date('d/m/Y',strtotime($claimRow->original_cover_start_date))
                );
            }

            $claim->setTenancyPostcode($claimRow->tenancy_postcode);
            $claim->setTenancyAddress($claimManager->concatDetails(
                array(
                    $claimRow->tenancy_housename, $claimRow->tenancy_street,
                    $claimRow->tenancy_town, $claimRow->tenancy_city
                )
            ));
            $claim->setTenancyAddressId($claimRow->tenancy_address_id);
            $claim->setTenancyHouseName($claimRow->tenancy_housename);
            $claim->setTenancyStreet($claimRow->tenancy_street);
            $claim->setTenancyTown($claimRow->tenancy_town);
            $claim->setTenancyCity($claimRow->tenancy_city);
            $claim->setTenantVacated($claimRow->tenant_vacated);
            $claim->setMonthlyRent($claimRow->monthly_rent);
            $claim->setDepositAmount($claimRow->deposit_amount);
            $claim->setRentArrears($claimRow->rent_arrears);

            $claim->setTenantsForwardingAddress($claimManager->concatDetails(
                array(
                    $claimRow->tenantsforwarding_housename, $claimRow->tenantsforwarding_street,
                    $claimRow->tenantsforwarding_town, $claimRow->tenantsforwarding_city
                )
            ));

            $claim->setTenantsForwardingAddressId($claimRow->tenantsforwarding_address_id);
            $claim->setTenantsForwardingHouseName($claimRow->tenantsforwarding_housename);
            $claim->setTenantsForwardingStreet($claimRow->tenantsforwarding_street);
            $claim->setTenantsForwardingTown($claimRow->tenantsforwarding_town);
            $claim->setTenantsForwardingCity($claimRow->tenantsforwarding_city);
            $claim->setTenantsForwardingPostcode($claimRow->tenantsforwarding_postcode);

            if($claimRow->tenant_vacated_date != "" || $claimRow->tenant_vacated_date == '0000-00-00')
            $claim->setTenantVacatedDate(date('d/m/Y',strtotime($claimRow->tenant_vacated_date)));

            if($claimRow->first_arrear_date != "" || $claimRow->first_arrear_date == '0000-00-00')
            $claim->setFirstArrearDate(date('d/m/Y',strtotime($claimRow->first_arrear_date)));

            if($claimRow->deposit_received_date != "" || $claimRow->deposit_received_date == '0000-00-00')
            $claim->setDepositReceivedDate(date('d/m/Y',strtotime($claimRow->deposit_received_date)));

            $claim->setDocConfirmationAgentName($claimRow->doc_confirmation_agent_name);
            $claim->setAdditionalInfo($claimRow->additional_information);

            // Claim payment details
            $claim->setClaimPaymentBankAccountName($claimRow->payment_account_name);
            $claim->setClaimPaymentBankAccountNumber($claimRow->payment_account_number);
            $claim->setClaimPaymentBankAccountSortCode($claimRow->payment_account_sortcode);

            if ($claimRow->authority_confirmed != null) {
                $claim->setAuthorityConfirmed($claimRow->authority_confirmed);
            }

            if ($claimRow->dec_confirmed != null) {
                $claim->setDeclarationConfirmed($claimRow->dec_confirmed);
            }

            return $claim;
        }
        else {
            return null;
        }
    }

    /**
     * Retrieves the specified claim record, encapsulates the details in a
     * Claim array and returns this.
     *
     * @param int $referenceNumber
     *
     * Identifies the claim record in the rent_guarantee_claims table.
     * @return array
     */

    public function getClaimByReferenceNumber($referenceNumber) {
        $select = $this->select();
        $select->where("reference_number = ? ",$referenceNumber);
        $claimRow = $this->fetchRow($select);
        return $claimRow->toArray();
    }

    /**
     *
     * Delete the Claim details for the given reference number
     *
     * @param int $referenceNumber
     *
     * @return void
     */
    public function deleteClaim($referenceNumber) {
        $where = $this->getAdapter()->quoteInto('reference_number = ?', $referenceNumber);
        $this->delete($where);
    }

}
?>
