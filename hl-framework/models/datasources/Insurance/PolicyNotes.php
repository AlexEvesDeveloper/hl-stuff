<?php

/**
* Model definition for Campaign datasource.
*/
class Datasource_Insurance_PolicyNotes extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'policynotes';
    protected $_primary = 'policynumber';
    /**#@-*/
    
    /**
     * Add note to policy notes
     *
     * @param string $policynumber Policynumber to add note to
     * @param string $newnote Note to add
     *
     * @return bool Status of insert/update
     */
    public function addNote($policynumber, $newnote)
    {
        $select = $this->select();
        $select->from($this->_name, array('notes'));
        $select->where('policynumber = ?', $policynumber);
        $select->limit(1);
        
        $row = $this->fetchRow($select);
        $note = null;
        
        if ($row == null)
        {
            // Insert
            return $this->insert(array('policynumber' => $policynumber, 'notes' => $newnote));
        }
        else
        {
            // Update
            $note = $row->notes;
            $note .= "\n\n" . $newnote;
            
            $where = $this->getAdapter()->quoteInto('policynumber = ?', $policynumber);
            return $this->update(array('notes' => $note), $where);
        }
    }
    
    
	/**
	 * Description given in the IChangeable interface.
	 */
    public function changeQuoteToPolicy($quoteNumber, $policyNumber = null) {
		
		//If policyNumber is empty then assume the QHLI should be replaced with PHLI.
		if(empty($policyNumber)) {
			
			$policyNumber = preg_replace('/^Q/', 'P', $quoteNumber);
		}
		
		$where = $this->quoteInto('policynumber = ?', $quoteNumber);
		$updatedData = array('policynumber' => $policyNumber);
		return $this->update($updatedData, $where);
	}
}

?>