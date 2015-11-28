<?php

class Manager_Insurance_Policy_Rentguarantee
{
    /**
     * Policy model, for retrieving the policy side of the RG policy
     * @var Datasource_Insurance_LegacyPolicies
     */
    protected $_policyModel;
    
    /**
     * Enquiry model, for retrieving the reference side of the RG policy
     * @var Datasource_ReferencingLegacy_Enquiry
     */
    protected $_enquiryModel;
    
    /**
     * Declined renewal model, for managing the declining of renewals
     * @var Datasource_Insurance_Policy_DeclineRenewal
     */
    protected $_declinerenewalModel;
    
    /**
     * Declined renewal questions model, for retrieving data about questions around declined renewals
     * @var Datasource_Insurance_Policy_DeclineRenewalQuestions
     */
    protected $_declinerenewalquestionsModel;
    
    /**
     * Declined renewal responses model, for applying reasons for non renewal
     * @var Datasource_Insurance_Policy_DeclineRenewalResponses
     */
    protected $_declinerenewalresponsesModel;
    
    /**
     * Set up the object
     */
    public function __construct()
    {
        $this->_policyModel = new Datasource_Insurance_LegacyPolicies();
        $this->_enquiryModel = new Datasource_ReferencingLegacy_Enquiry();
        
        $this->_declinerenewalModel = new Datasource_Insurance_Policy_DeclineRenewal();
        $this->_declinerenewalquestionsModel = new Datasource_Insurance_Policy_DeclineRenewalQuestions();
        $this->_declinerenewalresponsesModel = new Datasource_Insurance_Policy_DeclineRenewalResponses();
    }
    
    /**
     * Get policy details of a rent guarantee policy
     *
     * @param string $policynumber Rent guarantee policy number
     * @return Model_Insurance_Quote Quote object, representing the policy, populated with data
     */
    public function getPolicyDetails($policynumber)
    {
        return $this->_policyModel->getByPolicyNumber($policynumber);
    }
    
    /**
     * Get reference details of a rent guarantee policy
     *
     * @param string $policynumber Rent guarantee policy number
     * @return Model_ReferencingLegacy_Enquiry Reference object, representing the reference, populated with data
     */
    public function getReference($policynumber)
    {
        $enquiryId = $this->_enquiryModel->getExternalIdentifierByPolicyNumber($policynumber);
        return $this->_enquiryModel->getEnquiry($enquiryId);
    }
    
    /**
     * Get product premiums for month term size, linked to reference and agent
     *
     * @param int $months Months to retrieve premium for, should be 6 or 12.
     * @param int $irn Enquiry internal ID number to link pricing deals to to get product premiums
     * @return float Premium
     */
    public function getRenewalPremium($months, $irn)
    {
        return $this->_enquiryModel->getRenewalPremium($months, $irn);
    }
    
    /**
     * Decline a rent guarantee policy renewal by populating the declined renewal table
     * and recording the reasons for non-renewal.
     *
     * @param string $policynumber Policy number
     * @param string $reason Reason label for non renewal
     * @param string $why Option reason why renewal was not performed
     * @param string $name Name of individual who declined the renewal
     * @param string $from Source of change
     * @return void
     */
    public function declinePolicyRenewal($policynumber, $reason, $why, $name, $from)
    {
        // Record the response reason
        $questionid = $this->_declinerenewalquestionsModel->getQuestionId($reason);
        $this->_declinerenewalresponsesModel->addResponse($policynumber, $questionid, $reason, $why);
        
        // add the policy to the black list table
        $this->_declinerenewalModel->declinePolicyRenewal($policynumber);
        
        // Add note to policy numbers
        $date = date("Y-m-d h:i:s a");
        $note = $name . " selected not to renew Rent Guarantee on $from $date. Reason stated: $reason.\n\n";
        
        $policynotesmodel = new Datasource_Insurance_PolicyNotes();
        $policynotesmodel->addNote($policynumber, $note);
    }
    
    /**
     * Get the declined renewal status of a policy.
     *
     * @param string $policynumber Policy number to check status of
     * @return bool True if renewal declined, false if not
     */
    public function isPolicyDeclined($policynumber)
    {
        return $this->_declinerenewalModel->isPolicyDeclined($policynumber);
    }
}
