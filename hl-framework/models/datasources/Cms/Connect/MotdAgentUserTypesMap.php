<?php
/**
 * Model definition for the MotdAgentUserTypesMap table.
 */
class Datasource_Cms_Connect_MotdAgentUserTypesMap extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_homelet_connect';
    protected $_name = 'motd_agent_user_types_map';
    protected $_primary = 'id';
    
    /**
     * Function which adds a map (association) between a MOTD and an agent
     * user type.
     *
     * @param int $motdId
     * The MOTD ID.
     *
     * @param array $agentUserTypeIds
     * The array of agent user type IDs.
     */
    public function addMap($motdId, $agentUserTypeIds)
    {
        foreach($agentUserTypeIds as $currentAgentUserTypeId) {
            $data = array(
                'motd_id' =>  $motdId,
                'agent_user_types_id' => $currentAgentUserTypeId
            );
            $this->insert($data);
        }
    }
    
    /**
    * Removes all agent user maps corresponding to a particular motd, as
    * specified by the motdId passed in.
    *
    * @param int $motdId
    * The MOTD ID against which all associated agent user maps should be deleted.
    */
    public function removeAllMaps($motdId)
    {
        $where = $this->quoteInto('motd_id = ?', $motdId);
        $this->delete($where);
    }
}
?>