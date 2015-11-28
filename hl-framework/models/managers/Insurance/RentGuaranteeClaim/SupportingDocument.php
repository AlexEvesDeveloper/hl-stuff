<?php
/**
* Business rules class providing supporting document services.
*/
class Manager_Insurance_RentGuaranteeClaim_SupportingDocument extends Zend_Db_Table_Abstract {

    protected $_params;
    protected $_supportingDocumentModel;
    protected $_onlineclaimDir = "ocsupportingdocument";
    protected $_claimReferenceNumber;
    protected $_agentSchemeNumber;

    public function __construct($referenceNum, $agentSchemeNum) {
        $this->_supportingDocumentModel = new Datasource_Insurance_RentGuaranteeClaim_SupportingDocuments();
        $this->_claimReferenceNumber = $referenceNum;
        $this->_agentSchemeNumber = $agentSchemeNum;

        $this->_params = Zend_Registry::get('params');
    }

    /**
     * Returns saved Document types for a claim.
     *
     * This method will retrieve supporting document types
     *
     * Returns this object populated with relevant information, or null if no
     * relevant information has been stored.
     * @return array
     */

    public function getDocumentTypes() {
        return $this->_supportingDocumentModel->getDocumentTypes() + array('badfile' => 'Bad file');
    }

    /**
     * Prepares the upload directories
     */
    public function prepareUploadDirs() {

        $uploadPath = $this->_params->connect->supportingDocFilePath;
        if(!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777);
        }
        if(!file_exists($uploadPath.$this->_onlineclaimDir."/")) {
            mkdir($uploadPath.$this->_onlineclaimDir."/", 0777);
        }
        if(!file_exists($uploadPath.$this->_onlineclaimDir."/".$this->_agentSchemeNumber."/")) {
            mkdir($uploadPath.$this->_onlineclaimDir."/".$this->_agentSchemeNumber."/", 0777);
        }

        $supportDocPath = $uploadPath.$this->_onlineclaimDir."/".$this->_agentSchemeNumber."/".$this->_claimReferenceNumber."/";

        if(!file_exists($supportDocPath)) {
            mkdir($supportDocPath, 0777);
        }

    }

    /**
     * Save supporting documents
     *
     * This method provides a convenient way of inserting a supporting documents.
     *
     * @return String
     */
    public function saveSupportingDocument()
    {
        $files = $this->_uploadSupportingdocument();

        foreach($files as $info) {
            if ($info['type'] != '') {
                $filename = substr(
                    $info['fullPath'],
                    strlen(APPLICATION_PATH."/../private/uploads/")
                );
                $info['id'] = $this->_supportingDocumentModel->addSupportingDocument(
                    $this->_claimReferenceNumber, $info['type'], $filename
                );
            }
        }

        $descriptionMap = $this->getDocumentTypes();

        $file = new stdClass();
        $file->name         = $info['name'];
        $file->size         = $info['size'];
        $file->description  = $descriptionMap[$info['type']];
        // Add in download URL
        $file->url          = "/rentguaranteeclaims/download?d={$info['id']}&crn={$this->_claimReferenceNumber}";
        // Add in delete URL and HTTP method
        $file->delete_url   = '/json/rg-claims-file-uploader?file=' . $info['id'];
        $file->delete_type  = 'DELETE';

        // Handle error messages
        if (isset($info['error'])) {
            $file->error = $info['error'];
        }

        return array($file);
    }

    /**
     * To upload supporting document
     *
     * @param int $referenceNum, int $agentSchemeNum
     *
     * This method provides a convenient way of uploading the supporting document.
     *
     */
    private function _uploadSupportingDocument() {

        $upload = new Zend_File_Transfer_Adapter_Http();
        // Reword error messages in upload validator
        $uploadValidator = $upload->getValidator('Upload');
        $uploadValidator->setMessages(array(
            'fileUploadErrorIniSize'        => 'File exceeds the maximum allowed size',
            'fileUploadErrorFormSize'       => 'File exceeds the defined form size',
            'fileUploadErrorPartial'        => 'File was only partially uploaded',
            'fileUploadErrorNoFile'         => 'File was not uploaded',
            'fileUploadErrorNoTmpDir'       => 'No temporary directory was found for file',
            'fileUploadErrorCantWrite'      => 'File can\'t be written',
            'fileUploadErrorExtension'      => 'An error occurred while uploading the file',
            'fileUploadErrorAttack'         => 'File was illegally uploaded',
            'fileUploadErrorFileNotFound'   => 'File was not found',
            'fileUploadErrorUnknown'        => 'Unknown error while uploading file'
        ));
        // Add extension validator
        $upload->addValidator('Extension', false, implode(',', $this->_supportingDocumentModel->getDocumentExtensions()));
        $arrFiles = $upload->getFileInfo();
        foreach($arrFiles as $key=>$value) {
            if($value['name']=="") {
                unset($arrFiles[$key]);
            }
        }

        $upload->setDestination($this->getPath());
        $arrSupportedDocs = array();

        foreach ($arrFiles as $file => $info) {
            if ($upload->isValid($file)) {

                foreach ($_POST as $postKey => $postVal) {
                    if (substr($postKey, 0, 9) == 'filename_') {
                        if ($postVal == $info['name']) {
                            $index = substr($postKey, 9);
                            $type = $_POST["description_{$index}"];
                        }
                    }
                }

                $upload->receive($file);

                $arrSupportedDocs[] = array(
                    'type'      => $type,
                    'fullPath'  => $this->getPath() . $info['name'],
                    'name'      => $info['name'],
                    'size'      => filesize($this->getPath() . $info['name'])
                );
            } else {
                // Validation failed
                $arrSupportedDocs[] = array(
                    'id'        => '',
                    'type'      => 'badfile',
                    'fullPath'  => '',
                    'name'      => $info['name'],
                    'size'      => '',
                    'error'     => implode(', ', $upload->getMessages())
                );
            }
        }
        return $arrSupportedDocs;
    }

    /**
     * To define upload path
     *
     * @return string Directory.
     */

    public function getPath() {
        return sprintf(
            '%s/../private/uploads/%s/%s/%s/',
            APPLICATION_PATH,
            $this->_onlineclaimDir,
            $this->_agentSchemeNumber,
            $this->_claimReferenceNumber
        );
    }

    /**
     * To download uploaded document
     *
     * @param int $agentSchemeNum, int $refNum, int $docId
     *
     * This method provides a convenient way of downloading uploaded supporting document.
     *
     */
    public function downloadSupportingDocument($docId) {
        $filePath = APPLICATION_PATH . "/../private/uploads/" .
            $this->_supportingDocumentModel->getAttachmentFilenameById(
                $docId,
                $this->_claimReferenceNumber
            );
        if($filePath != "") {
            if(file_exists($filePath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.str_replace(" ","_",basename($filePath)));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));
                ob_clean();
                flush();
                readfile($filePath);
            } else {
                echo "Document not found..";
            }
        } else {
            echo "Access denied.";
        }
    }

    /**
     * To delete uploaded document
     *
     * @param int $agentSchemeNum, int $refNum, int $docId
     *
     * This method provides a convenient way for deleting uploaded document.
     *
     */
    public function deleteSupportingDocument() {

        // Get file ID from GET superglobal
        $id = (isset($_GET['file'])) ? $_GET['file'] : '0';

        // Delete document from DB
        $deletedRow = $this->_supportingDocumentModel->deleteByRefnoAndId(
            $this->_claimReferenceNumber,
            $id
        );

        // Delete from file storage
        $filePath = APPLICATION_PATH . "/../private/uploads/{$deletedRow['attachment_filename']}";
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    public function getSupportingDocumentList() {

        $results = $this->_supportingDocumentModel->getByReferenceNumber($this->_claimReferenceNumber);

        $descriptionMap = $this->getDocumentTypes();

        // Add in some extra details needed by UI
        foreach($results as $key => $result) {
            // Add in file size by looking in file storage
            $results[$key]->size = @filesize(APPLICATION_PATH . "/../private/uploads/{$result->fullPath}");

            // Add in looked-up description
            $results[$key]->description = $descriptionMap[$result->type];

            // Add in download URL
            $results[$key]->url = "/rentguaranteeclaims/download?d={$result->id}&crn={$this->_claimReferenceNumber}";

            // Add in delete URL and HTTP method
            $results[$key]->delete_url = '/json/rg-claims-file-uploader?file=' . $result->id;
            $results[$key]->delete_type = 'DELETE';

        }

        return $results;
    }

}
?>