<?php

class Document_IndexController extends Zend_Controller_Action
{
    /**
     * Retrieve a document from the server
     *
     * @return void
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $requesthash = $request->getParam('h');
        $mac = $request->getParam('m');
        $config = Zend_Registry::get('params');
        $localcache_path = null;
        
        if (isset($config->dms) && isset($config->dms->localcache) && isset($config->dms->localcache->directory))
            $localcache_path = $config->dms->localcache->directory;
        
        if ($mac == self::_generateAuthKey($requesthash))
        {
            // Macs validate, return the file
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->getHelper('layout')->disableLayout();
            
            // Open and read the pdf
            $full_filepath = realpath(APPLICATION_PATH . '/../' . $localcache_path) . '/' . $requesthash . '.pdf';
            $fh = fopen($full_filepath, 'r');
            $file_content = fread($fh, filesize($full_filepath));
            fclose($fh);
            
            // Set content type as pdf
            $response = $this->getResponse()->setHeader('Content-Type', 'application/pdf');
            
            // Send the pdf
            echo $file_content;
        }
    }
    
    /**
     * Generate the mac key name. Must be the same function as used in the InsuranceFunctions.php
     *
     * @param string $requesthash Request hash of request
     */
    private function _generateAuthKey($requesthash)
    {
        $config = Zend_Registry::get('params');
        $secret = null;
        
        // Capture HMAC secret key
        if (isset($config->dms) && isset($config->dms->localcache) && isset($config->dms->localcache->hmacsecret))
            $secret = $config->dms->localcache->hmacsecret;
        
        if ($secret == null)
            throw new Exception('hmac secret not set');
        
        return strtoupper(Zend_Crypt_Hmac::compute($secret, 'sha256', $requesthash));
    }
}
