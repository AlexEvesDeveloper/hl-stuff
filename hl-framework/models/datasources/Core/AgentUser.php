<?php

/**
 * Class Datasource_Core_AgentUser
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Datasource_Core_AgentUser extends Zend_Db_Table_Multidb
{
    /**
     * @var string database name
     */
    protected $_name = 'agentid';

    /**
     * @var string primary id
     */
    protected $_primary = 'agentschemeno';

    /**
     * @var string database identifier
     */
    protected $_multidb = 'db_legacy_homelet';

    /**
     * Find if the agent is allowed to use Rent Recovery Plus Insurance.
     *
     * @param int $agentSchemeNumber
     * @param string $username
     * @return bool
     */
    public function canDisplayRRPI($agentSchemeNumber, $username)
    {
        $select = $this->select()
            ->from($this->_name, 'is_rrp_allowed')
            ->where('agentschemeno = ?', $agentSchemeNumber)
            ->where('username = ?', $username)
        ;

        $returnVal = false;

        $row = $this->fetchRow($select);
        if ($row) {
            $returnVal = $row->is_rrp_allowed;
        }
        return $returnVal;
    }

    /**
     * Find if the agent is allowed to view the check right information.
     *
     * @param int $agentSchemeNumber
     * @param string $username
     * @return bool
     */
    public function canDisplayCheckRight($agentSchemeNumber, $username)
    {
        $select = $this->select()
            ->from($this->_name, 'has_check_right')
            ->where('agentschemeno = ?', $agentSchemeNumber)
            ->where('username = ?', $username)
        ;

        $returnVal = false;

        $row = $this->fetchRow($select);
        if ($row) {
            $returnVal = $row->has_check_right;
        }
        return $returnVal;
    }

}
