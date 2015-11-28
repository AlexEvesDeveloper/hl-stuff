<?php

include(APPLICATION_PATH . '/../library/phpqrcode/qrlib.php');

class Connect_View_Helper_QrCode extends Zend_View_Helper_Abstract
{

    public function qrCode($config = array())
    {
        // TODO: Get rid of reliance on an unchecked/untyped config array.
        // TODO: Have some default values for the more esoteric config.
        // TODO: Better caching.

        // Check if a QR code image already exists and was created less than so
        //   many seconds ago
        clearstatcache();
        if (!file_exists($config['filePath']) || filemtime($config['filePath']) < time() - $config['lifetime']) {
            // Generate a new QR code image
            QRcode::png(
                $config['content'],
                $config['filePath'],
                $config['errorCorrection'],
                $config['size'],
                $config['boundary']
            );
        }

        // Pass the QR code URL and name to the view script partial
        $qrData = array(
            'url' => $config['urlPath'],
            'name' => (isset($config['name']) && '' != $config['name']) ? $config['name'] : 'QR Code'
        );

        return $this->view->partial('partials/qr-code.phtml', $qrData);
    }

}