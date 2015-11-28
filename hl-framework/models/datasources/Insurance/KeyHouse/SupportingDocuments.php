<?php
/**
* Model definition for the supporting_documents table.
*/
class Datasource_Insurance_KeyHouse_SupportingDocuments extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_keyhouse';
    protected $_name = 'supporting_documents';
    protected $_primary = 'id';

   /**
    * Inserts new supporting document records for a claim.
    *
    * @param array
    *
    * @return boolean
    */
    public function save($data) {
        $returnVal = "";
        if(is_array($data)) {
            unset($data['id']);
            if($data['reference_number'] != "" && $data['supporting_document_name'] != "")
                $returnVal = $this->insert($data);
        }
        return $returnVal;
    }
}
?>