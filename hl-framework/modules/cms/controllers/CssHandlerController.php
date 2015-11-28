<?php
require_once 'lessc.inc.php';

class Cms_CssHandlerController extends Zend_Controller_Action
{
    public function init() 
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->getHelper('layout')->disableLayout();
    }

    public function indexAction() 
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $filename = $request->getRequestUri();
        $filePath = realpath(dirname(APPLICATION_PATH . '/../private' . $filename));

        // Validate this input
        //   must be alphanumeric / _ - or .
        //   must start with /assets
        //   must end with .css
        //   must be in the application paths private assets directory

        if (strpos($filePath, realpath(APPLICATION_PATH . '/../private/assets')) === 0 && preg_match('/^\/assets\/([a-zA-Z0-9\/_\-\.])+\.css$/', $filename)) {
            $cssInputFile = realpath(APPLICATION_PATH . '/../private' . $filename);

            // Check if its a css file. For optimisation purposes, we just return raw css files as is
            if (file_exists($cssInputFile)) {
                // File exists as a css file, just return the raw file
                $response->setHeader('Content-type', 'text/css; charset: UTF-8', true);

                echo file_get_contents($cssInputFile);
                return;
            }
            else {
                // File exists as a less file, compile the less file and throw back
                $lessInputFile = realpath(APPLICATION_PATH . '/../private' . str_replace('.css', '.less', $filename));
                $lessCacheTag = preg_replace('/[\.\-\/]/', '_', $lessInputFile);

                // Attempt to load the css from a cache
                $cache = Zend_Cache::factory(
                    'File',
                    'File',
                    array(
                        'master_files' => array($lessInputFile), // Check for modification of original file to expire entries
                        'lifetime' => 86400, // 1 day cache
                    ),
                    array(
                        'cache_dir' => realpath(APPLICATION_PATH . '/../private/cache/css/')
                    )
                );


                if (($fileContents = $cache->load($lessCacheTag)) === false) {
                    // Generate new and cache
                    try {
                        // Parse with the PHP based less parser
                        $lc = new lessc($lessInputFile);
                        $fileContents = $lc->parse();

                        // Cache the file
                        $status = $cache->save($fileContents); //, $lessCacheTag, array());

                    } catch (Exception $ex) {
                        // Throw out request as if it doesn't exist
                        $response->setHttpResponseCode(404);
                        return;
                    }
                }

                $response->setHeader('Content-type', 'text/css; charset: UTF-8', true);
                echo $fileContents;
                return;
            }
        }
        else {
            // Throw out request as if it doesn't exist
            $response->setHttpResponseCode(404);
        }
    }
}
