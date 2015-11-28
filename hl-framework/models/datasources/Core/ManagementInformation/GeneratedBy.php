<?php
/**
 * MI Table to store whom entered the quote
 */
class Datasource_Core_ManagementInformation_GeneratedBy extends Zend_Db_Table_Multidb {
    protected $_name = 'QuoteGenerateBy';
    protected $_primary = 'policynumber';
    protected $_multidb = 'db_legacy_homelet';
    
    
    /**
     * Save the new record
     * @param Model_Core_ManagementInformation_GeneratedBy $data The Data to be saved
     *
     */
    public function save($data){
        /* Zend does not implement a REPLACE into so we must SELECT/UPDATE */
        $rowExists = $this->getByPolicyNumber($data->policyNumber);
        if(count($rowExists) == 0){
            $dataToInsert = array(
                'policynumber' => $data->policyNumber,
                'csuid' => $data->csuId
                );
            $this->insert($dataToInsert);
        }else{
            $this->updateRow($data);
        }
    }
    
    /**
     * getByPolicyNumber, retrieves record as an array 
     * @param string $policyNumber Index of the Row
     * @return array The row to be returned
     */
    public function getByPolicyNumber($policyNumber){
        $select = $this->select();
        $select->where('policynumber = ?', $policyNumber);
        $returnArray = array();
        $returnArray = $this->fetchRow($select);
        return $returnArray;
    }
    
    
    /**
     * removes a record from the Table 
     * @param string $paymentRefno The payment to be removed from the dd table
     *
     */
    public function removeRecord($policyNumber){
        $where = $this->quoteInto('policynumber = ?', $policyNumber->policyNumber);
        $this->detete($where);
    }
    
    /**
     * updateRow updates a single row
     *
     *
     **/
    public function updateRow($data){
        $dataToUpdate = array(
        'policynumber' => $data->policyNumber,
        'csuid' => $data->csuId
        );
        $where = $this->quoteInto('policynumber = ?', $data->policyNumber);
        $this->update($dataToUpdate, $where);
    }
    
}
?>