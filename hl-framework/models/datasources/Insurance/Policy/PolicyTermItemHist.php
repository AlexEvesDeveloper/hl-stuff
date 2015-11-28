<?php

/**
 * Model definition for the table PolicyTermItemHist
 * This table is policy coverage term image from finance purpose
 * 
 */
class Datasource_Insurance_Policy_PolicyTermItemHist extends Zend_Db_Table_Multidb {
	protected $_name = 'PolicyTermItemHist';
	protected $_primary = 'id';
	protected $_multidb = 'db_legacy_homelet';
	
	/**
	 * Fetch a row from 
	 * * @param object $disb Id
	 *
	 * @return netpremium
	 */
	public function getRowToDisb($disb) {
		
		$select = $this->select ()->from ( $this->_name )
					   ->where ( 'policynumber = ?', $disb->_policynumber )
					   ->where ( 'policyOptionID = ?', $disb->_optionID)
					   ->where ( 'policyTermid = ?', $disb->_termid );
		$row = $this->fetchRow ( $select );
		
		if (! empty ( $row )) {
			return $row->netpremium;
		
		} else {
			return false;
		}
	}
	
	/**
	 * Insert a new row into the PolicyTermItemHist table
	 *
	 * @param Model_Policy_PolicyTermItemHist $data The data to be inserted
	 *
	 * 
	 */
	public function setItemHist($policy, $cover) {
		// Remove existing policy term item history record (we don't have a replace into command)
		

				
		$where = $this->quoteInto ( 'policyNumber = ? and policyOptionID = ? ', $policy->policyNumber, $cover->policyOptionID);
		
		$this->delete ( $where );
		
		$insertArray = array ();
		$insertArray ['policynumber'] = $policy->policyNumber;
		$insertArray ['policyTermid'] = $policy->termid ;
		$insertArray ['policyOptionID'] = $cover->policyOptionID;
		$insertArray ['startdate'] = $policy->startDate;
		$insertArray ['enddate'] = $policy->endDate;
		$insertArray ['sumInsued'] = $cover->sumInsured;
		$insertArray ['grosspremium'] = $cover->grosspremium; //annually value
		$insertArray ['premium'] = $cover->premium; //annually value
		$insertArray ['netpremium'] = $cover->netpremium; //annually value 
		$insertArray ['ipt'] = $cover->ipt; //annually value
		$insertArray ['monthremaining'] = $policy->policyLength;
		$this->insert ( $insertArray );
	
	}
	
	/**
	 * TODO: Document this
	 * @param policy object $policy, termid
	 * @return
	 * @author Jun Zhang
	 */
	public function UpdateTerm($policy, $termid) {
		$where = $this->quoteInto ( 'policyNumber = ? and startdate = ?', $policy->policyNumber, $policy->startDate );
		$updatedData = array ('policyTermid' => $termid );
		return $this->update ( $updatedData, $where );
	}
	
	/**
	 * Description given in the IChangeable interface.
	 */
	public function changeQuoteToPolicy($quoteNumber, $policyNumber = null) {
		
		//If policyNumber is empty then assume the QHLI should be replaced with PHLI.
		if (empty ( $policyNumber )) {
			
			$policyNumber = preg_replace ( '/^Q/', 'P', $quoteNumber );
		}
		
		$where = $this->quoteInto ( 'policyNumber = ?', $quoteNumber );
		$updatedData = array ('policyNumber' => $policyNumber );
		return $this->update ( $updatedData, $where );
	}
}

?>