<?php

/**
 * Encapsulates the data source responsible for storing WebLeadBlob objects.
 * WebLeadBlobs are the name given to 'blobs' of form data stored without processing into
 * the data store.
 */
class Datasource_Core_WebLeadBlobs extends Zend_Db_Table_Multidb {

    /**#@+
     * Mandatory attributes
     */
	protected $_multidb = 'db_legacy_webleads';
	protected $_name = 'QUOTEFORMS';
	protected $_primary = array('QUOTEID', 'STEPNUMBER');
	/**#@-*/
	
	
	/**
	 * Creates a new WebLeadBlob in the datasource.
	 *
	 * @param integer $webLeadSummaryId
	 * The WebLeadSummary identifier, which the blob is linked to, and which in
	 * part identifies the blob.
	 *
	 * @param integer $stepNumber
	 * The step number the blob will encapsulate. Must correspond to one of the
	 * consts exposed by Model_Core_WebLeadStep.
	 *
	 * @return void
	 */
	public function insertBlob($webLeadSummaryId, $stepNumber) {
		
        $data = array(
            'QUOTEID' => $webLeadSummaryId,
            'STEPNUMBER' => $stepNumber
        );

        $this->insert($data);
	}
	
	
	/**
	 * Updates an existing WebLeadBlob.
	 *
	 * @param Model_Core_WebLeadBlob
	 * The WebLeadBlob object to be updated in the data source.
	 *
	 * @return integer
	 * The number of entries in the data source affected by the update.
	 */
	public function updateBlob($webLeadBlob) {
		
		$data = array(
            'QUOTEID' => $webLeadBlob->webLeadSummaryId,
            'STEPNUMBER' => $webLeadBlob->stepNumber,
            'FORMDATA' => $webLeadBlob->blob,
            'FORMCHECKSUM' => $webLeadBlob->blobChecksum
        );

		//Employ an ugly workaround to accommodate quoteIntro's inability to
		//accept more than one parameter.
        $where = $this->quoteInto('QUOTEID = ? AND STEPNUMBER = ?', $webLeadBlob->webLeadSummaryId, $webLeadBlob->stepNumber);
		
        return $this->update($data, $where);
	}
	
	
		
	/**
	 * Retrieves a Model_Core_WebLeadBlob from the datasource, specified by
	 * the identifiers passed in.
	 *
	 * @param integer $webLeadSummaryId
	 * The WebLeadSummary identifier, which the blob is linked to.
	 *
	 * @param integer $stepNumber
	 * The step number the blob encapsulates. Must correspond to one of the
	 * consts exposed by Model_Core_WebLeadStep.
	 *
	 * @throws Zend_Exception
	 * Throws a Zend_Exception if the WebLeadBlob cannot be found.
	 */
	public function getBlob($webLeadSummaryId, $stepNumber) {
		
		$select = $this->select();
        $select->where('QUOTEID = ?', $webLeadSummaryId);
		$select->where('STEPNUMBER = ?', $stepNumber);
        $row = $this->fetchRow($select);
		if(empty($row)) {
			
			throw new Zend_Exception('Unable to locate WebLeadBlob.');
		}

		//Populate the data into a Model_Core_WebLeadSummary object.
		$webLeadBlob = new Model_Core_WebLeadBlob();
		$webLeadBlob->webLeadSummaryId = $row->QUOTEID;
		$webLeadBlob->stepNumber = $row->STEPNUMBER;
		$webLeadBlob->blob = $row->FORMDATA;
		$webLeadBlob->blobChecksum = $row->FORMCHECKSUM;
		return $webLeadBlob;
	}

    /**
     * For any particular web lead summary, find and return the last step number that a blob exists.
     *
     * @param int $webLeadSummaryId
     * @return int|null
     */
    public function getBlobMaxStep($webLeadSummaryId)
    {
        $select = $this->select('STEPNUMBER');
        $select
            ->where('QUOTEID = ?', $webLeadSummaryId)
            ->order('STEPNUMBER DESC')
            ->limit(1)
        ;
        $row = $this->fetchRow($select);

        if (empty($row)) {
            return null;
        }

        return $row->STEPNUMBER;
    }
	
	/**
	 * Removes a WebLeadBlob from the datasource.
	 *
	 * @param integer $webLeadSummaryId
	 * The WebLeadSummary identifier, which the blob is linked to, and which in
	 * part identifies the blob.
	 *
	 * @param integer $stepNumber
	 * The step number the blob encapsulates. Must correspond to one of the
	 * consts exposed by Model_Core_WebLeadStep.
	 *
	 * @return void
	 */
	public function removeBlob($webLeadSummaryId, $stepNumber) {
		
        $this->delete(
			array(
				'QUOTEID = ?' => $webLeadSummaryId,
				'STEPNUMBER = ?' => $stepNumber)
		);	
	}
}

?>