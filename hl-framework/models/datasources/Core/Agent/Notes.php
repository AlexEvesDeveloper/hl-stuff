<?php
/**
 * Model definition for the agent notes table
 *
 * @category   Datasource
 * @package    Datasource_Core
 * @subpackage Agent
 */

class Datasource_Core_Agent_Notes extends Zend_Db_Table_Multidb {
    protected $_name = 'agentNotes';
    protected $_primary = 'agentschemeno';
    protected $_multidb = 'db_legacy_homelet';
    protected $_referenceMap = array(
        'Notes' => array(
            'columns'       =>  array('agentschemeno'),
            'refTableClass' =>  'Model_Agent',
            'refColumns'    =>  array('agentschemeno')
    ));

    /**
     * This function will add a new note into the agentNotes table
     *
     * @param int schemeNumber
     * @param string note
     * @return boolean
     *
     */
    public function addNote($schemeNumber, $note, $csuid) {
        $data = array(
            'agentschemeno' =>  $schemeNumber,
            'dateEntered'   =>  new Zend_Db_Expr('NOW()'),
            'csuid'         =>  $csuid,
            'text'          =>  $note
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