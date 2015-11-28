<?php

/**
* Model definition for quote/policy document histories.
*/
class Datasource_Insurance_DocumentHistory extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'doc_request_history';
    protected $_primary = 'request_id';

    /**
     * Get a document details for a document id
     *
     * @param string $documentid Unique document id
     * @param string $policyNumber Policy number
     * @return \Model_Insurance_Document
     */
    public function getDocument($documentid, $policyNumber)
    {
        $document = null;
        $select = $this->select()
                        ->from(array('d' => $this->_name))
                        ->setIntegrityCheck(false)
                        ->join(array('t' => 'doc_template_names'), 'd.template_id = t.template_id')
                        ->where('d.request_hash = ?', $documentid)
                        ->where('d.policy_number = ?', $policyNumber)
                        ->where('d.suppression = 0')
                        ->limit(1);

        $row = $this->fetchRow($select);

        if ($row) {
            $document = new Model_Insurance_Document();
            $document->request_id = $row->request_id;
            $document->policy_number = $row->policy_number;
            $document->template_name = $row->template_name;
            $document->request_hash = $row->request_hash;
            $document->send_datetime = new Zend_Date($row->send_datetime, Zend_Date::ISO_8601);
            $document->csuid = $row->csuid;
        }

        return $document;
    }

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
     * @param $document_systemname
     * @internal param int $documentTypeIdentifier Identifies the documents to retrieve. Use the consts exposed in the
     *      DocumentTypes* Identifies the documents to retrieve. Use the consts exposed in the DocumentTypes
     *      classes to specify arguments here.
     *
     * @return mixed
     * Returns an array of one or more Model_Insurance_Document objects. If no matching
     * documents are found, then will return null.
     *
     * @todo Clean up the technique by which document types are specified, so that this method can
     * service all product types, rather than just TCI+.
     */
    public function getDocuments($policyNumber, $document_systemname)
    {
        $returnVal = null;
        
        //Retrieve the customer record.
        $select = $this->select()
                       ->from(array('d' => $this->_name))
                       ->setIntegrityCheck(false)
                       ->join(array('t' => 'doc_template_names'), 'd.template_id = t.template_id')
                       ->where('d.policy_number = ?', $policyNumber)
                       ->where('t.template_name = ?', $document_systemname)
                       ->where('d.suppression = 0');
	    
        $rowset = $this->fetchAll($select);
        
        if(count($rowset) > 0)
        {
            $returnVal = array();
            
            foreach($rowset as $row)
            {
                $document = new Model_Insurance_Document();
                $document->request_id = $row->request_id;
                $document->policy_number = $row->policy_number;
                $document->template_name = $row->template_name;
                $document->request_hash = $row->request_hash;
                $document->send_datetime = new Zend_Date($row->send_datetime, Zend_Date::ISO_8601);
                $document->csuid = $row->csuid;
                $returnVal[] = $document;
            }    
        }
        
        return $returnVal;
    }
    
    /**
	 * Removes a document from the data storage.
	 *
	 * @param integer $documentNumber
	 * Identifies the document to be removed from the data storage.
	 *
	 * @return void
	 */
    //public function removeDocument($documentNumber)
    //{
    //    Note: This method may no longer function as expected - removing documents is no longer possible from DMS.
    //    $where = $this->quoteInto('queuenumber = ?', $documentNumber);
    //    $this->delete($where);
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
	
	public function getDocumentsAll($policyNumber, $addresseeRestrictions = array())
    {
        $results = array();

        // Retrieve delay parameter
		$params = Zend_Registry::get('params');		
		$delay = $params->connect->document->displayDelayTime;
		
        // Retrieve all documents in the history table
        $select = $this->select()
                       ->setIntegrityCheck(false)
                       ->from(array('d' => $this->_name), '*')
                       ->columns(array('c.name as typename', 't.name as targetname'))
                       ->join(array('n' => 'doc_template_names'), 'd.template_id = n.template_id')
                       ->join(array('t' => 'doc_template_targets'), 'n.target_id = t.target_id')
                       ->join(array('c' => 'doc_template_types'), 'c.type_id = n.type_id')
                       ->join(array('m' => 'doc_request_methods'), 'm.method_id = d.delivery_method_id')
                       ->where('d.policy_number = ?', $policyNumber)

                       // If the last dispatch time is greater than the generation time then the dispatch should have taken place in which
                       // case we can display the document.
                       ->where(new Zend_Db_Expr("ADDTIME(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'), MAKETIME(0,(FLOOR(MINUTE(CURTIME()) / $delay) * $delay),0)) > d.send_datetime"))

                       ->where('d.suppression = 0')
                       ->order('d.send_datetime DESC');

        // Add target restrictions
        if (count($addresseeRestrictions) > 0) {
            $select->where('t.name IN (?)', $addresseeRestrictions);
        }

        $rowSet = $this->fetchAll($select);

        if (count($rowSet) > 0) {
            foreach($rowSet as $row) {
                switch ($row['targetname']) {
                    case 'holder':
                        $addressee = 'Policy holder';
                        break;

                    case 'agent':
                        $addressee = 'Agent';
                        break;
                }

				$document = new Model_Insurance_Document();
                $document->request_id = $row['request_id'];
                $document->policy_number = $row['policy_number'];
                $document->template_name = $row['template_name'];
                $document->request_hash = $row['request_hash'];
				$document->addresse = $addressee;
                $document->send_datetime = new Zend_Date($row['send_datetime'], Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY .
                        ' ' . Zend_Date::HOUR . ':' . Zend_Date::MINUTE . ':' . Zend_Date::SECOND);
                $document->csuid = $row['csuid'];
                $document->catType = $row['typename'];
                $document->customerDescription = $row['customers_description'];
                $document->send_method = ucfirst(strtolower($row['requestname']));

                $results[] = $document;
            }    
        }
        
        return $results;
    }
}
