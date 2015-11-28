<?php
/**
 * Business rules class which provides keyhouse validation services.
 */
class Manager_Insurance_RentGuaranteeClaim_KeyhouseValidation  extends Zend_Db_Table_Abstract{

    protected $_khValidationModel;

    public function __construct(){
        $this->_khValidationModel = new Datasource_Insurance_RentGuaranteeClaim_KeyhouseValidation();
    }

    /**
     * Insert new validated return message for a claim from Key House.
     *
     * @param int $referenceNum, String $validityStatus, String $message, String $khReferenceNum
     *
     * This method provides a convenient way of inserting new validated message fr a claim.
     *
     * @return void
     */
    public function insertData($referenceNum, $validityStatus, $message, $khReferenceNum) {
        $this->_khValidationModel->insertData(
            $referenceNum, $validityStatus, $message, $khReferenceNum
        );
    }
}
?>