<?php
class Datasource_Insurance_Policy_Cycles extends Zend_Db_Table_Multidb {
    public $maxBicycles = 10;

    protected $_name = 'pedalcycles';
    protected $_primary = 'refno';
    protected $_multidb = 'db_legacy_homelet';

    private $_refNo;
    private $_policyNumber;

    public function __construct($r,$p)
    {
        // Make this model use the homelet_insurance database connection
        $this->_refNo = $r;
        $this->_policyNumber = $p;
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
		
		$where = $this->quoteInto('policynumber = ?', $quoteNumber);
		$updatedData = array('policynumber' => $policyNumber);
		return $this->update($updatedData, $where);		
	}
    

    /**
     * Get the number of bicycles that have been added to a policy
     *
     * @return int
     */
    public function countBikes() {
        
        try {
            
            $select = $this->select()
                ->from($this->_name, 'COUNT(*) AS num')
                ->where('policynumber = ?', $this->_policyNumber)
                ->where('refno = ?', $this->_refNo);
            $count = $this->fetchRow($select);
        }
        catch(Exception $e) {
            
            //Catch occasions where the policyNumber and/or _refNo is empty.
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
                ->from($this->_name, 'SUM(value) AS totalValue')
                ->where('policynumber = ?', $this->_policyNumber)
                ->where('refno = ?', $this->_refNo);
            $count = $this->fetchRow($select);
        }
        catch(Exception $e) {
            //Catch occasions when the _policyID is empty.
            $count['totalValue'] = 0;
        }

        return $count['totalValue'];
    }
    
    /**
     * Get the details of each bicycle that's associated with a policy
     *
     * @return array
     */
    public function listBikes() {
        $bikes = array();

        $select = $this->select()
            ->from($this->_name)
            ->where('policynumber = ?', $this->_policyNumber)
            ->where('refno = ?', $this->_refNo)
            ->order('value ASC');
        $bikesAll = $this->fetchAll($select);

        foreach ($bikesAll as $row) {
            $bikes[] = array(
				'id'		=> $row['id'],
                'make'      => $row['make'],
                'model'     => $row['model'],
                'serial'    => $row['serialno'],
                'value'     => $row['value']
            );
        }

        return $bikes;
    }

    /**
     * Add a bicycle to a policy, if the number in it is less than the maximum
     *
     * @param array $data details of the item being added
     *
     * @return bool success status
     */
    public function addNew($data) {
        if ($this->countBikes() < $this->maxBicycles) {
            $bike = array(
                'refno'         => $this->_refNo,
                'policynumber'  => $this->_policyNumber,
                'make'          => $data['bicycle_make'],
                'model'         => $data['bicycle_model'],
                'serialno'      => $data['bicycle_serial'],
                'value'         => $data['bicycle_value']
            );
            if ($this->insert($bike)) {
                return true;
            } else {
                // Failed insertion
                Application_Core_Logger::log("Can't insert bicycle in table {$this->_name}", 'error');
                return false;
            }
        }
        return false;
    }

    /**
     * Remove a specific bike from a policy
     *
     * @param int id Id of bicycle to remove
     *
     * @return void
     */
    public function remove($id) {
        /*
            TODO: nee to write a delete ALL by refno for the use case the usr remove pedal syscle cover
        */

        $where = array(
			$this->quoteInto('policynumber = ?', $this->_policyNumber),
            $this->quoteInto('id = ?', $id)
        );
        $this->delete($where);
    }

    public function getSumInsuredByRefno(){
        #SELECT SUM(value) FROM pedalcycles where refno = "73476229.30244";
         $select = $this->select()
            ->from($this->_name, array('pedalCyclesSI' => 'SUM(`value`)'))
            ->group('refno')
            ->where('refno = ?', $this->_refNo);
        $row = $this->fetchRow($select);
        $returnArray = array();
        $returnArray['pedalCyclesSI'] = 0;
        if($row) $returnArray = $row->toArray();

        return $returnArray['pedalCyclesSI'];

    }
}
?>