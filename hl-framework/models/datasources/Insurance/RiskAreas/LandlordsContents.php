<?php
/**
* Data Model to represent the HLlandlordscontentsriskareas
*
* @author John Burrin
*
* 
*/



class Datasource_Insurance_RiskAreas_LandlordsContents extends Zend_Db_Table_Multidb {
    protected $_name = 'HLlandlordscontentsriskareas';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_insurance_com';

    /**
    * TODO: Document this
    * @param string $outCode, The postcode outcode
    * @return int Riskarea
    * @author John Burrin
    * 
    *   HLlandlordscontentsriskareas.startdate <= '2011-03-20'
    *   AND (HLlandlordscontentsriskareas.enddate >= '2011-03-20' OR HLlandlordscontentsriskareas.enddate = '0000-00-00')
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