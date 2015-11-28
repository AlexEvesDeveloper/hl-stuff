<?php
/**
 * Model definition for the agent emails table
 *
 * @category   Datasource
 * @package    Datasource_Core
 * @subpackage Agent
 */

class Datasource_Core_Agent_Emailaddresses extends Zend_Db_Table_Multidb {
    protected $_name = 'agent_emails';
    protected $_primary = 'scheme_number';
    protected $_multidb = 'db_legacy_homelet';
    protected $_dependentTables = array ('Datasource_Core_Agent_EmailaddressCategories');
    protected $_referenceMap = array(
        'Emailaddresses' => array(
            'columns'       =>  array('scheme_number'),
            'refTableClass' =>  'Datasource_Core_Agents',
            'refColumns'    =>  array('agentschemeno')
    ));

    /**
     * Look up and return the e-mail addresses associated with an agent.
     *
     * @todo Fix category to be a DB lookup and not rely on the domain object hardcoded constants.
     *
     * @param mixed $agentSchemeNumber
     *
     * @return array Array of Model_Core_Agent_EmailMap
     */
    public function getEmailAddresses($agentSchemeNumber) {
        // Get e-mail addresses
        $select = $this->select();
        $select->where('scheme_number = ? and category_id not in (4,5)', $agentSchemeNumber);
        $emailAddressArray = $this->fetchAll($select);

        $returnArray = array();
        foreach($emailAddressArray as $emailAddress) {
            $emailMap = new Model_Core_Agent_EmailMap();
            // TODO: This next line is using the domain object's category ID mapping - fix!
            $emailMap->category = $emailAddress['category_id'];
            $emailMap->emailAddress = new Model_Core_EmailAddress();
            $emailMap->emailAddress->emailAddress = $emailAddress['email_address'];
            $returnArray[] = $emailMap;
        }
        return $returnArray;
    }

    /**
     * @todo: Fix this so it's not messing around with arrays.
     *
     * @param unknown_type $schemeNumber
     * @param unknown_type $emailAddresses
     */
    public function setEmailAddresses($schemeNumber, $emailAddresses) {
        // First of all check to make sure this is a valid agent schemeNumber
        $agent = new Datasource_Core_Agents();
        $agentSelect = $agent->getBySchemeNumber($schemeNumber);
        $agentRow = $agent->fetchRow($agentSelect);
        if (!$agentRow) return false;

        // There is no 'replace' functionality in zend as it is, sadly, mysql specific
        // so we have to do a delete then an insert

        // First build a select object to get all email addresses for the specified agent
        $where = $this->quoteInto('scheme_number = ?', $schemeNumber);
        // Then delete them
        if (!$this->delete($where)){
            //return false;
        }

        // Now build the insert array
        foreach ($emailAddresses as $emailAddress) {
			
			$tempEmailAddress = '';
			$tempCategoryId = '';
			
			if(is_array($emailAddress)) {
				
				$tempEmailAddress = $emailAddress['emailAddress'];
				$tempCategoryId = $emailAddress['categoryID'];
			}
			else if(is_object($emailAddress)) {
				
				$tempEmailAddress = $emailAddress->emailAddress;
				$tempCategoryId = $emailAddress->categoryID;
			}
			else {
				
				Application_Core_Logger::log("Datatype unknown when changing email address for agent {$schemeNumber}.", 'error');
				return false;
			}
			
			if ($tempEmailAddress != '')
			{
				$data = array(
					'scheme_number'     =>  $schemeNumber,
					'email_address'     =>  $tempEmailAddress,
					'category_id'       =>  $tempCategoryId
				);

				if (!$this->insert($data)) {
					// Failed insertion
					Application_Core_Logger::log("Can't insert e-mail address in table {$this->_name} (scheme_number = {$schemeNumber})", 'error');
					return false;
				}
			}
        }
        return true;
    }

    /**
    * Filters results by an email category
    *
    * @param int agentSchemeNo
    * @return Zend_Db_Select
    *
    */
    public function filterByCategory($categoryID) {

        $select = $this->select();
        return $this->select()->where('category_id = ?', $categoryID);
    }
}
?>
