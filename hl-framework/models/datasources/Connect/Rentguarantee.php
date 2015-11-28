<?php
class Datasource_Connect_Rentguarantee extends Zend_Db_Table_Abstract
{
    protected $_name = 'agentid';
    protected $_primary = 'agentid';
    protected $_dbname = 'db_legacy_homelet';
    protected $_insuranceDb, $_homelet, $_homeletDB, $_homeletUK;
    
    function __construct() {
        // Make this model use the referencing database connection
        $this->_db             = Zend_Registry::get('db_legacy_homelet');
        $this->_insuranceDb = Zend_Registry::get('db_homelet_insurance_com');
        $this->_homelet     = Zend_Registry::get('db_legacy_homelet')->getConfig();    
        $this->_homeletDB    = $this->_homelet['dbname'];    
        $this->_homeletUK = Zend_Registry::get('db_legacy_homelet');
    }
    
    /**
     *
     * Gets Absolute Application datas in an array
     *
     * @return array
     *
     */
    public function getAbsoluteApplication($agentid)
    {
        $absoluteApplicationSelect = $this->select();
        $absoluteApplicationSelect->setIntegrityCheck(false);
        $absoluteApplicationSelect->from(array('ai' => 'agentid'), array('realname', 'username'));
        $absoluteApplicationSelect->join(array('na' => 'newagents'), 'ai.agentschemeno = na.agentschemeno', array('name', 'logo'));
        $absoluteApplicationSelect->join(array('ab' => 'absoluteType'),
                                         'na.absoluteType_id = ab.id', 
                                         array('absoluteTypeId' => 'id', 'absoluteTypeName' => 'name', 'absoluteLogo' => 'logo' ,'absoluteLogoAlt' => 'logoAlt'));
        
        $absoluteApplicationSelect->where('ai.agentid = ?',$agentid);
        $absoluteApplicationSelect->limit('1');
        $absoluteApplicationRow = $this->fetchRow($absoluteApplicationSelect);
        
        return $absoluteApplicationRow->toArray();
    }
    
    /**
     *
     * Gets find address by postcode datas in an array
     *
     * @return array
     *
     */
    public function getAddressByPostcode($postcode)
    {
        $findAddressSelect = $this->_insuranceDb->select();
        $findAddressSelect->from(array('p' => 'postcode_merge'), array('ORD', 'ORG', 'SBN', 'BNA', 'POB', 'NUM', 'address1', 'address2', 'address3', 'address4', 'address5', 'id'));
        $findAddressSelect->where('postcode = ?',$postcode);
        $findAddressRow = $this->_insuranceDb->fetchAll($findAddressSelect);
        
        return $findAddressRow;
    }
    
    /**
     *
     * Gets find address by id datas in an array
     *
     * @return array
     *
     */
    public function getAddressById($id)
    {
        $findAddressIdSelect = $this->_insuranceDb->select();
        $findAddressIdSelect->from(array('p' => 'postcode_merge'), array('ORD', 'ORG', 'SBN', 'BNA', 'POB', 'NUM', 'address1', 'address2', 'address3', 'address4', 'address5', 'postcode'));
        $findAddressIdSelect->where('id = ?',$id);
        $findAddressIdRow = $this->_insuranceDb->fetchRow($findAddressIdSelect);
        
        return $findAddressIdRow;
    }
    
    /**
     *
     * Gets find address by id datas in an array
     *
     * @return array
     *
     */
    public function getAgentDetail($agentschemeno)
    {
        $agentDetailSelect = $this->select();
        $agentDetailSelect->setIntegrityCheck(false);
        $agentDetailSelect->from(array('a' => 'newagents'), array('name', 'address1', 'address2', 'address3', 'address4', 'postcode', 'phone'));
        $agentDetailSelect->where('agentschemeno = ?',$agentschemeno);
        $agentDetailSelect->limit('1');
        
        $agentDetailRow = $this->fetchRow($agentDetailSelect);
        
        return $agentDetailRow;
    }
    
    /**
      * 
      * Gets the Rent Guarantee Renewal invited policies for the given Agent Scheme Number
      * @param int $agentSchemeNumber
      * @param String $policyNumber
      * @return Array
      */
     public function getRentGuaranteeRenewalInvites($agentSchemeNumber, $policyNumber)
     {
        $renewalData = array();
        $refUk = Zend_Registry::get('db_legacy_referencing')->getConfig();
        $refUkDB = $refUk['dbname'];
        
        $select = $this->_homeletUK->select();
        $select->distinct();
        
        $select->from
        (
            array('p' => 'policy'),
            array
            (
                'policynumber',
                'propaddress1',
                'propaddress3',
                'proppostcode',
                'enddate',
                'expirestoday' => new Zend_db_Expr('IF (enddate = CURDATE(), 1, 0)')
            )
        );
        
        $select->join
        (
            array('r' => $refUkDB . '.Enquiry'),
            'p.policynumber = r.policynumber',
            null
        );
        
        $select->join
        (
            array('t' => $refUkDB . '.Tenant'),
            't.ID = r.TenantID',
            array('firstname','lastname')
        );
        
        $select->joinLeft
        (
            array('d' => 'DECLINEDRENEWAL'),
            'd.policynumber = p.policynumber',
            null
        );
        
        $select->where('r.renewal = 0');
        $select->where('r.Guarantor = 0');
        $select->where('p.enddate >= CURDATE()');
        $select->where('DATE_SUB(p.enddate, interval 28 day) <= CURDATE()');
        $select->where('d.policynumber IS NULL');
        $select->where('p.companyschemenumber = ?', $agentSchemeNumber);
        $select->where('p.paystatus = "RenewalInvited"');
        $select->where('p.policynumber like "PRGI%"');
        
        if ($policyNumber != "")
        {
            $select->where('p.policynumber like ? ', '%' . $policyNumber . '%');
        }
        
        $select->order(array('enddate DESC','policynumber ASC'));
        
        return $this->_homeletUK->fetchAll($select);
    }
    
    /**
      * 
      * Gets the Rent Guarantee Renewal overdue policies for the given Agent Scheme Number
      * @param int $agentSchemeNumber
      * @param String $policyNumber
      * @return Array
      */
    public function getRentGuaranteeRenewalOverdues($agentSchemeNumber, $policyNumber)
    {
        $renewalData = array();
        $refUk = Zend_Registry::get('db_legacy_referencing')->getConfig();
        $refUkDB = $refUk['dbname'];
        
        $select = $this->_homeletUK->select();
        $select->distinct();
        
        $select->from
        (
            array('p' => 'policy'),
            array
            (
                'policynumber',
                'propaddress1',
                'propaddress3',
                'proppostcode',
                'enddate',
            )
        );
        
        $select->join
        (
            array('r' => $refUkDB . '.Enquiry'),
            'p.policynumber = r.policynumber',
            null
        );
        
        $select->join
        (
            array('t' => $refUkDB . '.Tenant'),
            't.ID = r.TenantID',
            array('firstname','lastname')
        );
        
        $select->joinLeft
        (
            array('d' => 'DECLINEDRENEWAL'),
            'd.policynumber = p.policynumber',
            null
        );
        
        $select->where('r.renewal = 0');
        $select->where('r.Guarantor = 0');
        $select->where('p.enddate < CURDATE()');
        $select->where('p.enddate > DATE_SUB(CURDATE(), interval 28 day)');
        $select->where('d.policynumber IS NULL');
        $select->where('p.companyschemenumber = ?', $agentSchemeNumber);
        $select->where('p.paystatus = "RenewalInvited"');
        $select->where('p.policynumber like "PRGI%"');
        
        if ($policyNumber != "")
        {
            $select->where('p.policynumber like ? ', '%' . $policyNumber . '%');
        }
        
        $select->order(array('enddate DESC','policynumber ASC'));
        return $this->_homeletUK->fetchAll($select);
    }
    
    /**
     * Returns a count of the number of policies that are due to lapse
     * today
     *
     * @param int $agentSchemeNumber Agent scheme number
     * @param string $policyNumber Policy number
     * @return int Policy lapsing today count
     */
    public function getExpiresTodayCount($agentSchemeNumber, $policyNumber)
    {
        $renewalData = array();
        $refUk = Zend_Registry::get('db_legacy_referencing')->getConfig();
        $refUkDB = $refUk['dbname'];
        
        $select = $this->_homeletUK->select();
        $select->distinct();
        
        $select->from
        (
            array('p' => 'policy'),
            array
            (
                'expirestoday' => new Zend_db_Expr('count(*)')
            )
        );
        
        $select->join
        (
            array('r' => $refUkDB . '.Enquiry'),
            'p.policynumber = r.policynumber',
            null
        );
        
        $select->join
        (
            array('t' => $refUkDB . '.Tenant'),
            't.ID = r.TenantID',
            null
        );
        
        $select->joinLeft
        (
            array('d' => 'DECLINEDRENEWAL'),
            'd.policynumber = p.policynumber',
            null
        );
        
        $select->where('p.enddate = CURDATE()');
        $select->where('r.renewal = 0');
        $select->where('r.Guarantor = 0');
        $select->where('d.policynumber IS NULL');
        $select->where('p.companyschemenumber = ?', $agentSchemeNumber);
        $select->where('p.paystatus = "RenewalInvited"');
        $select->where('p.policynumber like "PRGI%"');
        
        if ($policyNumber != "")
        {
            $select->where('p.policynumber like ? ', '%' . $policyNumber . '%');
        }
        
        $row = $this->_homeletUK->fetchAll($select);
        return $row[0]['expirestoday'];
    }
    
    /**
     * Search Address by Postcode, House Name/Number, Street 
     * @param string $mode(find_address/find_postcode)
     * @param string $postcode
     * @param string $house
     * @param string $street
     */
    public function searchAddress($mode, $postcode="", $house="") {
        $_insuranceCom = Zend_Registry::get('db_homelet_insurance_com');
        $select = $_insuranceCom->select();
        $select->from('postcode_merge',array('ORD', 'ORG', 'SBN', 'BNA', 'POB', 'NUM', 'address1', 'address2', 'address3', 'address4', 'address5', 'id', 'postcode'));
        
        if($mode == "find_address") {    //Find Address by Postcode or Housename/number
            $select->where("postcode=?",$postcode);
            // If a house is specified, add it to the search
            if ($house != "") {
                $select->where("BNA = '".$house."' OR NUM = '".$house."'");
            }
        }
        else if($mode == "find_postcode") {
            $select->where("postcode like ?", $postcode."%");
            if(preg_match('/\d/', $house)) {
                $select->where("NUM like ?", $house."%");
            } else if ($house != '') {
                $select->where("BNA like ?", $house."%");
            }
            $select->group("postcode");
        }
        $data = $_insuranceCom->fetchAll($select);
        if($data)
            return $data;
        else 
            return array();
    }
}
