<?php
/**
* Data Model to represent the HLbuildingsriskareas
*
* @author John Burrin
*
* 
*/



class Datasource_Insurance_RiskAreas_Buildings extends Zend_Db_Table_Multidb {
    protected $_name = 'HLbuildingsriskareas';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_insurance_com';

    /**
    * TODO: Document this
    * @param string $outCode, The postcode outcode
    * @return int Riskarea
    * @author John Burrin
    * 
    *   HLbuildingsriskareas.startdate <= '2011-03-20'
    *   AND (HLbuildingsriskareas.enddate >= '2011-03-20' OR HLbuildingsriskareas.enddate = '0000-00-00')
    */
    public function getCurrentRate($postCode){
        list($outCode,$inCode) = explode(" ",$postCode);
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
