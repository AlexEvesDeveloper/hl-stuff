<?php
/**
* Model definition for the rent guarantee claims next number table.
*/
class Datasource_Insurance_Keyhouse_NexNumber extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_keyhouse';
    protected $_name = 'online_claims';
    protected $_primary = 'reference_number';

    /**
     * Generates the next claim ID from the SQL Server next number table
     */
    public function generateClaimId()
    {

    }
}