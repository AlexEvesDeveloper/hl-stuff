<?php

/**
 * Manager class responsible for implementing Deed of Guarantee log-related
 * logic, and for binding together the internal blog domain objects and
 * datasources.
 *
 * @category   Manager
 * @package    Manager_Connect
 * @subpackage Doglog
 */
class Manager_Connect_Doglog {

    private $_doglogDatasource;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct() {

        $this->_doglogDatasource = new Datasource_Connect_Doglog();
    }

    /**
     * Push an agent user's ID into the Deed of Guarantee log.
     *
     * @param mixed $userId Agent user's unique ID in the legacy DB.
     *
     * @return mixed
     */
    public function logPush($userId) {

        return $this->_doglogDatasource->logPush($userId);
    }
}