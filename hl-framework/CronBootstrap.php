<?php

/**
 * Sub class to extend the standard MVC bootstrapper.
 * Put specific cron related boot strapping here, and override
 * existing methods that should be disabled with empty method
 * implementations
 */
require_once(dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php'); // The bootstap class is not namedspaced, load manually
final class CronBootstrap extends Bootstrap
{
    /**
     * Cron class name, must be set to run a cron
     * @var string
     */
    private $_cronclass = null;
    
    // Disable these bootstrap initialisations
    protected function _initRoutes() {}
    protected function _initFirePHP() {}
    protected function _initDbCaching() {}
        
    /**
     * Autoloading
     */
    protected function _initAutoload()
    {
        $autoloader = parent::_initAutoload();
        $autoloader->addResourceType('cron', 'cron/', 'Cron');
        return $autoloader;
    }
    
    /**
     * Set the cron class to use
     *
     * @param string $name Cron class name
     * @return CronBootstrap Self
     */
    public function setCronClass($name)
    {
        $this->_cronclass = $name;
        return $this;
    }
    
    /**
     * Run the application
     *
     * @return void
     */
    public function run()
    {
        $class = $this->_cronclass;
        
        if (!is_string($class))
            throw new Exception('Class name not set');
        
        // Create new object and run
        $cron = new $class();
        $cron->run();
    }
}
