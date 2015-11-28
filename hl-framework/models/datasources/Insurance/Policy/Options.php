<?php

class Datasource_Insurance_Policy_Options extends Zend_Db_Table_Multidb {
    protected $_name = 'policyOptions';
    protected $_primary = 'policyOptionID';
    protected $_multidb = 'db_legacy_homelet';
    
    private $_optionType; // The data base is currently an ENUM 'T','L' and 'N/A'
    private $_options;
    
    /**
     * Constructor
     *
     * @param $type type of policy - currently either 'T', 'L' or 'N/A'
     * @return void
     */
    public function __construct($type = '')
    {
        $this->_optionType = $type;
        parent::__construct();
    }
    
    
    /**
     * Fetch options from the database
     *
     * @param none
     * @return array
     */
    public function fetchOptions() {
        $fields = array('policyOption','minimumSumInsured','policyOptionID');
        $select = $this->select();
        $select->from($this->_name, $fields);
        $select->where('optionType = ?', $this->_optionType);
        $this->_options = $this->fetchAll($select);
        
        if (count($this->_options) > 0) {
            return $this->_options;
        } else {
            // Can't find policy options for this type - log a warning
            Application_Core_Logger::log("Policy options not found in database (optionType = {$this->_optionType})", 'warning');
            return false;
        }
    }
    
    /**
     * Fetch options by option name from the database
     * 
     * @param string optionName This is the name of a policy option e.g. 'contentstp'
     * @return array
     */
    public function fetchOptionsByName($optionName) {
        $select = $this->select()
                       ->where('policyOption = ?', $optionName);
        $row = $this->fetchrow($select);
        if (count($row)>0) { return $row->policyOptionID; }
        return false;
    }

        }

?>