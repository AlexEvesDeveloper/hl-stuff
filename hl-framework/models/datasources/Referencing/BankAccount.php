 <?php
 
/**
 * Model definition for the bank_account datasource.
 */
class Datasource_Referencing_BankAccount extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_referencing';    
    protected $_name = 'bank_account';
    protected $_primary = 'reference_id';
    
    /**
     * Inserts a new, empty BankAccount into the datasource and returns a corresponding object.
     *
     * @param integer $referenceId
     * Links the new BankAccount to the Reference.
     *
	 * @return Model_Referencing_BankAccount
	 * Encapsulates the details of the newly inserted BankAccount.
     */
    public function insertPlaceholder($referenceId) {
    
        $this->insert(array('reference_id' => $referenceId));
        
        $bankAccount = new Model_Referencing_BankAccount();
        $bankAccount->referenceId = $referenceId;
        $returnVal = $bankAccount;
        
        return $returnVal;
    }
    
    /**
     * Updates an existing BankAccount.
     *
     * @param Model_Referencing_BankAccount
     * The BankAccount details to update in the datasource.
     *
     * @return void
     */
    public function updateBankAccount($bankAccount) {
        
        if(empty($bankAccount)) {
            
            return;
        }
        
        if(empty($bankAccount->isValidated)) {
            
            $isValidated = false;
        }
        else {
            
            $isValidated = true;
        }
        
        $data = array(
            'account_number' => $bankAccount->accountNumber,
            'sort_code' => $bankAccount->sortCode,
            'is_validated' => $isValidated
        );

        $where = $this->quoteInto('reference_id = ?', $bankAccount->referenceId);
        $this->update($data, $where);
    }
    
    /**
     * Retrieves the specified BankAccount.
     *
     * @param integer $referenceId
     * The unique Reference identifier.
     *
     * @return mixed
     * The BankAccount details, encapsulated in a Model_Referencing_BankAccount
     * object, or null if the BankAccount cannot be found.
     */
    public function getByReferenceId($referenceId) {
        
        $select = $this->select();
        $select->where('reference_id = ?', $referenceId);
        $bankAccountRow = $this->fetchRow($select);
        
        if(empty($bankAccountRow)) {
            
            $returnVal = null;
        }
        else {
            
            $bankAccount = new Model_Referencing_BankAccount();
            $bankAccount->referenceId = $referenceId;
            $bankAccount->accountNumber = $bankAccountRow->account_number;
            $bankAccount->sortCode = $bankAccountRow->sort_code;
            
            if(empty($bankAccountRow->is_validated)) {
                
                $bankAccount->isValidated = false;
            }
            else {
                
                $bankAccount->isValidated = true;
            }
            
            $returnVal = $bankAccount;
        }
        
        return $returnVal;
    }
    
    /**
     * Deletes an existing bank account.
     *
     * @param Model_Referencing_BankAccount
     * The BankAccount to delete.
     *
     * @return void
     */
    public function deleteBankAccount($bankAccount) {
        
        $where = $this->quoteInto('reference_id = ?', $bankAccount->referenceId);
        $this->delete($where);
    }
}

?>