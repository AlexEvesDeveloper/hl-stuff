<?php

/**
* Model definition for quote/policy document queue.
*/
class Datasource_Insurance_DocumentQueue extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'doc_request_queue';
    protected $_primary = 'request_id';
    
	/**
     * Retrieves documents from the datastore.
     *
     * Composite documents, comprised of multiple document templates and therefore
     * identified by multiple document identifiers, will be included in the return
     * list if they contain the $documentTypeIdentifier passed in to this method.
     * 
     * @param string $policyNumber
     * Specifies the quote or policynumber to search against.
     *
     * @param integer $documentTypeIdentifier
     * Identifies the documents to retrieve. Use the consts exposed in the DocumentTypes
     * classes to specify arguments here.
     *
     * @return mixed
     * Returns an array of one or more Model_Insurance_Document objects. If no matching
     * documents are found, then will return null.
     *
     * @todo Clean up the technique by which document types are specified, so that this method can
     * service all product types, rather than just TCI+.
     */
    //public function getDocuments($policyNumber, $documentTypeIdentifier)
    // {
    //    Note: This method may no longer function as expected - Not required from original DMS solution. What requires this.
    //    //Retrieve the customer record.
    //    $select = $this->select()
    //        ->from($this->_name)
    //        ->where('policynumber = ?', $policyNumber)
    //        ->where('letteridlist LIKE ?', "$documentTypeIdentifier%");
    //    $rowset = $this->fetchAll($select);
    //    
    //    if(count($rowset) == 0) {
    //    	
    //        // No warning given as this is a common/normal scenario
    //        $returnVal = null;
    //    }
    //    else {
    //        
    //        $returnArray = array();
    //        foreach($rowset as $row) {
    //            
    //            $document = new Model_Insurance_Document();
    //            $document->documentNumber = $row->queuenumber;
    //            $document->policyNumber = $row->policynumber;
    //            $document->csuId = $row->csuid;                
    //            $document->timeQueued = new Zend_Date($row->timequeud, Zend_Date::ISO_8601);
    //            $document->documentIdentifierList = $row->letteridlist;
    //            $document->target = $row->target;
    //            $document->emailTo = $row->emailto;
    //            $document->postage = $row->postage;
    //            $document->fileName = $row->filename;
    //            
    //            $returnArray[] = $document;
    //        }    
    //        $returnVal = $returnArray;
    //    }
    //    
    //    return $returnVal;
    //}
        
	/**
	 * Description given in the IChangeable interface.
	 */
    public function changeQuoteToPolicy($quoteNumber, $policyNumber = null)
    {
		//If policyNumber is empty then assume the QHLI should be replaced with PHLI.
        if(empty($policyNumber))
        {
			$policyNumber = preg_replace('/^Q/', 'P', $quoteNumber);
		}
		
        $where = $this->getAdapter()->quoteInto('policy_number = ?', $quoteNumber);
        $updatedData = array('policy_number' => $policyNumber);
		return $this->update($updatedData, $where);		
	}
}
