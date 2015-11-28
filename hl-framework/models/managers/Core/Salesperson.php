<?php

/**
 * Manager class responsible for implementing salesperson-related business logic,
 * and for binding together the salesperson domain objects and datasources.
 *
 * @category   Manager
 * @package    Manager_Core
 * @subpackage Salesperson
 */
class Manager_Core_Salesperson {

    /**#@+
     * References to common aspects of the same salesperson stored in the datasources.
     */
    protected $_salespersonDatasource;
    protected $_salespersonId;
    protected $_salespersonObject;
    /**#@-*/

    public function __construct($salespersonId = null) {

        // Set up data sources
        $this->_salespersonDatasource = new Datasource_Core_Salesperson();

        if (!is_null($salespersonId)) {
            // Look up initial salesperson
            $this->getSalesperson($salespersonId);
        }
    }

	/**
	 * Look up and return a salesperson's details.
	 * Also sets the current salesperson properties.
	 *
	 * @param mixed $salespersonId
	 *
	 * @return Model_Core_Salesperson
	 *
	 * @throws Zend_Exception
	 * Throws a Zend_Exception if no Salesperson can be located.
	 */
    public function getSalesperson($salespersonId = null) {

        if (!is_null($salespersonId)) {
            $this->_salespersonId = $salespersonId;
        }

        if (!is_null($this->_salespersonId)) {
            // Get basic salesperson information
            $this->_salespersonObject = $this->_salespersonDatasource->getSalesperson($this->_salespersonId);
        }

        if (!is_null($this->_salespersonObject)) {
            return $this->_salespersonObject;
        }

        throw new Zend_Exception('Get salesperson failed');
    }

    /**
     * Fetch all available questions that can be posed to salespeople.
     *
     * @return array Array of (int)question ID => (string)question tuples.
     */
    public function getSalespersonAllQuestions() {
        return $this->_salespersonDatasource->getSalespersonAllQuestions();
    }
}