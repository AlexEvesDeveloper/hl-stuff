<?php
/**
* Data Model to represent the HLtenantsprotectionplusriskareas
* 
* @author John Burrin
*
* 
*/



class Datasource_Insurance_RiskAreas_TenantsProtectionPlus extends Zend_Db_Table_Multidb {
    protected $_name = 'HLtenantsprotectionplusriskareas';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_insurance_com';
    
    /**
    * Returns the Risk area based on a 
    * @param string $outCode, The postcode outcode
    * @return int Riskarea
    * @author John Burrin
    * 
    *   HLtenantsprotectionplusriskareas.startdate <= '2011-03-20'
    *   AND (HLtenantsprotectionplusriskareas.enddate >= '2011-03-20' OR HLtenantsprotectionplusriskareas.enddate = '0000-00-00')
    */
    public function getCurrentRate($outCode){
        $returnVal = false;
        $select = $this->select()
            ->from($this->_name,array('riskarea'))
            ->where('postcode = ?', $outCode)
            ->where('startdate <= ?', date("Y-m-d"))
            ->where('enddate >= ? OR enddate = "0000-00-00"', date("Y-m-d"));
        $row = $this->fetchRow($select);
                
        if(!empty($row))
        {
            $returnVal = $row->riskarea;
        }
        return $returnVal;
    }
}
?>