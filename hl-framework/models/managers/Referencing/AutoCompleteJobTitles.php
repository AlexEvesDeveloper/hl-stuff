<?php

/**
 * Copyright 2015 Barbon Insurance Group.
 *
 * Closed source, all rights reserved.
 *
 * Unless required by applicable law or agreed to in writing, 
 * distribution is prohibited.
 */

/**
 * Encapsulates the reference job title business logic.
 *
 * All access to the reference job title datasources should be through this class.
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class Manager_Referencing_AutoCompleteJobTitles
{
    /**
     * @var Zend_Db_Table_Multidb
     */
    protected $dbTable;

    /**
     * Set $dbTable
     *
     * @param string Class name of the datasource table
     *
     * @return $this
     */
    public function setDbTable($value)
    {
        if (is_string($value)) {
            $this->dbTable = new $value();
        }
        
        if ( ! $this->dbTable instanceof Zend_Db_Table_Multidb) {
            throw new Exception('Invalid datasource provided');
        }
            
        return $this;
    }

    /**
     * Get $dbTable
     *
     * @return Datasource_Referencing_JobTitle
     */
    public function getDbTable()
    {
        if ($this->dbTable === null){
            $this->setDbTable('Datasource_Referencing_AutoCompleteJobTitles');
        }

        return $this->dbTable;
    }

    /**
     * Get all rows.
     *
     * @return array
     */
    public function findAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();

        return $this->processResultSet($resultSet);              
    }

    /**
     * Find rows where 'fast_track' column matches the given (boolean) $status.
     *
     * @param boolean The status to match against
     *
     * @return array
     */
    public function findByFastTrackStatus($status)
    {
        $db = $this->getDbTable();

        $select = $db->select()->where('fast_track = ?', $status);
        $resultSet = $db->fetchAll($select);

        return $this->processResultSet($resultSet);
    }

    /**
     * Take a DB result set and create a Model_Referencing_AutoCompleteJobTitle object with each row
     *
     * @param array DB results
     *
     * @return array
     */
    private function processResultSet($resultSet)
    {
        $entities = array();

        foreach ($resultSet as $row) {
            $entity = new Model_Referencing_AutoCompleteJobTitles();
            $entity->setId($row->id);
            $entity->setTitle($row->title);
            $entity->setFastTrack($row->fast_track);

            $entities[] = $entity;
        }

        return $entities;             
    }
}