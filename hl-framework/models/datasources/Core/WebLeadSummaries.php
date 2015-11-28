<?php

/**
 * Encapsulates the data source responsible for storing WebLeadSummary objects.
 */
class Datasource_Core_WebLeadSummaries extends Zend_Db_Table_Multidb {

    /**#@+
     * Mandatory attributes
     */
	protected $_multidb = 'db_legacy_webleads';
	protected $_name = 'QUOTESUMMARY';
	protected $_primary = 'QUOTEID';
	/**#@-*/
	
	
	/**
	 * Description given in the IChangeable interface.
	 */
    public function changeQuoteToPolicy($quoteNumber, $policyNumber = null) {
		
		//If policyNumber is empty then assume the QHLI should be replaced with PHLI.
		if(empty($policyNumber)) {
			
			$policyNumber = preg_replace('/^Q/', 'P', $quoteNumber);
		}
		
		$lastUpdated = Zend_Date::now();
		$lastUpdatedTime = $lastUpdated->toString(Zend_Date::ISO_8601);
		
		$where = $this->quoteInto('QUOTENUMBER = ?', $quoteNumber);
		$updatedData = array('QUOTENUMBER' => $policyNumber, 'STATUS' => 3, 'LASTUPDATED' => $lastUpdatedTime);
		return $this->update($updatedData, $where);
	}
	
	
	/**
	 * Inserts a new, empty WebLeadSummary into the datastore, and returns the
	 * ID created.
	 *
	 * @return integer
	 * The id of the new WebLeadSummary created.
	 */
	public function insertSummary() {
		
		//Insert a new, blank WebLeadSummary, and return the ID created.
        $data = array(
            'TITLE' => '',
            'FIRSTNAME' => '',
            'LASTNAME' => ''
        );

        return $this->insert($data);
	}
	

	/**
	 * Updates an existing WebLeadSummary.
	 *
	 * @param Model_Core_WebLeadSummary
	 * The WebLeadSummary object to be updated in the data source.
	 *
	 * @return integer
	 * The number of entries in the data source affected by the update.
	 */
	public function updateSummary(Model_Core_WebLeadSummary $webLeadSummary) {
		
		//Apply safety logic to the date/time properties in the WebLeadSummary.
		if($webLeadSummary->startTime == null) {
			
			$startTime = '0000-00-00';
		}
		else {
			
			$startTime = $webLeadSummary->startTime->toString(Zend_Date::ISO_8601);
		}
		
		if($webLeadSummary->lastUpdatedTime == null) {
			
			$lastUpdatedTime = '0000-00-00';
		}
		else {
			
			$lastUpdatedTime = $webLeadSummary->lastUpdatedTime->toString(Zend_Date::ISO_8601);
		}
		
		if($webLeadSummary->completedTime == null) {
			
			$completedTime = '0000-00-00';
		}
		else {
			
			$completedTime = $webLeadSummary->completedTime->toString(Zend_Date::ISO_8601);
		}
		
		
		//Apply type conversion logic to the follow-up property.
		if(($webLeadSummary->followUp == null) || ($webLeadSummary->followUp == false)) {
			
			$webLeadSummary->followUp = 0;
		}
		else {

			$webLeadSummary->followUp = 1;
		}
		
		
		//And update.
		$data = array(
            'TITLE' => $webLeadSummary->title,
            'FIRSTNAME' => $webLeadSummary->firstName,
            'LASTNAME' => $webLeadSummary->lastName,
            'CONTACTNUMBER' => $webLeadSummary->contactNumber,
            'EMAIL' => $webLeadSummary->emailAddress,
            'STARTTIME' => $startTime,
            'LASTUPDATED' => $lastUpdatedTime,
            'STATUS' => $webLeadSummary->status,
            'PRODUCT' => $webLeadSummary->product,
            'QUOTENUMBER' => $webLeadSummary->quoteNumber,
            'PROMOTIONCODE' => $webLeadSummary->promotionCode,
            'FOLLOWUP' => (($webLeadSummary->followUp) ? 1 : 0),
            'REFERER' => $webLeadSummary->referer,
            'COMPLETED' => $completedTime,
            'ISMAILERSENT' => (($webLeadSummary->isMailerSent) ? 1 : 0),
        );

        $where = $this->quoteInto('QUOTEID = ?', $webLeadSummary->webLeadSummaryId);
        return $this->update($data, $where);
	}
	
	
	/**
	 * Identifies if a WebLeadSummary exists in the datasource.
	 *
	 * @param integer $webLeadSummaryId
	 * Specifies the WebLeadSummary to search for.
	 *
	 * @return boolean
	 * Returns true if the WebLeadSummary exists in the datasource, false otherwise.
	 */
	public function getSummaryExists($webLeadSummaryId) {
		
		try {
			
			$this->getSummary($webLeadSummaryId);
			$returnVal = true;
		}
		catch(Zend_Exception $e) {
			
			$returnVal = false;
		}
		
		return $returnVal;
	}
	
	
	/**
	 * Retrieves a WebLead ID corresponding to the quote/policy number passed in.
	 *
	 * @param string $quoteNumber
	 * Specifies the WebLead identifier to retrieve.
	 *
	 * @return mixed
	 * The WebLead identifier, or null if one cannot be found.
	 */
	public function getSummaryId($quoteNumber) {
		
		$select = $this->select();
        $select->where('QUOTENUMBER = ?', $quoteNumber);
        $row = $this->fetchRow($select);
        
		if(empty($row)) {
			
			$returnVal = null;
		}
		else {
			
			$returnVal = $row->QUOTEID;
		}
		
		return $returnVal;
	}
	
	
	/**
	 * Retrieves a Model_Core_WebLeadSummary from the datasource, specified by
	 * the identifier passed in.
	 *
	 * @param integer $webLeadSummaryId
	 * Specifies the WebLeadSummary to retrieve.
	 *
	 * @return Model_Core_WebLeadSummary
	 * The WebLeadSummary corresponding to the identifier passed in.
	 *
	 * @throws Zend_Exception
	 * Throws a Zend_Exception if the WebLeadSummary cannot be found.
	 */
	public function getSummary($webLeadSummaryId) {
		
		$select = $this->select();
        $select->where('QUOTEID = ?', $webLeadSummaryId);
        $row = $this->fetchRow($select);
		if(empty($row)) {
			
			throw new Zend_Exception('Invalid weblead identifier.');
		}

        return $this->hydrateModel($row);
	}
	
	
	/**
	 * Removes a WebLeadSummary from the datasource.
	 *
	 * @param integer $webLeadSummaryId
	 * Identifies the WebLeadSummary to remove from the datasource.
	 *
	 * @return void
	 */
	public function removeSummary($webLeadSummaryId) {
		
        $where = $this->quoteInto('QUOTEID = ?', $webLeadSummaryId);
        $this->delete($where);
	}

    /**
     * Returns all active (IN_PROGRESS and ABANDONED) summaries between two offsets given in an array (minLastUpdated
     * and maxLastUpdated) that are a particular product set (products) with an optional hour offset (hourOffset).
     *
     * @param array $criteria
     * @return Zend_Db_Table_Rowset
     */
    public function searchActiveSummaries($criteria)
    {
        $now = Zend_Date::now();
        $nowTime = $now->toString(Zend_Date::ISO_8601);

        // Handle optional time offset
        $offsetType = 'SUB';
        $offsetHour = 0;
        if (isset($criteria['hourOffset'])) {
            $offset = $criteria['hourOffset'];
            if ($offset < 0) {
                $offsetHour = abs($offset);
            }
            else {
                $offsetType = 'ADD';
            }
        }

        $select = $this->select();
        $select
            ->where(
                'LASTUPDATED < ?',
                new Zend_Db_Expr(
                    sprintf(
                        'DATE_%s(\'%s\', INTERVAL %d SECOND)',
                        $offsetType,
                        $nowTime,
                        $criteria['minLastUpdated'] + (3600 * $offsetHour)
                    )
                )
            )
            ->where(
                'LASTUPDATED > ?',
                new Zend_Db_Expr(
                    sprintf(
                        'DATE_%s(\'%s\', INTERVAL %d SECOND)',
                        $offsetType,
                        $nowTime,
                        $criteria['maxLastUpdated'] + (3600 * $offsetHour)
                    )
                )
            )
            ->where(
                'STATUS IN (?)',
                array(
                    Model_Core_WebLeadStatus::IN_PROGRESS,
                    Model_Core_WebLeadStatus::ABANDONED,
                )
            )
            ->where(
                'PRODUCT IN (?)',
                $criteria['products']
            )
            ->where(
                'ISMAILERSENT = ?',
                $criteria['isMailerSent']
            )
        ;

        $activeSummaryResults = $this->fetchAll($select);

        $returnVal = array();
        foreach($activeSummaryResults as $activeSummaryRow) {
            $returnVal[] = $this->hydrateModel($activeSummaryRow);
        }

        return $returnVal;
    }

    /**
     * Takes a data row from the database and returns a hydrated model representation.
     *
     * @param Zend_Db_Table_Row $dataRow
     * @return Model_Core_WebLeadSummary
     */
    private function hydrateModel(Zend_Db_Table_Row $dataRow) {
        //Populate the data into a Model_Core_WebLeadSummary object

        $webLeadSummary = new Model_Core_WebLeadSummary();
        $webLeadSummary->webLeadSummaryId = $dataRow->QUOTEID;
        $webLeadSummary->title = $dataRow->TITLE;
        $webLeadSummary->firstName = $dataRow->FIRSTNAME;
        $webLeadSummary->lastName = $dataRow->LASTNAME;
        $webLeadSummary->contactNumber = $dataRow->CONTACTNUMBER;
        $webLeadSummary->emailAddress = $dataRow->EMAIL;
        $webLeadSummary->startTime = new Zend_Date($dataRow->STARTTIME, Zend_Date::ISO_8601);
        $webLeadSummary->lastUpdatedTime = new Zend_Date($dataRow->LASTUPDATED, Zend_Date::ISO_8601);
        $webLeadSummary->status = $dataRow->STATUS;
        $webLeadSummary->product = $dataRow->PRODUCT;
        $webLeadSummary->quoteNumber = $dataRow->QUOTENUMBER;
        $webLeadSummary->promotionCode = $dataRow->PROMOTIONCODE;

        if ($dataRow->FOLLOWUP == 1) {

            $webLeadSummary->followUp = true;
        }
        else {

            $webLeadSummary->followUp = false;
        }

        $webLeadSummary->referer = $dataRow->REFERER;
        $webLeadSummary->completedTime = new Zend_Date($dataRow->COMPLETED, Zend_Date::ISO_8601);
        $webLeadSummary->isMailerSent = (1 == $dataRow->ISMAILERSENT) ? true : false;

        return $webLeadSummary;
    }
}