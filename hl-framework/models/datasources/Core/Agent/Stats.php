<?php

/**
 * Model definition for the agent stats table.
 *
 * @category   Datasource
 * @package    Datasource_Core
 * @subpackage Agent
 */

class Datasource_Core_Agent_Stats extends Zend_Db_Table_Multidb {

    protected $_name = 'agent_stat';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_connect';

    /**
     * Fetch one or more stat values for an agent, searched based on the given
     * criteria.
     *
     * @param $statType An ID from Model_Core_Agent_StatType
     * @param $asn
     * @param $dateApplicable
     * @param $variant
     *
     * @return array Array of Model_Core_Agent_Stat objects.
     */
    public function getStat($statType, $asn, $dateApplicable = null, $variant = null) {

        // Select any stat entries matching the given criteria
        $where  = $this->quoteInto('agent_stat_type_id = ?', $statType);
        $where .= $this->quoteInto(' AND agent_id = ?', $asn);
        if (!is_null($dateApplicable)) {
            $where .= $this->quoteInto(' AND date_applicable = ?', $dateApplicable);
        }
        if (!is_null($variant)) {
            $where .= $this->quoteInto(' AND variant = ?', $variant);
        }

        // Fetch them
        $select = $this->select()
            ->where($where);
        $statArray = $this->fetchAll($select);

        // Place into array of domain objects
        $returnArray = array();
        foreach($statArray as $statRow) {

            $stat = new Model_Core_Agent_Stat();

            $stat->statTypeId           = $statRow['agent_stat_type_id'];
            $stat->agentSchemeNumber    = $statRow['agent_id'];
            $stat->dateApplicable       = (($statRow['date_applicable'] != '') ? $statRow['date_applicable'] : null);
            $stat->variant              = (($statRow['variant'] != '') ? $statRow['variant'] : null);
            $stat->value                = $statRow['value'];

            $returnArray[] = $stat;
        }

        return $returnArray;
    }

    /**
     * Writes a stat object to the persistent store.  If a stat with the same
     * basic attributes (not including the value) already exists, it will be
     * deleted and replaced.
     *
     * @param Model_Core_Agent_Stat $stat The stat to write.
     * @param bool $eraseFirst Optional switch for pre-erasing stat before
     * adding.
     *
     * @return void
     */
    public function setStat(Model_Core_Agent_Stat $stat, $eraseFirst = true) {

        if ($eraseFirst) {
            // Blindly erase if stat already exists
            $checkExists = $this->eraseStat(
                $stat->statTypeId,
                $stat->agentSchemeNumber,
                $stat->dateApplicable,
                $stat->variant
            );
        }

        // Insert stat
        $data = array(
            'agent_stat_type_id'    => $stat->statTypeId,
            'agent_id'              => $stat->agentSchemeNumber,
            'value'                 => $stat->value
        );
        if (!is_null($stat->dateApplicable)) {
            $data['date_applicable'] = $stat->dateApplicable;
        }
        if (!is_null($stat->variant)) {
            $data['variant'] = $stat->variant;
        }

        if (!$this->insert($data)) {
            // Failed insertion
            Application_Core_Logger::log("Can't insert agent stat in table {$this->_name} (scheme_number = {$schemeNumber})", 'error');
            return false;
        }
    }

    /**
     * Erases any stats matching the given criteria.
     *
     * @param $statType
     * @param $asn
     * @param $dateApplicable
     * @param $variant
     *
     * @return void
     */
    public function eraseStat($statType, $asn, $dateApplicable = null, $variant = null) {

        // Select any stat entries matching the given criteria
        $where  = $this->quoteInto('agent_stat_type_id = ?', $statType);
        $where .= $this->quoteInto(' AND agent_id = ?', $asn);
        if (!is_null($dateApplicable)) {
            $where .= $this->quoteInto(' AND date_applicable = ?', $dateApplicable);
        }
        if (!is_null($variant)) {
            $where .= $this->quoteInto(' AND variant = ?', $variant);
        }

        // And delete them
        if (!$this->delete($where)){
            //return false;
        }
    }
}