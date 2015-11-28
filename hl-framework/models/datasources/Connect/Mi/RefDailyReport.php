<?php

final class Datasource_Connect_Mi_RefDailyReport extends Zend_Db_Table_Multidb
{
    /**#@+
     * Mandatory attributes
     */
     
    protected $_name = 'daily_reports';
    protected $_primary = 'Reference_Number';
    protected $_multidb = 'db_homelet_connect';
    /**#@-*/
	
    public function fetchLiveByASN($agentschemeno){
    	$dataObject = new Model_Core_ManagementInformation_DailyReports();
    	$fields = array(
    			'Section' => 'Section',
    			'Reference_Number' => 'Reference_Number',
    			'Property_Address' => 'Property_Address',
    			'Tenant_Name' => 'Tenant_Name',
    			'Type'  => 'Type',
    			'Employer_Ref' => 'Employer_Ref',
    			'Future_Emp_Ref' => 'Future_Emp_Ref',
    			'Landlord_Ref'  => 'Landlord_Ref',
    			// 'Final_Decision' => 'Final_Decision',
    			// 'Reason' => 'Reason',
    			'Notes' => 'Notes',
    			'Agent_Scheme_Number' => 'Agent_Scheme_Number'
    			);
    	$select = $this->select()
    	->from($this->_name, $fields)
    	->where('Agent_Scheme_Number = ? AND Section = "Live"', $agentschemeno);
   // 	die ($select->__toString());
    	$rowset = $this->fetchAll($select);
    	return $rowset;
    }
    
    public function fetchTemporaryByASN($agentschemeno){
    	$dataObject = new Model_Core_ManagementInformation_DailyReports();
    	$fields = array(
    			'Section' => 'Section',
    			'Reference_Number' => 'Reference_Number',
    			'Property_Address' => 'Property_Address',
    			'Tenant_Name' => 'Tenant_Name',
    			'Type'  => 'Type',
    			'Employer_Ref' => 'Employer_Ref',
    			'Future_Emp_Ref' => 'Future_Emp_Ref',
    			'Landlord_Ref'  => 'Landlord_Ref',
    			// 'Final_Decision' => 'Final_Decision',
    			// 'Reason' => 'Reason',
    			'Notes' => 'Notes',
    			'Agent_Scheme_Number' => 'Agent_Scheme_Number'
    			);
    	$select = $this->select()
    	->from($this->_name, $fields)
    	->where('Agent_Scheme_Number = ? AND Section = "Temporary"', $agentschemeno);
   // 	die ($select->__toString());
    	$rowset = $this->fetchAll($select);
    	return $rowset;
    }
    
    public function fetchCompleteByASN($agentschemeno){
    	$dataObject = new Model_Core_ManagementInformation_DailyReports();
    	$fields = array(
    			'Section' => 'Section',
    			'Reference_Number' => 'Reference_Number',
    			'Property_Address' => 'Property_Address',
    			'Tenant_Name' => 'Tenant_Name',
    			'Type'  => 'Type',
    			// 'Employer_Ref' => 'Employer_Ref',
    			// 'Future_Emp_Ref' => 'Future_Emp_Ref',
    			// 'Landlord_Ref'  => 'Landlord_Ref',
    			'Final_Decision' => 'Final_Decision',
    			'Reason' => 'Reason',
    			'Notes' => 'Notes',
    			'Agent_Scheme_Number' => 'Agent_Scheme_Number'
    			);
    	$select = $this->select()
    	->from($this->_name, $fields)
    	->where('Agent_Scheme_Number = ? AND Section = "Complete"', $agentschemeno);
   // 	die ($select->__toString());
    	$rowset = $this->fetchAll($select);
    	return $rowset;
    }

}
