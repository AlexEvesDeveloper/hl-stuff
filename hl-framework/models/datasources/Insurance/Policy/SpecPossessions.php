<?php
class Datasource_Insurance_Policy_SpecPossessions extends Zend_Db_Table_Multidb
{
    public      $maxPossessions = 10;

    protected   $_name = 'specpossessions';
    protected   $_primary = 'ItemID';
    protected   $_multidb = 'db_legacy_homelet';
    protected   $_homeletUK;
    private     $_policyID;
    

    public function __construct($p)
    {
        // Make this model use the homelet_insurance database connection
        $this->_policyID = $p;
                     
        $this->_homeletUK     = Zend_Registry::get($this->_multidb);    
        parent::__construct();

    }
    
	
    /**
	 * Description given in the IChangeable interface.
	 */
	public function changeQuoteToPolicy($quoteNumber, $policyNumber = null) {
		
		//If policyNumber is empty then assume the QHLI should be replaced with PHLI.
		if(empty($policyNumber)) {
			
			$policyNumber = preg_replace('/^Q/', 'P', $quoteNumber);
		}
		
		$where = $this->quoteInto('PolicyID = ?', $quoteNumber);
		$updatedData = array('PolicyID' => $policyNumber);
		return $this->update($updatedData, $where);		
	}
    

    /**
     * Get the number of specified possessions that have been added to a policy
     *
     * @return int
     */
    public function countPossessions() {

        try {
            $select = $this->select()
                ->from($this->_name, 'COUNT(*) AS num')
                ->where('PolicyID = ?', $this->_policyID);
            $count = $this->fetchRow($select);
        }
        catch(Exception $e) {
            //Catch occasions when the _policyID is empty.
            $count['num'] = 0;
        }

        return $count['num'];
    }
    
    /**
     * Get the total value of possessions
     *
     * @return double
     */
    public function getTotalValue() {
        try {
            $select = $this->select()
                ->from($this->_name, 'SUM(DeclaredValue) AS value')
                ->where('PolicyID = ?', $this->_policyID);
            $count = $this->fetchRow($select);
        }
        catch(Exception $e) {
            //Catch occasions when the _policyID is empty.
            $count['value'] = 0;
        }

        return $count['value'];
    }
    
    /**
     * Get the details of each specified possession that's associated with a policy
     *
     * @return array
     */
    public function listPossessions() {
        $possessions = array();

        $select = $this->select()
            ->from(array('s' => $this->_name))
            ->setIntegrityCheck(false)
            ->join(array('c' => 'specpossession_categories'),'s.SpecpossessionCategoryId = c.id',array('c.Label'))
            ->where('PolicyID = ?', $this->_policyID)
            ->order('ItemID ASC');
        $possessionsAll = $this->fetchAll($select);

        foreach ($possessionsAll as $row) {
            $possessions[] = array(
                'id'            => $row['ItemID'],
                'category'      => $row['Label'],
                'description'   => $row['Description'],
                'value'         => $row['DeclaredValue']
            );
        }

        return $possessions;
    }

    /**
     * Add a specified possession to a policy, if the number in it is less than the maximum
     *
     * @param array $data details of the item being added
     *
     * @return bool success status
     */
    public function addNew($data) {
        if ($this->countPossessions() < $this->maxPossessions) {
            $possession = array(
                'PolicyID'      => $this->_policyID,
                'SpecpossessionCategoryId' => $data['possession_categoryId'],
                'Description'   => $data['possession_description'],
                'DeclaredValue' => $data['possession_value'],
                'Confirmed'     => ($data['possession_value'] < 25000) ? 1 : 0
            );
			
			
            if ($this->insert($possession)) {
                return true;
            } else {
                // Failed insertion
                Application_Core_Logger::log("Can't insert specified possession in table {$this->_name}", 'error');
                return false;
            }
        }
        return false;
    }

    /**
     * Remove the Nth (in the order returned by listPossessions()) specified possession from a policy
     *
     * @param int nth specified possession to remove
     *
     * @return void
     */
    public function remove($nth) {
        // Find ID to remove based on Nth ordering - apart from LIMIT clause, SELECT must match listPossessions() method
        $select = $this->select()
            ->from($this->_name)
            ->where('PolicyID = ?', $this->_policyID)
            ->order('ItemID ASC')
            ->limit(1, $nth);
        $result = $this->fetchAll($select); // Uses fetchAll instead of fetchRow as limit() is broken with fetchRow (returns first result irrespective)

        if (count($result) > 0) {
            foreach($result as $row) {
                $id = $row['ItemID'];
            }
            $where = array(
                $this->quoteInto('PolicyID = ?', $this->_policyID),
                $this->quoteInto('ItemID = ?', $id)
            );
            $this->delete($where);
        } else {
            // Can't find Nth specified possession - log a warning
            Application_Core_Logger::log("Can't find Nth specified possession to delete (N = {$nth})", 'warning');
            return false;
        }
    }
    
    /**
     * Get the details of each specified Categories that's associated with a policy
     *
     * @return array
     */
    public function listCategories() {
        $scat = array('' => '--- please select ---');
        
        $select = $this->_homeletUK->select()
            ->from("specpossession_categories")
            ->where("AdminOnly = 'no' ");
           
        $sAll = $this->_homeletUK->fetchAll($select);

        foreach ($sAll as $row) {
            $scat[$row['id']] = $row['Label'];
        }

        return $scat;
    }
    
    public function getSumInsuredByRefno(){
        // TODO
    }
}
?>
