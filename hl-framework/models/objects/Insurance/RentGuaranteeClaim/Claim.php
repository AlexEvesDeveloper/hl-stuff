<?php

/**
 * Holds a single claim.
 */
class Model_Insurance_RentGuaranteeClaim_Claim extends Model_Abstract
{

    public $_referenceNumber;

    /**
    *    Claim created and updated date information
    */
    public $_entryDate         ='0000-00-00';
    public $_lastUpdatedDate ='0000-00-00';

    /**
    *    Agent Information
    */
    public $_agentId;
    public $_agentSchemeNumber;
    public $_agentName;
    public $_agentContactName;
    public $_agentAddressId;
    public $_agentAddress;
    public $_agentHouseName;
    public $_agentStreet;
    public $_agentTown;
    public $_agentCity;
    public $_agentPostcode;
    public $_agentTelephone;
    public $_agentFax;
    public $_agentEmail;
    public $_isAr;
    public $_isDir;

    /**
    *    Landlord Information
    */
    public $_landlord1Name;
    public $_landlordCompanyName;
    public $_landlordAddressId;
    public $_landlordAddress;
    public $_landlordHouseName;
    public $_landlordStreet;
    public $_landlordTown;
    public $_landlordCity;
    public $_landlordPostcode;
    public $_landlordTelephone;
    public $_landlordFax;
    public $_landlordEmail;

    /**
    * Adherence Information
    */
    public $_housingActAdherence;

    /**
    * Tenant Information
    */
    public $_tenancyStartDate;
    public $_tenancyEndDate;
    public $_originalCoverStartDate;
    public $_tenancyPostcode;
    public $_tenancyAddressId;
    public $_tenancyAddress;
    public $_tenancyHouseName;
    public $_tenancyStreet;
    public $_tenancyTown;
    public $_tenancyCity;
    public $_monthlyRent;
    public $_depositAmount;
    public $_rentArrears;
    public $_tenantsVacated;
    public $_tenantsVacatedDate;
    public $_firstArrearDate;
    public $_depositReceivedDate;
    public $_tenantVacatedDate;
    public $_totalGuarantors;
    public $_totalTenants;

    /**
     * Claim background information
     */
    public $_policyNumber;
    public $_recentComplaints;
    public $_recentComplaintsDetails;
    public $_groundsForClaim;
    public $_groundsForClaimDetails;
    public $_arrearsAtVacantPossession;

    public $_occupationConfirmedByTel;
    public $_occupationConfirmedByTelDate;
    public $_occupationConfirmedByTelContact;
    public $_occupationConfirmedByEmail;
    public $_occupationConfirmedByEmailDate;
    public $_occupationConfirmedByEmailContact;
    public $_occupationConfirmedByVisit;
    public $_occupationConfirmedByVisitDate;
    public $_occupationConfirmedByVisitIndividual;
    public $_occupationConfirmedByVisitContact;

    /**
     * Tenants forwarding details
     */
    public $_tenantsForwardingAddress;
    public $_tenantsForwardingAddressId;
    public $_tenantsForwardingHouseName;
    public $_tenantsForwardingStreet;
    public $_tenantsForwardingTown;
    public $_tenantsForwardingCity;
    public $_tenantsForwardingPostcode;

    /**
     * S21 notice status
     */
    public $_s21NoticeServed;
    public $_s21NoticeExpiry;
    public $_s21MoneyDepositReceived;
    public $_s21MoneyDepositHeldUnderTdsScheme;
    public $_s21TdsCompliedWith;
    public $_s21TdsPrescribedToTenant;
    public $_s21LandlordDepositInPropertyForm;
    public $_s21PropertyReturnedByServeDate;

    /**
     * S8 notice status
     */
    public $_s8NoticeServed;
    public $_s8NoticeExpiry;
    public $_s8DemandLetterSent;
    public $_s8Over18Occupants;


    /**
     * Cheque Payable
     */
    public $_chequePayableTo;

    /**
     * KeyHouse Submission Status
     */
    public $_submittedToKeyHouse;
    public $_data_complete;
    public $_housing_benefit_applied;

    /**
     * Claim payment details
     */
    public $_paymentBankAccountName;
    public $_paymentBankAccountNumber;
    public $_paymentBankAccountSortCode;

    /**
    * Insured authority and declaration
    */
    public $_docConfirmAgentName;
    public $_landlordIsPropertyProprietor;

    /**
    * Additional Information
    */
    public $_additionalInfo;

    /**
     * Agents confirmations
     */
    public $_authConfirmed;
    public $_decConfirmed;

    /**
     * Get Reference Number
     *
     * @return int referenceNumber
     */
    public function getReferenceNumber()
    {
        return $this->_referenceNumber;
    }

    /**
     * Set the reference number.
     *
     * @param int $referenceNumber
     * @return void
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->_referenceNumber =   $referenceNumber;
    }

    /**
     * Get Agent Name
     *
     * @return string agentName
     */
    public function getAgentName()
    {
        return $this->_agentName;
    }

    /**
     * Set Agent Name.
     *
     * @param string $agentName
     * @return void
     */
    public function setAgentName($agentName)
    {
        $this->_agentName   =   $agentName;
    }

    /**
     * Get the Agent Id
     *
     * @return int agentId
     */
    public function getAgentId()
    {
        return $this->_agentId;
    }

    /**
     * Set the Agent Id.
     *
     * @param string $agentId.
     * @return void
     */
    public function setAgentId($agentId)
    {
        $this->_agentId =   $agentId;
    }

    /**
     * Get the Agent Scheme Number
     *
     * @return int agentSchemeNumber
     */

    public function getAgentSchemeNumber()
    {
        return $this->_agentSchemeNumber;
    }

    /**
     * Set the Agent Scheme Number.
     *
     * @param int $agentSchemeNumber
     * @return void
     */
    public function setAgentSchemeNumber($agentSchemeNumber)
    {
        $this->_agentSchemeNumber   =   $agentSchemeNumber;
    }

    /**
     * Get the submitted to keyhouse status.
     *
     * @return int submittedToKeyHouse
     */

    public function getSubmittedToKeyHouse()
    {
        return $this->_submittedToKeyHouse;
    }

    /**
     * Set the Submitted To Key House status.
     *
     * @param int $submittedToKeyHouse
     * @return void
     */
    public function setSubmittedToKeyHouse($submittedToKeyHouse)
    {
        $this->_submittedToKeyHouse =   $submittedToKeyHouse;
    }

    /**
     * Get the Agent contact name.
     *
     * @return string agentContactName
     * The Agent contact name.
     */
    public function getAgentContactName()
    {
        return $this->_agentContactName;
    }

    /**
     * Set Agent Contact Name.
     *
     * @param string $agentContactName
     * @return void
     */
    public function setAgentContactName($agentContactName)
    {
        $this->_agentContactName    =   $agentContactName;
    }

    /**
     * Get Agent Postcode.
     *
     * @return string agentPostcode
     */
    public function getAgentPostcode()
    {
        return $this->_agentPostcode;
    }

    /**
     * Set Agent Postcode.
     *
     * @param string $agentPostcode
     * @return void
     */
    public function setAgentPostcode($agentPostcode)
    {
        $this->_agentPostcode   =   $agentPostcode;
    }

    /**
     * Get Agent Address Id.
     *
     * @return int
     */
    public function getAgentAddressId()
    {
        return $this->_agentAddressId;
    }

    /**
     * Set Agent Address Id.
     *
     * @param int $agentAddressId
     * @return void
     */
    public function setAgentAddressId($agentAddressId)
    {
        $this->_agentAddressId  =   $agentAddressId;
    }

    /**
     * Get Agent Address Id.
     *
     * @return int agentAddress
     */
    public function getAgentAddress()
    {
        return $this->_agentAddress;
    }

    /**
     * Set Agent Address.
     *
     * @param int $agentAddress
     * @return void
     */
    public function setAgentAddress($agentAddress)
    {
        $this->_agentAddress    =   $agentAddress;
    }

    /**
     * Get Agent House Name
     *
     * @return string agentHouseName
     */
    public function getAgentHousename()
    {
        return $this->_agentHouseName;
    }

    /**
     * Set Agent House Name.
     *
     * @param string $agentHouseName
     * @return void
     */
    public function setAgentHouseName($agentHouseName)
    {
        $this->_agentHouseName  =   $agentHouseName;
    }

    /**
     * Get Agent Street.
     *
     * @return string agentStreet
     */
    public function getAgentStreet()
    {
        return $this->_agentStreet;
    }

    /**
     * Set Agent Street.
     *
     * @param string $agentStreet
     * @return void
     */
    public function setAgentStreet($agentStreet)
    {
        $this->_agentStreet =   $agentStreet;
    }

    /**
     * Get Agent City.
     *
     * @return string agentCity
     */
    public function getAgentCity()
    {
        return $this->_agentCity;
    }

    /**
     * Set Agent Street.
     *
     * @param string $agentCity.
     * @return void
     */
    public function setAgentCity($agentCity)
    {
        $this->_agentCity   =   $agentCity;
    }

    /**
     * Get Agent Telephone.
     *
     * @return int agentTelephone
     * The Agent Telephone.
     */
    public function getAgentTelephone()
    {
        return $this->_agentTelephone;
    }

    /**
     * Set Agent Telephone.
     *
     * @param int $agentTelephone
     * @return void
     */
    public function setAgentTelephone($agentTelephone)
    {
        $this->_agentTelephone  =   $agentTelephone;
    }

    /**
     * Get Agent Fax.
     *
     * @return int agentFax
     */
    public function getAgentFax()
    {
        return $this->_agentFax;
    }

    /**
     * Set Agent Fax.
     *
     * @param int $agentFax
     * @return void
     */
    public function setAgentFax($agentFax)
    {
        $this->_agentFax=$agentFax;
    }

    /**
     * Get Agent Town.
     *
     * @return string agentTown
     */
    public function getAgentTown()
    {
        return $this->_agentTown;
    }

   /**
     * Set Agent Town.
    *
     * @param string $agentTown
     * @return void
     */
    public function setAgentTown($agentTown)
    {
        $this->_agentTown=$agentTown;
    }

    /**
     * Get Email Id.
     *
     * @return string agentEmail
     */
    public function getAgentEMail()
    {
        return $this->_agentEmail;
    }

    /**
     * Set Agent Email Id.
     *
     * @param string $agentEmail
     * @return void
     */
    public function setAgentEMail($agentEmail)
    {
        $this->_agentEmail=$agentEmail;
    }

    /**
     * Get landlord1 name.
     *
     * @return string lanlordName
     */
    public function getLandlord1Name()
    {
        return $this->_landlord1Name;
    }


   /**
     * Set landlord1 name
    *
     * @param string $landlord1Name
     * @return void
     */
    public function setLandlord1Name($landlord1Name)
    {
        $this->_landlord1Name=$landlord1Name;
    }

    /**
     * Get landlord company name.
     *
     * @return string
     */
    public function getLandlordCompanyName()
    {
        return $this->_landlordCompanyName;
    }

   /**
     * Set landlord company name
    *
     * @param string $landlordCompanyName
     * @return void
     */
    public function setLandlordCompanyName($landlordCompanyName)
    {
        $this->_landlordCompanyName=$landlordCompanyName;
    }

    /**
     * Get landlord postcode.
     *
     * @return string landlordPostcode
     */
    public function getLandlordPostcode()
    {
        return $this->_landlordPostcode;
    }

    /**
     * Set landlord postcode
     *
     * @param string $landlordPostcode
     * @return void
     */
    public function setLandlordPostcode($landlordPostcode)
    {
        $this->_landlordPostcode=$landlordPostcode;
    }

    /**
     * Get landlord Address Id.
     *
     * @return string landlordAddressId
     */
    public function getLandlordAddressId()
    {
        return $this->_landlordAddressId;
    }

    /**
     * Set landlord Address Id.
     *
     * @param string $landlordAddressId
     * @return void
     */
    public function setLandlordAddressId($landlordAddressId)
    {
        $this->_landlordAddressId=$landlordAddressId;
    }

    /**
     * Get landlord Address.
     *
     * @return string landlordAddress
     * The Landlord Address.
     */
    public function getLandlordAddress()
    {
        return $this->_landlordAddress;
    }

    /**
     * Set landlord Address.
     *
     * @param string $landlordAddress
     * @return void
     */
    public function setLandlordAddress($landlordAddress)
    {
        $this->_landlordAddress=$landlordAddress;
    }

    /**
     * Get landlord House name.
     *
     * @return string landlordHouseName
     * The Landlord House name.
     */
    public function getLandlordHousename()
    {
        return $this->_landlordHouseName;
    }

    /**
     * Set landlord House name
     *
     * @param string $landlordHouseName
     * @return void
     */
    public function setLandlordHousename($landlordHouseName)
    {
        $this->_landlordHouseName=$landlordHouseName;
    }

    /**
     * Get landlord Street.
     *
     * @return string landlordStreet
     */
    public function getLandlordStreet()
    {
        return $this->_landlordStreet;
    }

    /**
     * Set landlord Street
     *
     * @param string $landlordStreet
     * @return void
     */
    public function setLandlordStreet($landlordStreet)
    {
        $this->_landlordStreet=$landlordStreet;
    }

    /**
     * Get landlord Town.
     *
     * @return string landlordTown
     */
    public function getLandlordTown()
    {
        return $this->_landlordTown;
    }


   /**
     * Set landlord Town
    *
     * @param string $landlordTown
     * @return void
     */
    public function setLandlordTown($landlordTown)
    {
        $this->_landlordTown=$landlordTown;
    }

    /**
     * Get landlord City.
     *
     * @return string landlordCity
     */
    public function getLandlordCity()
    {
        return $this->_landlordCity;
    }

    /**
     * Set landlord City
     *
     * @param string $landlordCity
     * @return void
     */
    public function setLandlordCity($landlordCity)
    {
        $this->_landlordCity=$landlordCity;
    }

    /**
     * Get landlord Telephone.
     *
     * @return int landlordTelephone
     */
    public function getLandlordTelephone()
    {
        return $this->_landlordTelephone;
    }

    /**
     * Set landlord Telephone
     *
     * @param int $landlordTelephone
     * @return void
     */
    public function setLandlordTelephone($landlordTelephone)
    {
        $this->_landlordTelephone=$landlordTelephone;
    }

    /**
     * Get landlord fax number.
     *
     * @return int landlordFax
     */
    public function getLandlordFax()
    {
        return $this->_landlordFax;
    }

    /**
     * Set landlord Fax number
     *
     * @param int $landlordFax
     * @return void
     */
    public function setLandlordFax($landlordFax)
    {
        $this->_landlordFax=$landlordFax;
    }

    /**
     * Get landlord Email
     *
     * @return string landlordEmail
     */
    public function getLandlordEmail()
    {
        return $this->_landlordEmail;
    }

    /**
     * Set the landlord Email.
     *
     * @param string $landlordEmail
     * @return void
     */
    public function setLandlordEmail($landlordEmail)
    {
        $this->_landlordEmail=$landlordEmail;
    }

    /**
     * Get cheque payable to.
     *
     * @return string chequePayableTo
     */
    public function getChequePayableTo()
    {
        return $this->_chequePayableTo;
    }

    /**
     * Set cheque payable to.
     *
     * @param string $chequePayableTo
     * @return void
     */
    public function setChequePayableTo($chequePayableTo)
    {
        $this->_chequePayableTo=$chequePayableTo;
    }

    /**
     * Get Housing Act Adherence
     *
     * @return int housingActAdherence
     */
    public function getHousingActAdherence()
    {
        return $this->_housingActAdherence;
    }

    /**
     * Set Housing Act Adherence
     *
     * @param int $housingActAdherence
     * @return void
     */
    public function setHousingActAdherence($housingActAdherence)
    {
        $this->_housingActAdherence=$housingActAdherence;
    }

    /**
     * Get Tenancy Start Date
     *
     * @return string tenancyStartDate
     */
    public function getTenancyStartDate()
    {
        return $this->_tenancyStartDate;
    }

    /**
     * Set Tenancy Start Date
     *
     * @param string $tenancyStartDate
     * @return void
     */
    public function setTenancyStartDate($tenancyStartDate)
    {
        $this->_tenancyStartDate=$tenancyStartDate;
    }

    /**
     * Get Tenancy Renewal Date
     *
     * @return string tenancyRenewalDate
     */
    public function getTenancyEndDate()
    {
        return $this->_tenancyEndDate;
    }

    /**
     * Set Tenancy Renewal Date
     *
     * @param string $tenancyRenewalDate
     * @return void
     */
    public function setTenancyEndDate($tenancyRenewalDate)
    {
        $this->_tenancyEndDate=$tenancyRenewalDate;
    }

    /**
     * Get Original Cover Start Date
     *
     * @return string originalCoverStartDate
     */
    public function getOriginalCoverStartDate()
    {
        return $this->_originalCoverStartDate;
    }

    /**
     * Set Original Cover Start Date
     *
     * @param string $coverStartDate
     * @return void
     */
    public function setOriginalCoverStartDate($coverStartDate)
    {
        $this->_originalCoverStartDate=$coverStartDate;
    }

    /**
     * Get Tenancy Potcode
     *
     * @return String tenancyPostcode
     */
    public function getTenancyPostcode()
    {
        return $this->_tenancyPostcode;
    }

    /**
     * Set Tenancy Postcode
     *
     * @param string $tenancyPostcode
     * @return void
     */
    public function setTenancyPostcode($tenancyPostcode)
    {
        $this->_tenancyPostcode=$tenancyPostcode;
    }

    /**
     * Get Tenancy Address
     *
     * @return String tenancyAddress
     */
    public function getTenancyAddress()
    {
        return $this->_tenancyAddress;
    }

    /**
     * Set Tenancy Address
     *
     * @param string $tenancyAddress
     * @return void
     */
    public function setTenancyAddress($tenancyAddress)
    {
        $this->_tenancyAddress=$tenancyAddress;
    }

    /**
     * Get Tenancy House Name
     *
     * @return String tenancyHouseName
     */
    public function getTenancyHouseName()
    {
        return $this->_tenancyHouseName;
    }

    /**
     * Set Tenancy House Name
     *
     * @param string $tenancyHouseName
     * @return void
     */
    public function setTenancyHouseName($tenancyHouseName)
    {
        $this->_tenancyHouseName=$tenancyHouseName;
    }

    /**
     * Get Tenancy Street
     *
     * @return String tenancyStreet
     */
    public function getTenancyStreet()
    {
        return $this->_tenancyStreet;
    }

    /**
     * Set Tenancy Street
     *
     * @param string $tenancyStreet
     * @return void
     */
    public function setTenancyStreet($tenancyStreet)
    {
        $this->_tenancyStreet=$tenancyStreet;
    }

    /**
     * Get Tenancy Town
     *
     * @return String tenancyTown
     */
    public function getTenancyTown()
    {
        return $this->_tenancyTown;
    }

    /**
     * Set Tenancy Town
     *
     * @param string $tenancyTown
     * @return void
     */
    public function setTenancyTown($tenancyTown)
    {
        $this->_tenancyTown=$tenancyTown;
    }

    /**
     * Get Tenancy City
     *
     * @return String tenancyCity
     */
    public function getTenancyCity()
    {
        return $this->_tenancyCity;
    }

    /**
     * Set Tenancy City
     *
     * @param string $tenancyCity
     * @return void
     */
    public function setTenancyCity($tenancyCity)
    {
        $this->_tenancyCity=$tenancyCity;
    }

    /**
     * Get Monthly Rent
     *
     * @return int monthlyRent
     */
    public function getMonthlyRent()
    {
        return $this->_monthlyRent;
    }

    /**
     * Set Monthly Rent
     *
     * @param string $monthlyRent
     * @return void
     */
    public function setMonthlyRent($monthlyRent)
    {
        $this->_monthlyRent=$monthlyRent;
    }

    /**
     * Get Deposit Amount
     *
     * @return int depositAmount
     */
    public function getDepositAmount()
    {
        return $this->_depositAmount;
    }

   /**
     * Set Deposit Amount
    *
     * @param int $depositAmount
     * @return void
     */

    public function setDepositAmount($depositAmount)
    {
        $this->_depositAmount=$depositAmount;
    }

    /**
     * Get Rent Arrears
     *
     * @return int rentArrears
     */
    public function getRentArrears()
    {
        return $this->_rentArrears;
    }

    /**
     * Set Deposit Amount
     *
     * @param int $depositAmount
     * @return void
     */
    public function setRentArrears($depositAmount)
    {
        $this->_rentArrears=$depositAmount;
    }

    /**
     * Get Tenants vacated
     *
     * @return int tenantsVacated
     */
    public function getTenantVacated()
    {
        return $this->_tenantsVacated;
    }

    /**
     * Set Tenants vacated
     *
     * @param int $tenantsVacated
     * @return void
     */
    public function setTenantVacated($tenantsVacated)
    {
        $this->_tenantsVacated=$tenantsVacated;
    }

    /**
     * Get Tenants vacated Date
     *
     * @return string tenantsVacatedDate
     */
    public function getTenantVacatedDate()
    {
        return $this->_tenantsVacatedDate;
    }

    /**
     * Set Tenants vacated Date
     *
     * @param string $tenantsVacatedDate
     * @return void
     */
    public function setTenantVacatedDate($tenantsVacatedDate)
    {
        $this->_tenantsVacatedDate=$tenantsVacatedDate;
    }

    /**
     * Get First Arrear Date
     *
     * @return string firstArrearDate
     */
    public function getFirstArrearDate()
    {
        return $this->_firstArrearDate;
    }

    /**
     * Set First Arrear Date
     *
     * @param string $firstArrearDate
     * @return void
     */
    public function setFirstArrearDate($firstArrearDate)
    {
        $this->_firstArrearDate=$firstArrearDate;
    }

    /**
     * Get Deposit Received Date
     *
     * @return string depositReceivedDate
     */
    public function getDepositReceivedDate()
    {
        return $this->_depositReceivedDate;
    }

    /**
     * Set Deposit Received Date
     *
     * @param string $depositReceivedDate
     * @return void
     */
    public function setDepositReceivedDate($depositReceivedDate)
    {
        $this->_depositReceivedDate=$depositReceivedDate;
    }

    /**
     * Get Total Guarantors
     *
     * @return int totalGuarantors
     */
    public function getTotalGuarantors()
    {
        return $this->_totalGuarantors;
    }

    /**
     * Set Total Guarantors
     *
     * @param int $totalGuarantors
     * @return void
     */
    public function setTotalGuarantors($totalGuarantors)
    {
        $this->_totalGuarantors=$totalGuarantors;
    }

    /**
     * Get Total Tenants
     *
     * @return int totalTenants
     */
    public function getTotalTenants()
    {
        return $this->_totalTenants;
    }

    /**
     * Set Total Tenants
     *
     * @param int $totalTenants
     * @return void
     */
    public function setTotalTenants($totalTenants)
    {
        $this->_totalTenants=$totalTenants;
    }

    /**
     * Get Additional Information
     *
     * @return string additionalInfo
     */
    public function getAdditionalInfo()
    {
        return $this->_additionalInfo;
    }

    /**
     * Set Additional Information
     *
     * @param string $additionalInfo
     * @return void
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->_additionalInfo=$additionalInfo;
    }

    /**
     * Get Document Confirmation Agent Name
     *
     * @return string docConfirmAgentName
     */
    public function getDocConfirmationAgentName()
    {
        return $this->_docConfirmAgentName;
    }

    /**
     * Set Document Confirmation Agent Name
     *
     * @param string $docConfirmAgentName
     * @return void
     */
    public function setDocConfirmationAgentName($docConfirmAgentName)
    {
        $this->_docConfirmAgentName = $docConfirmAgentName;
    }

    /**
     * You know the drill...
     *
     * @param void
     * @return _data_complete
     */
    public function getDataComplete()
    {
        return $this->_data_complete;
    }

    /**
     * Yawn...
     *
     * @param int $val
     * @return void
     */
    public function setDataComplete($val)
    {
        $this->_data_complete =   $val;
    }

    /**
     * Get if the tenant has housing benefit applied
     *
     * @return _data_complete
     */
    public function getHousingBenefitApplied()
    {
        return $this->_housing_benefit_applied;
    }

    /**
     * Yawn...
     *
     * @param int $val
     * @return void
     */
    public function setHousingBenefitApplied($val)
    {
        $this->_housing_benefit_applied =   $val;
    }

	/**
     * Get the tenancy address Id
     *
     * @return int $_tenancyAddressId
     */
    public function getTenancyAddressId()
    {
        return $this->_tenancyAddressId;
    }

	/**
     * Set the tenancy address Id
     *
     * @param int $_tenancyAddressId Tenancy address Id
     */
    public function setTenancyAddressId($_tenancyAddressId)
    {
        $this->_tenancyAddressId = $_tenancyAddressId;
    }

    /**
     * Set the claim payment bank account name
     *
     * @param $paymentBankAccountName Bank account name
     */
    public function setClaimPaymentBankAccountName($paymentBankAccountName)
    {
        $this->_paymentBankAccountName = $paymentBankAccountName;
    }

    /**
     * Get the claim payment bank account name
     *
     * @return string Claim payment bank account name
     */
    public function getClaimPaymentBankAccountName()
    {
        return $this->_paymentBankAccountName;
    }

    /**
     * Set the claim payment bank account number
     *
     * @param $paymentBankAccountNumber Bank account number
     */
    public function setClaimPaymentBankAccountNumber($paymentBankAccountNumber)
    {
        $this->_paymentBankAccountNumber = $paymentBankAccountNumber;
    }

    /**
     * Get the claim payment bank account number
     *
     * @return string Claim payment bank account number
     */
    public function getClaimPaymentBankAccountNumber()
    {
        return $this->_paymentBankAccountNumber;
    }

    /**
     * Set the claim payment bank account sort code
     *
     * @param $paymentBankAccountSortCode Bank account sort code
     */
    public function setClaimPaymentBankAccountSortCode($paymentBankAccountSortCode)
    {
        $this->_paymentBankAccountSortCode = $paymentBankAccountSortCode;
    }

    /**
     * Get the claim payment bank account sort code
     *
     * @return string Claim payment bank account sort code
     */
    public function getClaimPaymentBankAccountSortCode()
    {
        return $this->_paymentBankAccountSortCode;
    }

    /**
     * Set the recent complaints flag - Yes or No
     *
     * @param $recentComplaints string Recent complaints flag
     */
    public function setRecentComplaints($recentComplaints)
    {
        $this->_recentComplaints = $recentComplaints;
    }

    /**
     * Get the recent complaints flag - Yes or No
     *
     * @return mixed
     */
    public function getRecentComplaints()
    {
        return $this->_recentComplaints;
    }

    /**
     * Set recent complaint details
     *
     * @param $recentComplaintsDetails
     */
    public function setRecentComplaintsDetails($recentComplaintsDetails)
    {
        $this->_recentComplaintsDetails = $recentComplaintsDetails;
    }

    /**
     * Get recent complaint details
     *
     * @return mixed
     */
    public function getRecentComplaintsDetails()
    {
        return $this->_recentComplaintsDetails;
    }

    /**
     * Set the policy number for the claim
     *
     * @param $policyNumber string Policy number
     */
    public function setPolicyNumber($policyNumber)
    {
        $this->_policyNumber = $policyNumber;
    }

    /**
     * Get the policy number for the claim
     *
     * @return string Policy number
     */
    public function getPolicyNumber()
    {
        return $this->_policyNumber;
    }

    /**
     * Set grounds for claim
     *
     * @param $groundsForClaim
     */
    public function setGroundsForClaim($groundsForClaim)
    {
        $this->_groundsForClaim = $groundsForClaim;
    }

    /**
     * Get grounds for claim
     *
     * @return mixed
     */
    public function getGroundsForClaim()
    {
        return $this->_groundsForClaim;
    }

    /**
     * Set grounds for claim details
     *
     * @param $groundsForClaimDetails
     */
    public function setGroundsForClaimDetails($groundsForClaimDetails)
    {
        $this->_groundsForClaimDetails = $groundsForClaimDetails;
    }

    /**
     * Get grounds for claim details
     *
     * @return mixed
     */
    public function getGroundsForClaimDetails()
    {
        return $this->_groundsForClaimDetails;
    }

    /**
     * Set tenants occupation of property confirmed by telephone flag
     *
     * @param $occupationConfirmedByTel
     */
    public function setTenantsOccupationOfPropertyConfirmedByTel($occupationConfirmedByTel)
    {
        $this->_occupationConfirmedByTel = $occupationConfirmedByTel;
    }

    /**
     * Get tenants occupation of property confirmed by telephone flag
     *
     * @return mixed
     */
    public function getTenantsOccupationOfPropertyConfirmedByTel()
    {
        return $this->_occupationConfirmedByTel;
    }

    /**
     * Set tenants occupation of property confirmed by telephone date
     *
     * @param $occupationConfirmedByTelDate
     */
    public function setTenantsOccupationOfPropertyConfirmedByTelDate($occupationConfirmedByTelDate)
    {
        $this->_occupationConfirmedByTelDate = $occupationConfirmedByTelDate;
    }

    /**
     * Get tenants occupation of property confirmed by telephone date
     *
     * @return mixed
     */
    public function getTenantsOccupationOfPropertyConfirmedByTelDate()
    {
        return $this->_occupationConfirmedByTelDate;
    }

    /**
     * Set tenants occupation of property confirmed by telephone contact
     *
     * @param $occupationConfirmedByTelContact
     */
    public function setTenantsOccupationOfPropertyConfirmedByTelContact($occupationConfirmedByTelContact)
    {
        $this->_occupationConfirmedByTelContact = $occupationConfirmedByTelContact;
    }

    /**
     * Get tenants occupation of property confirmed by telephone contact
     *
     * @return mixed
     */
    public function getTenantsOccupationOfPropertyConfirmedByTelContact()
    {
        return $this->_occupationConfirmedByTelContact;
    }

    /**
     * Set tenants occupation of property confirmed by email
     *
     * @param $occupationConfirmedByEmail
     */
    public function setTenantsOccupationOfPropertyConfirmedByEmail($occupationConfirmedByEmail)
    {
        $this->_occupationConfirmedByEmail = $occupationConfirmedByEmail;
    }

    /**
     * Get tenants occupation of property confirmed by email
     *
     * @return mixed
     */
    public function getTenantsOccupationOfPropertyConfirmedByEmail()
    {
        return $this->_occupationConfirmedByEmail;
    }

    /**
     * Set tenants occupation of property confirmed by email date
     *
     * @param $occupationConfirmedByEmailDate
     */
    public function setTenantsOccupationOfPropertyConfirmedByEmailDate($occupationConfirmedByEmailDate)
    {
        $this->_occupationConfirmedByEmailDate = $occupationConfirmedByEmailDate;
    }

    /**
     * Get tenants occupation of property confirmed by email date
     *
     * @return mixed
     */
    public function getTenantsOccupationOfPropertyConfirmedByEmailDate()
    {
        return $this->_occupationConfirmedByEmailDate;
    }

    /**
     * Set tenants occupation of property confirmed by email contact
     *
     * @param $occupationConfirmedByEmailContact
     */
    public function setTenantsOccupationOfPropertyConfirmedByEmailContact($occupationConfirmedByEmailContact)
    {
        $this->_occupationConfirmedByEmailContact = $occupationConfirmedByEmailContact;
    }

    /**
     * Get tenants occupation of property confirmed by email contact
     *
     * @return mixed
     */
    public function getTenantsOccupationOfPropertyConfirmedByEmailContact()
    {
        return $this->_occupationConfirmedByEmailContact;
    }

    /**
     * Set tenants occupation of property confirmed by visit
     *
     * @param $occupationConfirmedByVisit
     */
    public function setTenantsOccupationOfPropertyConfirmedByVisit($occupationConfirmedByVisit)
    {
        $this->_occupationConfirmedByVisit = $occupationConfirmedByVisit;
    }

    /**
     * Get tenants occupation of property confirmed by visit
     *
     * @return mixed
     */
    public function getTenantsOccupationOfPropertyConfirmedByVisit()
    {
        return $this->_occupationConfirmedByVisit;
    }

    /**
     * Set tenants occupation of property confirmed by visit date
     *
     * @param $occupationConfirmedByVisitDate
     */
    public function setTenantsOccupationOfPropertyConfirmedByVisitDate($occupationConfirmedByVisitDate)
    {
        $this->_occupationConfirmedByVisitDate = $occupationConfirmedByVisitDate;
    }

    /**
     * Get tenants occupation of property confirmed by visit date
     *
     * @return mixed
     */
    public function getTenantsOccupationOfPropertyConfirmedByVisitDate()
    {
        return $this->_occupationConfirmedByVisitDate;
    }

    /**
     * Set tenants occupation of property confirmed by visit individual attending
     *
     * @param $occupationConfirmedByVisitIndividual
     */
    public function setTenantsOccupationOfPropertyConfirmedByVisitIndividual($occupationConfirmedByVisitIndividual)
    {
        $this->_occupationConfirmedByVisitIndividual = $occupationConfirmedByVisitIndividual;
    }

    /**
     * Get tenants occupation of property confirmed by visit individual attending
     *
     * @return mixed
     */
    public function getTenantsOccupationOfPropertyConfirmedByVisitIndividual()
    {
        return $this->_occupationConfirmedByVisitIndividual;
    }

    /**
     * Set tenants occupation of property confirmed by visit contact
     *
     * @param $occupationConfirmedByVisitContact
     */
    public function setTenantsOccupationOfPropertyConfirmedByVisitContact($occupationConfirmedByVisitContact)
    {
        $this->_occupationConfirmedByVisitContact = $occupationConfirmedByVisitContact;
    }

    /**
     * Get tenants occupation of property confirmed by visit contact
     *
     * @return mixed
     */
    public function getTenantsOccupationOfPropertyConfirmedByVisitContact()
    {
        return $this->_occupationConfirmedByVisitContact;
    }

    /**
     * Set s21 notice has been served flag
     *
     * @param $s21NoticeServed
     */
    public function setS21NoticeServed($s21NoticeServed)
    {
        $this->_s21NoticeServed = $s21NoticeServed;
    }

    /**
     * Get s21 notice has been served flag
     *
     * @return mixed
     */
    public function getS21NoticeServed()
    {
        return $this->_s21NoticeServed;
    }

    /**
     * Set s21 notice expiry
     *
     * @param $s21NoticeExpiry
     */
    public function setS21NoticeExpiry($s21NoticeExpiry)
    {
        $this->_s21NoticeExpiry = $s21NoticeExpiry;
    }

    /**
     * Get s21 notice expiry
     *
     * @return mixed
     */
    public function getS21NoticeExpiry()
    {
        return $this->_s21NoticeExpiry;
    }

    /**
     * Set s21 money deposit received
     *
     * @param $s21MoneyDepositReceived
     */
    public function setS21NoticeMoneyDepositReceived($s21MoneyDepositReceived)
    {
        $this->_s21MoneyDepositReceived = $s21MoneyDepositReceived;
    }

    /**
     * Get s21 money deposit received
     *
     * @return mixed
     */
    public function getS21NoticeMoneyDepositReceived()
    {
        return $this->_s21MoneyDepositReceived;
    }

    /**
     * Set s21 deposit held under TDS scheme flag
     *
     * @param $s21MoneyDepositHeldUnderTdsScheme
     */
    public function setS21NoticeMoneyDepositHeldUnderTdsScheme($s21MoneyDepositHeldUnderTdsScheme)
    {
        $this->_s21MoneyDepositHeldUnderTdsScheme = $s21MoneyDepositHeldUnderTdsScheme;
    }

    /**
     * Get s21 deposit held under TDS scheme flag
     *
     * @return mixed
     */
    public function getS21NoticeMoneyDepositHeldUnderTdsScheme()
    {
        return $this->_s21MoneyDepositHeldUnderTdsScheme;
    }

    /**
     * Set s21 TDS complied with flag
     *
     * @param $s21TdsCompliedWith
     */
    public function setS21NoticeTdsCompliedWith($s21TdsCompliedWith)
    {
        $this->_s21TdsCompliedWith = $s21TdsCompliedWith;
    }

    /**
     * Get s21 TDS complied with flag
     *
     * @return mixed
     */
    public function getS21NoticeTdsCompliedWith()
    {
        return $this->_s21TdsCompliedWith;
    }

    /**
     * Set the TDS scheme details prescribed to tenant flag
     *
     * @param $s21TdsPrescribedToTenant
     */
    public function setS21NoticeTdsPrescribedToTenant($s21TdsPrescribedToTenant)
    {
        $this->_s21TdsPrescribedToTenant = $s21TdsPrescribedToTenant;
    }

    /**
     * Get the TDS scheme details prescribed to tenant flag
     *
     * @return mixed
     */
    public function getS21NoticeTdsPrescribedToTenant()
    {
        return $this->_s21TdsPrescribedToTenant;
    }

    /**
     * Set the landlord deposit received in property form flag
     *
     * @param $s21LandlordDepositInPropertyForm
     */
    public function setS21NoticeLandlordDepositInPropertyForm($s21LandlordDepositInPropertyForm)
    {
        $this->_s21LandlordDepositInPropertyForm = $s21LandlordDepositInPropertyForm;
    }

    /**
     * Get the landlord deposit received in property form flag
     *
     * @return mixed
     */
    public function getS21NoticeLandlordDepositInPropertyForm()
    {
        return $this->_s21LandlordDepositInPropertyForm;
    }

    /**
     * Set the property returned at the s21 notice serve date flag
     *
     * @param $s21PropertyReturnedByServeDate
     */
    public function setS21NoticePropertyReturnedAtNoticeServeDate($s21PropertyReturnedByServeDate)
    {
        $this->_s21PropertyReturnedByServeDate = $s21PropertyReturnedByServeDate;
    }

    /**
     * Get the property returned at the s21 notice serve date flag
     *
     * @return mixed
     */
    public function getS21NoticePropertyReturnedAtNoticeServeDate()
    {
        return $this->_s21PropertyReturnedByServeDate;
    }

    /**
     * Set the s8 notice served flag
     *
     * @param $s8NoticeServed
     */
    public function setS8NoticeServed($s8NoticeServed)
    {
        $this->_s8NoticeServed = $s8NoticeServed;
    }

    /**
     * Get the s8 notice served flag
     *
     * @return mixed
     */
    public function getS8NoticeServed()
    {
        return $this->_s8NoticeServed;
    }

    /**
     * Set the s8 notice expiry date
     *
     * @param $s8NoticeExpiry
     */
    public function setS8NoticeExpiry($s8NoticeExpiry)
    {
        $this->_s8NoticeExpiry = $s8NoticeExpiry;
    }

    /**
     * Get the s8 notice expiry date
     *
     * @return mixed
     */
    public function getS8NoticeExpiry()
    {
        return $this->_s8NoticeExpiry;
    }

    /**
     * Set the s8 demand letter sent flag
     *
     * @param $s8DemandLetterSent
     */
    public function setS8NoticeDemandLetterSent($s8DemandLetterSent)
    {
        $this->_s8DemandLetterSent = $s8DemandLetterSent;
    }

    /**
     * Get the s8 demand letter sent flag
     *
     * @return mixed
     */
    public function getS8NoticeDemandLetterSent()
    {
        return $this->_s8DemandLetterSent;
    }

    /**
     * Set the details of over occupants within the property
     *
     * @param $s8Over18Occupants
     */
    public function setS8NoticeOver18Occupants($s8Over18Occupants)
    {
        $this->_s8Over18Occupants = $s8Over18Occupants;
    }

    /**
     * Get the details of over occupants within the property
     *
     * @return mixed
     */
    public function getS8NoticeOver18Occupants()
    {
        return $this->_s8Over18Occupants;
    }

    /**
     * Set the tenants forwarding address id
     *
     * @param $tenantsForwardingAddressId
     */
    public function setTenantsForwardingAddressId($tenantsForwardingAddressId)
    {
        $this->_tenantsForwardingAddressId = $tenantsForwardingAddressId;
    }

    /**
     * Get the tenants forwarding address id
     *
     * @return mixed
     */
    public function getTenantsForwardingAddressId()
    {
        return $this->_tenantsForwardingAddressId;
    }

    /**
     * Set the full tenants forwarding address
     *
     * @param $tenantsForwardingAddress string Tenants forwarding address
     */
    public function setTenantsForwardingAddress($tenantsForwardingAddress)
    {
        $this->_tenantsForwardingAddress = $tenantsForwardingAddress;
    }

    /**
     * Get the full tenants forwarding address
     *
     * @return string Tenants forwarding address
     */
    public function getTenantsForwardingAddress()
    {
        return $this->_tenantsForwardingAddress;
    }

    /**
     * Set the tenants forwarding address house name
     *
     * @param $tenantsForwardingHouseName
     */
    public function setTenantsForwardingHouseName($tenantsForwardingHouseName)
    {
        $this->_tenantsForwardingHouseName = $tenantsForwardingHouseName;
    }

    /**
     * Get the tenants forwarding address house name
     *
     * @return mixed
     */
    public function getTenantsForwardingHouseName()
    {
        return $this->_tenantsForwardingHouseName;
    }

    /**
     * Set the tenants forwarding address street
     *
     * @param $tenantsForwardingStreet
     */
    public function setTenantsForwardingStreet($tenantsForwardingStreet)
    {
        $this->_tenantsForwardingStreet = $tenantsForwardingStreet;
    }

    /**
     * Get the tenants forwarding address street
     *
     * @return mixed
     */
    public function getTenantsForwardingStreet()
    {
        return $this->_tenantsForwardingStreet;
    }

    /**
     * Set the tenants forwarding address town
     *
     * @param $tenantsForwardingTown
     */
    public function setTenantsForwardingTown($tenantsForwardingTown)
    {
        $this->_tenantsForwardingTown = $tenantsForwardingTown;
    }

    /**
     * Get the tenants forwarding address town
     *
     * @return mixed
     */
    public function getTenantsForwardingTown()
    {
        return $this->_tenantsForwardingTown;
    }

    /**
     * Set the tenants forwarding address city
     *
     * @param $tenantsForwardingCity
     */
    public function setTenantsForwardingCity($tenantsForwardingCity)
    {
        $this->_tenantsForwardingCity = $tenantsForwardingCity;
    }

    /**
     * Get the tenants forwarding address city
     *
     * @return mixed
     */
    public function getTenantsForwardingCity()
    {
        return $this->_tenantsForwardingCity;
    }

    /**
     * Set the tenants forwarding address postcode
     *
     * @param $tenantsForwardingPostcode
     */
    public function setTenantsForwardingPostcode($tenantsForwardingPostcode)
    {
        $this->_tenantsForwardingPostcode = $tenantsForwardingPostcode;
    }

    /**
     * Get the tenants forwarding address postcode
     *
     * @return mixed
     */
    public function getTenantsForwardingPostcode()
    {
        return $this->_tenantsForwardingPostcode;
    }

    /**
     * Set the landlord is the property proprietor flag
     *
     * @param $landlordIsPropertyProprietor
     */
    public function setLandlordIsPropertyProprietor($landlordIsPropertyProprietor)
    {
        $this->_landlordIsPropertyProprietor = $landlordIsPropertyProprietor;
    }

    /**
     * Get the landlord is the property proprietor flag
     *
     * @return mixed
     */
    public function getLandlordIsPropertyProprietor()
    {
        return $this->_landlordIsPropertyProprietor;
    }

    /**
     * Set the agent is AR flag
     *
     * @param $isAr
     */
    public function setIsAr($isAr)
    {
        $this->_isAr = $isAr;
    }

    /**
     * Get the agent is AR flag
     *
     * @return mixed
     */
    public function getIsAr()
    {
        return $this->_isAr;
    }

    /**
     * Set the agent is DIR flag
     *
     * @param $isDir
     */
    public function setIsDir($isDir)
    {
        $this->_isDir = $isDir;
    }

    /**
     * Get the agent is DIR flag
     *
     * @return mixed
     */
    public function getIsDir()
    {
        return $this->_isDir;
    }

    /**
     * Set the amount of arrears at the point vacant possession was obtained
     *
     * @param $arrearsAtVacantPossession string Arrears at vacant possession
     */
    public function setArrearsAtVacantPossession($arrearsAtVacantPossession)
    {
        $this->_arrearsAtVacantPossession = $arrearsAtVacantPossession;
    }

    /**
     * Get the amount of arrears at the point vacant possession was obtained
     *
     * @return mixed Arrears at vacant possession
     */
    public function getArrearsAtVacantPossession()
    {
        return $this->_arrearsAtVacantPossession;
    }

    /**
     * Set the agents authority confirmation
     *
     * @param $authConfirmed int Authority confirmation
     */
    public function setAuthorityConfirmed($authConfirmed)
    {
        $this->_authConfirmed = $authConfirmed;
    }

    /**
     * Get the agents authority confirmation
     *
     * @return int Authority confirmation
     */
    public function getAuthorityConfirmed()
    {
        return $this->_authConfirmed;
    }

    /**
     * Set the agents declaration confirmation
     *
     * @param $decConfirmed int Declaration confirmation
     */
    public function setDeclarationConfirmed($decConfirmed)
    {
        $this->_decConfirmed = $decConfirmed;
    }

    /**
     * Get the agents declaration confirmation
     *
     * @return int Declaration confirmation
     */
    public function getDeclarationConfirmed()
    {
        return $this->_decConfirmed;
    }
}
