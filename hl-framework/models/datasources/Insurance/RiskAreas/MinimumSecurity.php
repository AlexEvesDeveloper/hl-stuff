<?php
/**
* Data Model to represent the HLminsecurityriskareas
* 
* @author John Burrin
*
* 
*/



class Datasource_Insurance_RiskAreas_MinimumSecurity extends Zend_Db_Table_Multidb {
    protected $_name = 'HLminsecurityriskareas';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_insurance_com';

    /**
    * TODO: Document this
    * @param string $outCode, The postcode outcode
    * @return int Riskarea
    * @author John Burrin
    * 
    *   HLminsecurityriskareas.startdate <= '2011-03-20'
    *   AND (HLminsecurityriskareas.enddate >= '2011-03-20' OR HLminsecurityriskareas.enddate = '0000-00-00')
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