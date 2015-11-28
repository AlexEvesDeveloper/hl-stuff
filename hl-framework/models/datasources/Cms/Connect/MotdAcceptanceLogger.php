<?php

/**
 * Datasource definition for connecting to and updating the legacy MotD log
 * table.  Provides local equivalent to Ben's MotdAcceptanceLogger class in
 * legacy PHP4 Connect.
 *
 * @category   Datasource
 * @package    Datasource_Cms
 * @subpackage MotdAcceptanceLogger
 */
class Datasource_Cms_Connect_MotdAcceptanceLogger extends Zend_Db_Table_Multidb {

    protected $_name = 'motd_agent_user_map';
    protected $_primary = 'motd_id';
    protected $_multidb = 'db_legacy_homelet';

    public function checkMotdAccepted($motdId, $agentId) {

        $select = $this->select()
            ->from($this->_name)
            ->where('`agent_user_id` = ?', $agentId)
            ->where('`motd_id` = ?', $motdId);
        $motdAccepted = $this->fetchAll($select);

        return (count($motdAccepted) > 0);
    }

    public function markMotdAccepted($motdId, $agentId) {

        // Check this MotD isn't already marked as read
        if (!$this->checkMotdAccepted($motdId, $agentId)) {

            // Insert a record indicating the agent user has read the MotD
            $data = array(
                'agent_user_id' => $agentId,
                'motd_id'       => $motdId,
                'date_read'     => new Zend_Db_Expr('NOW()')
            );

            return $this->insert($data);
        }
    }
}