<?php
/**
 * Model definition for the agent cleanup notes table
 *
 * @category   Datasource
 * @package    Datasource_Core
 * @subpackage Agent
 */

class Datasource_Core_Agent_CleanupNotes extends Zend_Db_Table_Multidb {
    protected $_adapter = 'db_legacy_homelet';
    protected $_name = 'AGENTCLEANUPNOTES';
    protected $_primary = 'AGENTSCHEMENUMBER';
    protected $_multidb = 'db_legacy_homelet';
    protected $_referenceMap = array(
        'CleanupNotes' => array(
            'columns'       =>  array('AGENTSCHEMENUMBER'),
            'refTableClass' =>  'Model_Agent',
            'refColumns'    =>  array('agentschemeno')
    ));

    /**
     * This function will add a new note into the cleanupnotes table
     *
     * @param int schemeNumber
     * @param string note
     * @return boolean
     */
    public function addNote($schemeNumber, $note) {
        $data = array(
            'AGENTSCHEMENUMBER' =>  $schemeNumber,
            'DATETIME'          =>  new Zend_Db_Expr('NOW()'),
            'NOTES'     =>  $note
        );

        // Insert the data into a new row in the table
        if ($this->insert($data)) {
            return true;
        } else {
            // Failed insertion
            Application_Core_Logger::log("Can't insert note in table {$this->_name} (AGENTSCHEMENUMBER = {$schemeNumber})", 'error');
            return false;
        }
    }
}
?>