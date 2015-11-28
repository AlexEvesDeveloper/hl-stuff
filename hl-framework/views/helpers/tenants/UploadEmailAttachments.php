<?php

class Cms_View_Helper_UploadEmailAttachments extends Zend_View_Helper_Abstract {

    /**
     * Helper function for generating email attachment HTML fragment
     *
     * @param Model_Referencing_Reference
     * Encapsulates details of the reference.
     *
     * @return string
     */
    public function uploadEmailAttachments($reference) {
        
        $pageSession = new Zend_Session_Namespace('tenants_referencing_tracker');
        $tatMailManager = new Manager_Referencing_TatMail($reference);
        $attachments = $tatMailManager->detailAttachments();

        // Filter out names from complete paths and make HTML safe, and add up total file size
        $fileNames = array();
        $totalSize = 0;
        foreach($attachments as $file => $size) {
            $fileNames[] = htmlentities(substr($file, strrpos($file, '/') + 1));
            $totalSize += $size;
        }
        
        // Return partial view HTML
        return $this->view->partial(
            'tenants-referencing-tracker/partials/upload-email-attachments.phtml',
            array(
                'totalSize' => $totalSize,
                'maxSize' => 4194304, // TODO: parameterise!
                'attachments' => $fileNames
            )
        );
    }

}
