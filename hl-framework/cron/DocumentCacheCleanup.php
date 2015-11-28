<?php

final class Cron_DocumentCacheCleanup
{
    public function run()
    {
        $cachetimeout = 0;
        $localcache_path = null;
        $config = Zend_Registry::get('params');
        
        if (isset($config->dms) && isset($config->dms->localcache) && isset($config->dms->localcache->directory))
            $localcache_path = $config->dms->localcache->directory;
        
        if (isset($config->dms) && isset($config->dms->localcache) && isset($config->dms->localcache->timeout))
            $cachetimeout = $config->dms->localcache->timeout;
        
        $path = realpath(APPLICATION_PATH . '/../' . $localcache_path);
        
        // Check the parameter is set, it is a directory and that the directory is writable.
        if ($localcache_path == null || $localcache_path == '' ||
            !is_dir($path) ||
            !is_writable($path))
        {
            // Cache directory not set, return soap fault
            error_log(__FILE__ . ':' . __LINE__ . ':Cache directory not set, unable to clean documents');
            return;
        }
        
        if ($cachetimeout <= 0)
        {
            // Cache timeout invalid
            error_log(__FILE__ . ':' . __LINE__ . ':Cache timeout invalid or not set');
            return;
        }
        
        // Set expiry time frame
        $expirytime = time() - $cachetimeout;
        
        
        
        $dir = opendir($path);
        
        while ($record = readdir($dir))
        {
            // Check file for matching PDF type
            if (preg_match('/.*\.pdf$/', $record) == 0)
                continue;
            
            $accesstime = fileatime($path . '/' . $record);
            
            // Check if failed to get access time, try the creation time
            if ($accesstime === false)
                $accesstime = filectime($path . '/' . $record);
            
            // Check if create time work, skip if failed    
            if ($accesstime === false)
                continue;
            
            // Check if access time is less than current time - timeout period
            if ($accesstime < $expirytime)
                unlink($path . '/' . $record);
        }
        
        closedir($dir);
    }
}
