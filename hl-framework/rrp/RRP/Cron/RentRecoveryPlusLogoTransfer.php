<?php

namespace RRP\Cron;
use RRP\DependencyInjection\LegacyContainer;
use RRP\DependencyInjection\RRPContainer;

/**
 * Class RentRecoveryPlusLogoTransfer
 *
 * @package RRP\Cron
 * @author April Portus <april.portus@barbon.com>
 */
final class RentRecoveryPlusLogoTransfer
{
    /**
     * Performs the logo transfer cron
     *
     * @throws \Exception
     */
    public function run()
    {
        $legacyContainer = new LegacyContainer();
        $rrpContainer = new RRPContainer();

        /** @var \Datasource_Core_Agents $agentClient */
        $agentClient = $legacyContainer->get('rrp.legacy.datasource.agent');

        $sftpLogoPath = $rrpContainer->get('rrp.config.sftp_logo_path');

        // Login to the SFTP server
        $sftp = $rrpContainer->get('rrp.sftp_client');
        if (!$sftp) {
            error_log(__FILE__ . ':' . __LINE__ . ':Remote Login Failed');
            throw new \Exception('Remote Login Failed');
        }

        // Get the list of logos to transfer
        $transferList = $agentClient->getAllDocumentLogoTransfers();

        // Attempt to put each file then check the size on the remote server matches to validate the transfer
        $successList = array();
        $failureList = array();
        foreach ($transferList as $sftpFileName => $uploadFileName) {
            $uploadFile = $sftpLogoPath . $sftpFileName;
            $sftp->put($sftpFileName, $uploadFile, NET_SFTP_LOCAL_FILE);

            $uploadSize = filesize($uploadFile);
            $sftpSize = $sftp->size($sftpFileName);
            if ($uploadSize == $sftpSize) {
                $successList[] = $sftpFileName;
            }
            else {
                $failureList[] = $sftpFileName;
            }
        }

        // Update the list of successful transfers
        if (count($successList) > 0) {
            if ( ! $agentClient->updateDocumentLogoTransfers($successList)) {
                $message = 'Failed to mark the successful logo transfers';
                error_log(__FILE__ . ':' . __LINE__ . ':' . $message);
                throw new \Exception($message);
            }
        }

        if (count($failureList) > 0) {
            $message = 'Failed to transfer files: ' . implode(', ', $failureList);
            error_log(__FILE__ . ':' . __LINE__ . ':' . $message);
            throw new \Exception($message);
        }
    }
}
