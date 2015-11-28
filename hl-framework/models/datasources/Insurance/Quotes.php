<?php
class Datasource_Insurance_Quotes extends Zend_Db_Table_Multidb {
	protected $_multidb = 'db_homelet';    
	protected $_name = 'quotes';
    protected $_primary = 'id';
	
	/**
	 * Find a quote by it's ID
	 * 
	 * @param int id
	 * @return Model_Insurance_LandlordsPlus_Quote
	 *
	 */
	public function getByID($id) {
		$quoteModel = new Model_Insurance_Quote();
		
		$select = $this->select();
		$select->where('id = ?', $id);
		
		$quoteRow = $this->fetchRow($select);
		if (count($quoteRow)>0) {
			// We've successfully found a quote with this ID so we can populate the model and return it
			$quoteModel->ID 				= $quoteRow->id;
			$quoteModel->legacyID			= $quoteRow->legacy_id;
			$quoteModel->customerID			= $quoteRow->customer_id;
			$quoteModel->legacyCustomerID 	= $quoteRow->legacy_customer_id;
			$quoteModel->issuedDate			= $quoteRow->issued_date;
			$quoteModel->startDate			= $quoteRow->start_date;
			$quoteModel->endDate			= $quoteRow->end_date;
			$quoteModel->status 			= $quoteRow->status;
			$quoteModel->agentSchemeNumber  = $quoteRow->agent_scheme_number;
			$quoteModel->payBy				= $quoteRow->pay_by;
			$quoteModel->payFrequency		= $quoteRow->pay_frequency;
			$quoteModel->premium            =0;
			$quoteModel->ipt                =0;
		    $quoteModel->policyNumber       =$quoteRow->legacy_id;
		    $quoteModel->policyLength       =12;
			
			return $quoteModel;
		} else {
			// No matching record
			return false;
		}
	}
	
	/**
	 * Find a new quote ID using just a legacy quote ID
	 *
	 * @return int ID
	 */
	public function getIDByLegacyID($legacyID) {
		$quoteModel = new Model_Insurance_Quote();
		
		$select = $this->select();
		$select->where('legacy_id = ?', $legacyID);
		
		$quoteRow = $this->fetchRow($select);
		if (count($quoteRow)>0) {
			return $quoteRow->id;
		}
		return null;
	}
	
	/**
	 * Find a new quote ID using an old legacy customer reference number
	 *
	 * @return int ID
	 * @munt 8
	 * Please don't use this!
	 */
	public function getIDByReferenceNumber($referenceNumber) {
		$quoteModel = new Model_Insurance_Quote();
		
		$select = $this->select();
		$select->where('legacy_customer_id = ?', $referenceNumber);
		
		$quoteRow = $this->fetchRow($select);
		if (count($quoteRow)>0) {
			return $quoteRow->id;
		}
		return null;
	}	
	
	/**
	 * Delete a quote by it's ID
	 *
	 */
	public function deleteByID($id) {
		$where = $this->quoteInto('id = ?',$id);
		$this->delete($where);
	}
	
	/**
	 * Save a quote in the database (update or insert depending on if ID exists)
	 * 
	 * @param Model_Insurance_Quote
	 *  
	 */
	public function save($quoteModel) {
		if (!($quoteModel instanceof Model_Insurance_Quote)) {
			throw new Exception("Invalid quote object - must be an instance of Model_Insurance_Quote.");
			return false;
		}
		
		// Build the data into an array we can upsert
		$data = array(
			'id'						=> $quoteModel->ID,
			'legacy_id'					=> $quoteModel->legacyID,
			'customer_id'				=> $quoteModel->customerID,
			'legacy_customer_id'		=> $quoteModel->legacyCustomerID,
			'issued_date'				=> $quoteModel->issuedDate,
			'start_date'				=> $quoteModel->startDate,
			'end_date'					=> $quoteModel->endDate,
			'status'					=> strtoupper($quoteModel->status),
			'agent_scheme_number'		=> $quoteModel->agentSchemeNumber,
			'pay_by'					=> $quoteModel->payBy,
			'pay_frequency'				=> $quoteModel->payFrequency			
		);
		
		//Check to see if the ID already exists in the database
		if (!is_null($quoteModel->ID)) {
			$select = $this->select()->where('id = ?', $quoteModel->ID);
			$quoteRow = $this->fetchRow($select);
		
			if (count($quoteRow)>0) {
				// Quote already exists - update existing record
				$where = $this->quoteInto('id = ?', $quoteModel->ID);
				$this->update($data, $where);
			}
		} else {
			// Quote doesn't exist yet - insert a new record
			// Update the quote model with the new inserted ID
			$quoteModel->ID = $this->insert($data);
		}
		return $quoteModel;
	}
}

?>