<?php
/**
 * Model definition for MotdAgentTypesMap table.
 */
class Datasource_Cms_Connect_MotdAgentTypesMap extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_homelet_connect';
    protected $_name = 'motd_agent_types_map';
    protected $_primary = 'id';
    
    /**
     * Function which adds a map (association) between a MOTD and an agent type.
     *
     * @param int $motdId
     * The MOTD ID.
     *
     * @param array $agentTypeIds
     * The array of agent type IDs.
     */
    public function addMap($motdId, $agentTypeIds)
    {
        foreach($agentTypeIds as $currentAgentTypeId) {
            $data = array(
                'motd_id' =>  $motdId,
                'agent_types_id' => $currentAgentTypeId
            );
            $this->insert($data);
        }
    }
    
    /**
     * Removes all agent maps corresponding to a particular motd, as specified
     * by the motdId passed in.
     *
     * @param int $motdId
     * The MOTD ID against which all associated agent maps should be deleted.
     */
    public function removeAllMaps($motdId)
    {
        $where = $this->quoteInto('motd_id = ?', $motdId);
        $this->delete($where);
    }
}
?>