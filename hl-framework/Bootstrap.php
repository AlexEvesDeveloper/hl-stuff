<?php

use Iris\DependencyInjection\Container AS IrisContainer;
use RRP\DependencyInjection\RRPContainer;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Initialise composer globals
     *
     * @return void
     */
    protected function _initComposer()
    {
        /** Composer PSR-0 Autoloader */
        require_once __DIR__ . '/../vendor/autoload.php';
    }

    /**
     * Initialise the timezone
     *
     * @return void
     */
    protected function _initTimezone() {

		// Set the default timezone
        date_default_timezone_set('Europe/London');

		// Set the locale so that Zend_Date and Zend_Currency objects function
		// as expected.
		Zend_Registry::set('Zend_Locale', new Zend_Locale('en_GB'));
    }

    /**
     * Define constants from application.ini
     *
     * @return void
     */
    protected function _initConstants()
    {
        $options = $this->getOption('constants');

        if (is_array($options)) {
            foreach($options as $key => $value) {
                if(!defined($key)) {
                    define($key, $value);
                }
            }
        }
    }

    /**
     * Add error handling
     *
     * @return void
     */
    protected function _initErrorHandler() {
    	$front = Zend_Controller_Front::getInstance();
		$front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
		    'module'     => 'error',
		    'controller' => 'error',
		    'action'     => 'error'
		)));
    }

    /**
     * Add databases to the registry
     *
     * @return void
     */
    protected function _initDbRegistry() {
        $this->bootstrap('multidb');
        $multidb = $this->getPluginResource('multidb');
        Zend_Registry::set('db_referencing', $multidb->getDb('referencing'));
        Zend_Registry::set('db_homelet_cms', $multidb->getDb('cms'));
        Zend_Registry::set('db_homelet_admin', $multidb->getDb('homeletadmin'));
        Zend_Registry::set('db_homelet_insurance_com', $multidb->getDb('insurance'));
        Zend_Registry::set('db_homelet_connect', $multidb->getDb('connect'));
		Zend_Registry::set('db_homelet', $multidb->getDb('homelet'));

        Zend_Registry::set('db_legacy_referencing', $multidb->getDb('referencinglegacy'));
        Zend_Registry::set('db_legacy_slave_referencing', $multidb->getDb('referencingslavelegacy'));

        Zend_Registry::set('db_legacy_homelet', $multidb->getDb('homeletlegacy'));
        Zend_Registry::set('db_legacy_slave_homelet', $multidb->getDb('homeletslavelegacy'));

        Zend_Registry::set('db_legacy_webleads', $multidb->getDb('webleadslegacy'));
        Zend_Registry::set('db_legacy_homeletDW', $multidb->getDb('homeletDWlegacy'));
		Zend_Registry::set('db_portfolio', $multidb->getDb('portfolio'));
		Zend_Registry::set('db_keyhouse', $multidb->getDb('keyhouse'));
		Zend_Registry::set('db_letting_agents', $multidb->getDb('letting_agents'));
		
		Zend_Registry::set('db_fsa', $multidb->getDb('fsa'));
    }

    /**
     * Setup the system logger, the simple system logger (used for fast, low-resolution logging) and the MI logger, as
     * configured by constants defined in application.ini
     *
     * @return void
     */
    protected function _initSyslog()
    {
        $dbAdapterSystem = Zend_Registry::get(SYSTEM_LOGGER_DB_HANDLE);

        $dbAdapterUser = Zend_Registry::get(USER_LOGGER_DB_HANDLE);

        // System logger, including full trace
        $fullColumnMapping = array(
            'level' => 'priority',
            'levelCode' => 'priorityName',
            'message' => 'message',
            'extendedMessage' => 'extendedMessage',
            'eventTime' => 'timestamp',
            'ipAddress' => 'ipAddress',
            'requestUrl' => 'requestURL',
            'file' => 'file',
            'line' => 'line',
            'trace' => 'trace'
        );
        $logger = $this->createLogger(
            SYSTEM_LOGGER_TYPE,
            $dbAdapterSystem,
            SYSTEM_LOGGER_DB_TABLE,
            SYSTEM_LOGGER_STREAM_PATH,
            SYSTEM_LOGGER_SYSLOG_APPLICATION,
            SYSTEM_LOGGER_SYSLOG_FACILITY,
            $fullColumnMapping
        );
        Zend_Registry::set('logger', $logger);

        // Simple system logger, bare bones
        $simpleColumnMapping = array(
            'message' => 'message',
            'level' => 'priority',
            'levelCode' => 'priorityName',
            'eventTime' => 'timestamp',
            'requestUrl' => 'requestURL'
        );
        $simpleLogger = $this->createLogger(
            SYSTEM_LOGGER_TYPE,
            $dbAdapterSystem,
            SYSTEM_LOGGER_DB_TABLE,
            SYSTEM_LOGGER_STREAM_PATH,
            SYSTEM_LOGGER_SYSLOG_APPLICATION,
            SYSTEM_LOGGER_SYSLOG_FACILITY,
            $simpleColumnMapping
        );
        Zend_Registry::set('simpleLogger', $simpleLogger);

        // Modified logger for MI info-level user events, used by Application_Core_ActivityLogger::log()
        $miColumnMapping = array(
            'message' => 'message',
            'extendedMessage' => 'extendedMessage',
            'eventCode' => 'eventCode',
            'eventTime' => 'timestamp',
            'ipAddress' => 'ipAddress',
            'system' => 'system',
            'username' => 'username'
        );
        $miLogger = $this->createLogger(
            USER_LOGGER_TYPE,
            $dbAdapterUser,
            USER_LOGGER_DB_TABLE,
            USER_LOGGER_STREAM_PATH,
            USER_LOGGER_SYSLOG_APPLICATION,
            USER_LOGGER_SYSLOG_FACILITY,
            $miColumnMapping
        );
        Zend_Registry::set('activityLogger', $miLogger);
    }

    /**
     * Instantiate a Zend_Log logger, depending on the logger type specified in application.ini
     *
     * @param string $type
     * @param mixed $dbAdapter
     * @param string $dbTable
     * @param string $streamPath
     * @param string $syslogApplication
     * @param string $syslogFacility
     * @param array $columnMapping
     * @return Zend_Log
     */
    private function createLogger(
        $type,
        $dbAdapter,
        $dbTable,
        $streamPath,
        $syslogApplication,
        $syslogFacility,
        $columnMapping
    )
    {
        // Instantiate a writer
        switch ($type) {
            case 'Db':
                $writer = new Zend_Log_Writer_Db($dbAdapter, $dbTable, $columnMapping);
                break;
            case 'Stream':
                $writer = new Zend_Log_Writer_Stream($streamPath);
                break;
            case 'Syslog':
                $writer = new Zend_Log_Writer_Syslog(array(
                    'application' => $syslogApplication,
                    'facility' => $syslogFacility
                ));
                break;
            case 'DetailedStream':
                $writer = new Zend_Log_Writer_DetailedStream($streamPath, $columnMapping);
                break;
            case 'DetailedSyslog':
                $writer = new Zend_Log_Writer_DetailedSyslog(
                    array(
                        'application' => $syslogApplication,
                        'facility' => $syslogFacility
                    ),
                    $columnMapping
                );
                break;
            case 'Null':
            default:
                $writer = new Zend_Log_Writer_Null();
                break;
        }

        // Instantiate a logger using the new writer
        $logger = new Zend_Log($writer);

        return $logger;
    }

    /**
     * Start Autoloader
     *
     * @access protected
     * @return Zend_Application_Module_Autoloader
     */
    protected function _initAutoload() {
    	$autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath'  => dirname(__FILE__)
        ));

		$autoloader->addResourceType('objects', 'models/objects/', 'Model');
		$autoloader->addResourceType('managers', 'models/managers/', 'Manager');
		$autoloader->addResourceType('datasources', 'models/datasources/', 'Datasource');
		$autoloader->addResourceType('services', 'models/services/', 'Service');
		$autoloader->addResourceType('authentication', 'models/authentication/', 'Auth');
		$autoloader->addResourceType('forms', 'forms/', 'Form');
		// $autoloader->addResourceType('subforms', 'subforms/', 'Subform');

		return $autoloader;
    }

    /**
     * Define custom routing
     *
     * @access protected
     * @return void
     */
    protected function _initRoutes()
    {
        // TODO: This needs to go in a routing .ini file using
        //   "routes.toplevel_domain.chains.rest.type = Zend_Rest_Route", but as
        //   PB and I have found out this doesn't work, at least in this version
        //   of ZF
        // Set up REST routing - solution from http://mwop.net/blog/228-Building-RESTful-Services-with-Zend-Framework
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        // Specifying the "rest" module only as RESTful:
        $restRoute = new Zend_Rest_Route(
            $front,
            array(),
            array(
                'rest',
            )
        );
        $router->addRoute('rest', $restRoute);

    	$config = new Zend_Config(array(), true);

		foreach (glob(ROUTES_FOLDER . "*.ini") as $configFile) {
			$config->merge(new Zend_Config_Ini($configFile));
		}

        $router = $front->getRouter();
		$router->addConfig($config, 'routes');
    }

    protected function _initDbCache()
    {
    	if (APPLICATION_CACHING) {
	    	// Setup file cache on database queries to prevent hundreds of describe statements
			$frontendOptions = array('automatic_serialization' => true);

			$backendOptions  = array('cache_dir' => APPLICATION_PATH . '/../private/cache/db/');

			$cache = Zend_Cache::factory('Core',
			                             'File',
			                             $frontendOptions,
			                             $backendOptions);
			Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
		}
    }

    protected function _initCache()
    {

    }

    /**
     * Loads custom parameters from a config file
     *
     * @access protected
     * @return void
     */
    protected function _initParams() {
        // Load global parameters
		$config = new Zend_Config(array(), true);
		foreach (glob(CONFIG_FOLDER . "*.ini") as $configFile) {
			$config->merge(new Zend_Config_Ini($configFile, APPLICATION_ENV));
		}

		Zend_Registry::set('params', $config);
    }

	protected function _initDBLogging() {
		if (APPLICATION_ENV == 'development') {
			Application_Core_Logger::logDBQueries();
		}
	}

    /**
     * Initialise IRIS service container
     *
     * @return void
     */
    protected function _initIrisServiceContainer()
    {
        Zend_Registry::set('iris_container', new IrisContainer());
    }

    /**
     * Initialise RRP service container
     *
     * @return void
     */
    protected function _initRRPServiceContainer()
    {
        Zend_Registry::set('rrp_container', new RRPContainer());
    }
}

