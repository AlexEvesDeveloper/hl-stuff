<?php
/**
 * Class for remotely updating rent guarantee claim
 *
 */
class Service_Connect_RGCSAccessor {

    /**
     * Gets a data stream of files by claim reference number
     *
     * Collates all uploaded files into a zip file on the server. Also makes md5
     * hash of zip file so that, once transferred, data integrity can be tested
     * at the consumer end.
     *
     * Subsequently the zip file is deleted.
     *
     * @param int $refNo rent guarantee claim reference number
     * @return array data
     */
    public function getFileByRefNo($refNo)
    {
        $claimMan = new Manager_Insurance_RentGuaranteeClaim_Claim();
        $files = $claimMan->getSupportingDocumentsByReferenceNumber($refNo);

        if (count($files) == 0) {
            return array(
                'item' => sprintf('Error - No files registered for CRN %s', $refNo),
                'hash' => False
            );
        }

        // Setup paths and file location
        $base_path = APPLICATION_PATH . '/../private/uploads/';
        $zip_path = $base_path . 'rentguaranteeclaims/zips/';

        // Check for file path sanity
        if (!file_exists($zip_path)) {
            return array(
                'item' => sprintf('Error - file path <%s> does not exist', $zip_path),
                'hash' => False
            );
        }

        // Create archive
        $zip = new ZipArchive();
        $archive = sprintf($zip_path . 'files_%s.zip', $refNo);

        // Create archive
        if ($zip->open($archive, ZIPARCHIVE::CREATE)!==TRUE) {
            return array(
                'item' => sprintf('Error - cannot create <%s>', $archive),
                'hash' => False
            );
        }

        // Add the files
        foreach ($files as $file) {
            $zip->addFile(
                $base_path . $file->fullPath,
                array_pop(explode('/', $file->fullPath))
            );
        }
        $zip->close();
        $md5 = md5_file($archive);

        // Read the zip
        $fh = fopen($archive, 'r');
        $zipData = fread($fh, filesize($archive));

        // Clean up
        fclose($fh);
        unlink($archive);

        // Return the data to the caller
        return array('data' => base64_encode($zipData), 'hash' => $md5);
    }

    /**
     * Returns a list of data complete but untransmitted claims ids
     *
     * @return Array Containing data complete but untransmitted claims ids
     */
    public function listDataCompleteRefIds()
    {
        $claimMan = new Manager_Insurance_RentGuaranteeClaim_Claim();
        return $claimMan->fetchDataCompleteClaimsIds();
    }

    /**
     * Exec a cleanup of the claim, post
     */

    /**
     * Completes the claim by reference number
     *
     * @param int $refNo rent guarantee claim reference number
     * @return bool success
     */
    public function completeClaimByRefNo($refNo)
    {
        // Completed flag
        $completed = false;

        // This returns the validity status of the claim to update MySQL
        $dsKHClaim = new Datasource_Insurance_KeyHouse_Validation();
        $khResult = $dsKHClaim->getValidatedByRefNo($refNo);

        // Get claim from MySQL
        $claimManager = new Manager_Insurance_RentGuaranteeClaim_Claim($refNo);
        $claim = $claimManager->getKHClaim($refNo);

        // Update claim in MySQL with validity status from SQL Server
        // If validity status has some value
        if (is_object($claim) && $khResult['validity_status'] != '') {
            // Success
            $claim->setSubmittedToKeyHouse(1);
            // Update claim status
            $claimManager->updateClaim($claim);
            // Update validation table
            $validator = new Manager_Insurance_RentGuaranteeClaim_KeyhouseValidation();
            // Insert claim result
            if($khResult['validity_status'] == "YES") {
                // Send submission confirmation email to agent
                $claimManager->sendConfirmationEmail($refNo, $khResult['kcrn']);
                // Insert results
                $validator->insertData(
                    $refNo, 'Success', 'In progress',
                    $khResult['kcrn']
                );
            } else {
                // Insert results
                $validator->insertData(
                    $refNo, 'Failure', $khResult['reason'],
                    'None'
                );
            }
            // We have success
            $completed = true;
            //}
        }
        // Done.
        return $completed;
    }
}