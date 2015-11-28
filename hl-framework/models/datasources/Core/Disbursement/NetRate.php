<?php
/**
* Model definition for the InsurerRate table
* 
*/
class Datasource_Core_Disbursement_NetRate extends Zend_Db_Table_Multidb {
    protected $_name = 'disbursement';
    protected $_primary = 'disno';
    protected $_multidb = 'db_legacy_homelet';
    /**
     * Getter for the policy amounts covered.
     *
     * Returns the net rate by which $optionName is covered on the current
     * quote / policy. This can be used to identify specific cover net value,
     * without the need of extracting the entire net rates
     * field and splitting up the pipe-delimited fields.
     *
     * @param string $optionName
     * @param int $rateSetID
     * @param int $riskarea
     * @param string $date
     * @param string $band
     * 
     * @return net rate for option
     *
     */
    public function getNetRatebyOption($optionName,$rateSetID,$riskarea,$date=null, $bandValue=null) {
        if (is_null($date)) $date = date("Y-m-d");
        //First retrieve all the policyoptions and amountscovered for the
        //current quote.
        $fields = array('disb'.$optionName);
        $select = $this->select()
            ->from($this->_name, $fields)
            ->where('start_date < ?', $date)
            ->where('end_date > ?', $date)
            ->where('whitelabelID= ?','HL')
            ->where('fromratesetid <= ?', (int) $rateSetID)
            ->where('toratesetid >= ?', (int) $rateSetID);

        $row = $this->fetchRow($select);
        //$name='disb'.$optionName;
        
        $rowArray = $row->toArray();
        
        $processV="";
       // Now use it as a normal array
       foreach ($rowArray as $column => $value) {
            $processV= $value;
          }

     
        $net_rate=explode("|", $processV);
        
        if(is_null($bandValue)){
         
          return $net_rate[$riskarea-1];
        }
        else{
          $band=array('A','B','C','D');
          foreach ($band as $key => $value){
            $returnArr = array();
            for($i=0;$i<=3;$i++){
                $returnArr[$i]=array_shift($net_rate);
               
            }
            if($value==$bandValue){
                
               return  $returnArr[$riskarea-1];
            }
           
         }         
          
          
            
        }
        
     }
 
}   
?>
