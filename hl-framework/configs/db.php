<?php
include(dirname(__FILE__) . '/db_auth.php');

// Define database connections
return array(
    'resources' => array(
        'multidb' => array(
            'homeletlegacy' => array (
                'adapter'       => 'PDO_MYSQL',
                'host'			=> $dbParams['MYSQL4_HOST'],
                'username'      => $dbParams['LEGACY_HOMELET_USER'],
                'password'      => $dbParams['LEGACY_HOMELET_PASSWORD'],
                'dbname'        => 'homeletuk_com',
                'persistent'    => false
            ),
            'homeletslavelegacy' => array (
                'adapter'       => 'PDO_MYSQL',
                'host'          => $dbParams['MYSQL4SLAVE_HOST'],
                'username'      => $dbParams['LEGACY_SLAVE_HOMELET_USER'],
                'password'      => $dbParams['LEGACY_SLAVE_HOMELET_PASSWORD'],
                'dbname'        => 'homeletuk_com',
                'persistent'    => false
            ),
            'webleadslegacy' => array (
                'adapter'       => 'PDO_MYSQL',
                'host'          => $dbParams['MYSQL4_HOST'],
                'username'      => $dbParams['LEGACY_WEBLEADS_USER'],
                'password'      => $dbParams['LEGACY_WEBLEADS_PASSWORD'],
                'dbname'        => 'FORMWATCHER',
                'persistent'    => false
            ),
            'referencinglegacy' => array (
                'adapter'       => 'PDO_MYSQL',
                'host'          => $dbParams['MYSQL4_HOST'],
                'username'      => $dbParams['LEGACY_REFERENCING_USER'],
                'password'      => $dbParams['LEGACY_REFERENCING_PASSWORD'],
                'dbname'        => 'referencing_uk',
                'persistent'    => false
            ),
            'referencingslavelegacy' => array (
                'adapter'       => 'PDO_MYSQL',
                'host'          => $dbParams['MYSQL4SLAVE_HOST'],
                'username'      => $dbParams['LEGACY_SLAVE_REFERENCING_USER'],
                'password'      => $dbParams['LEGACY_SLAVE_REFERENCING_PASSWORD'],
                'dbname'        => 'referencing_uk',
                'persistent'    => false
            ),
            'referencing' => array (
                'adapter'       => 'PDO_MYSQL',
                'host'          => $dbParams['MYSQL5_HOST'],
                'username'      => $dbParams['REFERENCING_USER'],
                'password'      => $dbParams['REFERENCING_PASSWORD'],
                'dbname'    	=> 'referencing',
                'persistent'    => false
            ),
            'cms' => array (
                'adapter'       => 'PDO_MYSQL',
                'host'          => $dbParams['MYSQL5_HOST'],
                'username'      => $dbParams['HOMELET_CMS_USER'],
                'password'      => $dbParams['HOMELET_CMS_PASSWORD'],
                'dbname'        => 'homelet_cms',
                'persistent'    => false
            ),
            'homeletadmin' => array (
                'adapter'       => 'PDO_MYSQL',
                'host'          => $dbParams['MYSQL5_HOST'],
                'username'      => $dbParams['HOMELET_ADMIN_USER'],
                'password'      => $dbParams['HOMELET_ADMIN_PASSWORD'],
                'dbname'        => 'homelet_admin',
                'persistent'    => false
            ),
            'portfolio'	=> array (
                'adapter'       => 'PDO_MYSQL',
                'host'          => $dbParams['MYSQL5_HOST'],
                'username'      => $dbParams['PORTFOLIO_USER'],
                'password'      => $dbParams['PORTFOLIO_PASSWORD'],
                'dbname'        => 'portfolio',
                'persistent'    => false
            ),
            'insurance'	=> array (
                'adapter'       => 'PDO_MYSQL',
                'host'          => $dbParams['MYSQL4_HOST'],
                'username'      => $dbParams['LEGACY_INSURANCE_USER'],
                'password'      => $dbParams['LEGACY_INSURANCE_PASSWORD'],
                'dbname'        => 'homelet_insurance_com',
                'persistent'    => false
            ),
            'connect' => array (
                'adapter'       => 'PDO_MYSQL',
                'host'          => $dbParams['MYSQL5_HOST'],
                'username'      => $dbParams['CONNECT_USER'],
                'password'      => $dbParams['CONNECT_PASSWORD'],
                'dbname'        => 'homelet_connect',
                'persistent'    => false
            ),
            'homelet' => array (
                'adapter'       => 'PDO_MYSQL',
                'host'          => $dbParams['MYSQL5_HOST'],
                'username'      => $dbParams['HOMELET_USER'],
                'password'      => $dbParams['HOMELET_PASSWORD'],
                'dbname'        => 'homelet',
                'persistent'    => false,
                'default'       => true
            ),
            'homeletDWlegacy' => array (
                'adapter'       => 'PDO_MYSQL',
                'host'          => $dbParams['MYSQL4_HOST'],
                'username'      => $dbParams['LEGACY_DATAWAREHOUSE_USER'],
                'password'      => $dbParams['LEGACY_DATAWAREHOUSE_PASSWORD'],
                'dbname'        => 'homeletDW',
                'persistent'    => false
            ),
            'keyhouse' => array (
                'adapter'       => 'PDO_DBLIB',
                'host'          => $dbParams['KEYHOUSE_HOST'],
                'username'      => $dbParams['KEYHOUSE_USER'],
                'password'      => $dbParams['KEYHOUSE_PASSWORD'],
                'dbname'        => 'keyhouse',
                #'persistent'   => true,
                #'port'         => '2196'
            ),
            'letting_agents' => array (
                'adapter'       => 'PDO_MYSQL',
                'host'          => $dbParams['MYSQL5_HOST'],
                'username'      => $dbParams['LETTING_AGENT_USER'],
                'password'      => $dbParams['LETTING_AGENT_PASSWORD'],
                'dbname'        => 'letting_agents',
                'persistent'    => false
            ),
            'fsa' => array (
                'adapter'       => 'PDO_MYSQL',
                'host'          => $dbParams['MYSQL4_HOST'],
                'username'      => $dbParams['LEGACY_FSA_USER'],
                'password'      => $dbParams['LEGACY_FSA_PASSWORD'],
                'dbname'        => 'FSA',
                'persistent'    => false
            )
        )
    )
);