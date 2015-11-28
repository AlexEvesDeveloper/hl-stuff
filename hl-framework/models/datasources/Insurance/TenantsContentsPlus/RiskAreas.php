<?php
/**
* Model definition for the RiskAreas
* 
*/
class Datasource_Insurance_TenantsContentsPlus_RiskAreas extends Zend_Db_Table_Multidb {
    protected $_name = 'HLtenantscontentsriskareas';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_insurance_com';
    
    /**
    * Finds a specific riskarea by id
    *
    * @param int id
    * @return array
    * 
    */    
    function listByID($id) {
        $select = $this->select();
        $select = $this->select()->where('id = ?', $id);
        $rows = $this->fetchAll($select);
        return $rows->toArray();
    }
    
    /**
    * Finds a specific riskarea by postcode outcode
    *
    * @param int id
    * @return array
    */    
    function findByOutCode($postcode) {
        // TODO: Replace this with a PROPER incode/outcode splitter!!
        $outcode = str_replace(" ","",$postcode); // Remove space
        $outcode = substr($outcode,0,strlen($outcode)-3); // This should leave us with the postcode less the last 3 chars, the outcode
        
        $select = $this->select()
            ->from($this->_name,array('riskarea'))
            ->where('postcode = ?', $outcode)
            ->where('startdate < ?', date("Y-m-d"))
            ->where('enddate > ?', date("Y-m-d"));
        $row = $this->fetchRow($select);
                
        if(!empty($row))
        {
            $returnVal = $row->riskarea;
        }
        else {
            // Can't find by outcode - log a warning
            Application_Core_Logger::log("Can't find by outcode in table {$this->_name} (postcode = {$outcode})", 'warning');
            $returnVal = null;
        }
        return $returnVal;
    }
}

?>