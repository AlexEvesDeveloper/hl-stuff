<?php
/**
* Model definition for the supporting_documents table.
*/
class Datasource_Insurance_RentGuaranteeClaim_SupportingDocuments extends Zend_Db_Table_Multidb
{

    /**#@+
     * Mandatory attributes
     */
    protected $_name = 'rent_guarantee_claims_supporting_documents';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_connect';
    /**#@-*/

    /**
     * Return allowable document types in an associative array.
     *
     * @return array
     */
    public function getDocumentTypes()
    {
        return array(
            'tenancy_agreement'         => 'Copy of the full Tenancy Agreement(s)',
            'rent_schedule'             => 'Complete Rent Schedule',
            'determination'             => 'Pre-tenancy determination(s)',
            'covenant'                  => 'Guarantor\'s Covenant(s)',
            'residency_proof'           => 'Proof of residency',
            'final_reference'           => 'Final reference reports (including terms and conditions)',
            'income_proof'              => 'Proof of income',
            'additional_information'    => 'Additional information',
            'section21_or_section8'     => 'Section 21/Section 8 (if already served)'
        );
    }

    /**
     * Return an array of allowable document extensions.
     * TODO: Parameterise.
     *
     * @return array
     */
    public function getDocumentExtensions()
    {
        return array(
            'csv',
            'doc',
            'docx',
            'gif',
            'htm',
            'html',
            'ief',
            'jpe',
            'jpeg',
            'jpg',
            'pdf',
            'png',
            'rgb',
            'rtf',
            'svg',
            'tif',
            'tiff',
            'txt',
            'xls',
            'xlsx'
        );
    }

    /**
     * Insert a supporting document for a claim into the persistent store.
     *
     * @param unknown_type $referenceNumber
     * @param string $documentType Must be from the list of keys given by $this->getDocumentTypes()
     * @param unknown_type $filename
     *
     * @return boolean
     */
    public function addSupportingDocument($referenceNumber, $documentType, $filename)
    {
        $returnVal = false;

        if (array_key_exists($documentType, $this->getDocumentTypes())) {
            $data = array(
                'reference_number'          => $referenceNumber,
                'supporting_document_name'  => $documentType,
                'attachment_filename'       => $filename
            );

            $returnVal = $this->insert($data);
        }

        return $returnVal;
    }

    /**
     * Gets supporting documents by reference number.
     *
     * @param int $referenceNumber
     *
     * @return object
     */
    public function getByReferenceNumber($referenceNum) {

        $select = $this->select();
        $select->where('reference_number = ?', $referenceNum);

        $results = array();
        $rowset = $this->fetchAll($select);

        foreach ($rowset as $row) {
            $data = new stdClass();
            $data->id       = $row['id'];
            $data->type     = $row['supporting_document_name'];
            $data->fullPath = $row['attachment_filename'];
            $pathParts      = explode('/', $row['attachment_filename']);
            $data->name     = array_pop($pathParts);
            $results[] = $data;
        }
        return $results;
    }

     /**
     * To get the documents by ID and Reference Number
     *
     * @param int $docId, $referenceNumber
     */
    public function getAttachmentFilenameById($docId, $referenceNumber) {

        $select = $this->select();
        $select->where('reference_number = ?', $referenceNumber);
        $select->where('id = ?', $docId);
        $row = $this->fetchRow($select);

        if (count($row) > 0) {
            return $row['attachment_filename'];
        } else {
            return "";
        }
    }

    /**
     *
     * Delete all the supporting documents for the given Reference number
     * @param int $referenceNum
     * @return void;
     */
    public function deleteByReferenceNumber($referenceNumber) {

        $where = $this->getAdapter()->quoteInto('reference_number = ?', $referenceNumber);
        $this->delete($where);
    }

    /**
     * Delete a named saved file from the DB.
     *
     * @param mixed $referenceNumber
     * @param mixed $fileId
     *
     * @return string
     */
    public function deleteByRefnoAndId($referenceNumber, $fileId) {

        // Select row being deleted to return afterwards
        $select = $this->select();
        $select->where('reference_number = ?', $referenceNumber);
        $select->where('id = ?', $fileId);
        $deletedRow = $this->fetchRow($select);
        try {
            $deletedRow = $deletedRow->toArray();

            // Actual deletion
            $where  = $this->quoteInto('reference_number = ?', $referenceNumber);
            $where .= $this->quoteInto(' AND id = ?', $fileId);
            $this->delete($where);
        } catch (Exception $e) {
            $deletedRow = '';
        }

        return $deletedRow;
    }
}