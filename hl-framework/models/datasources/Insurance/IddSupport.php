<?php

/**
 * Model definition for the table IddSupport
 * This table is for policy idd support information
 * 
 */
class Datasource_Insurance_IddSupport extends Zend_Db_Table_Multidb {
	protected $_name = 'iddsupport';
	protected $_primary = 'id';
	protected $_multidb = 'db_legacy_homelet';
	
	
	/**
	 * Find a iddsupport by it's ID
	 * 
	 * @param int id
	 * @return boolen 
	 *
	 */
	public function isIddSupport($policynumber) {
		
		$select = $this->select();
		$select->where('policynumber = ?', $policynumber);
		
		$quoteRow = $this->fetchRow($select);
		if (count($quoteRow)>0) {
                    return true;
                }

                return false;
            }
	/**
	 * Insert a new row into the iddsupport table
	 *
	 * @param $data The data to be inserted
	 *
	 * 
	 */
	public function setIddSupport($insertArray) {
		// Remove existing policy term item history record (we don't have a replace into command)
	        $insertArray['isHLIDD']=1;
                if($insertArray['agentschemeno']!=1403796 &&
                   $insertArray['callerid']==1 &&
                   preg_match('/^AR|NAR|AR_AP|NAR_AP|Direct|EPB|DIR_AP$/i', $insertArray['FSA_status']) && 
                   $insertArray['origsaleid']!=9
                ){
                    $insertArray['isHLIDD']=0; 
                }
                
                $dateObject = new Zend_Date();
                $insertArray['dateon'] = $dateObject->get(Zend_Date::TIMESTAMP);
	        $id=$this->insert ( $insertArray );

                return $id;
	
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
