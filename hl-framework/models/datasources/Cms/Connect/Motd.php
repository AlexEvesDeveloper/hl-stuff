<?php
/**
 * Model definition for the Connect MOTD (Message of the Day) table
 */
class Datasource_Cms_Connect_Motd extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_homelet_connect';
    protected $_name = 'motd';
    protected $_primary = 'id';
    
    /**
     * Retrieve all MOTDs.
     *
     * @return array
     * A two-dimensional array of user messages.
     */
    public function getAll()
    {
        $select = $this->select();
        $select->where('is_archived != 1');
        $motdArray = $this->fetchAll($select);
        
        $returnArray = array();
        foreach($motdArray as $currentMotd)
        {
            //Retrieve the linked agent types associated with this message of the day.
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(array('matl' => 'motd_agent_types_map'), array('agent_types_id'));
            $select->where('matl.motd_id = ?', $currentMotd['id']);
            $agentTypes = $this->fetchAll($select);
            
            $agentTypesList = '';
            foreach ($agentTypes as $agentType) {
                
                if($agentTypesList == '') {
                    
                    $agentTypesList = $agentType['agent_types_id'];
                }
                else {
                
                    $agentTypesList .= ',' . $agentType['agent_types_id'];
                }
            }
            
            //Retrieve the linked agent user types associated with this message of the day.
            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(array('mautl' => 'motd_agent_user_types_map'), array('agent_user_types_id'));
            $select->where('mautl.motd_id = ?', $currentMotd['id']);
            $agentUserTypes = $this->fetchAll($select);
            
            $agentUserTypesList = '';
            foreach ($agentUserTypes as $agentUserType) {
                
                if($agentUserTypesList == '') {
                    
                    $agentUserTypesList = $agentUserType['agent_user_types_id'];
                }
                else {
                    
                    $agentUserTypesList .= ',' . $agentUserType['agent_user_types_id'];
                }
            }
            
            $returnArray[] = array(
                'id' => $currentMotd['id'],
                'lastUpdated' => $currentMotd['last_updated'],
                'displayFrom' => $currentMotd['display_from'],
                'displayTo' => $currentMotd['display_to'],
                'agentTypes' => $agentTypesList,
                'agentUserTypes' => $agentUserTypesList,
                'motdTitle' => $currentMotd['title'],
                'message' => $currentMotd['message'],
                'active' => $currentMotd['active'],
                'displayWidth' => $currentMotd['display_width']
            );
        }
        
        return $returnArray;
    }
    
    /**
     * Retrieve a specific MOTD by ID.
     *
     * @param int $id
     * The MOTD identifier.
     * 
     * @return array
     * Returns a one-dimensional array containing the specified MOTD values.
     */
    public function getByID($id)
    {
        $select = $this->select();
        $select->where('id = ?', $id);
        $select->where('is_archived != 1');
        $motd = $this->fetchRow($select);
        
        //Retrieve the linked agent types associated with this message of the day.
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('matm' => 'motd_agent_types_map'), array('agent_types_id'));
        $select->where('matm.motd_id = ?', $id);
        $agentTypes = $this->fetchAll($select);
        
        $agentTypesList = '';
        foreach ($agentTypes as $agentType) {
            
            $agentTypesList .= $agentType['agent_types_id'] . ', ';
        }
        
        //Retrieve the linked agent user types associated with this message of the day.
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('mautl' => 'motd_agent_user_types_map'), array('agent_user_types_id'));
        $select->where('mautl.motd_id = ?', $id);
        $agentUserTypes = $this->fetchAll($select);
        
        $agentUserTypesList = '';
        foreach ($agentUserTypes as $agentUserType) {
            
            $agentUserTypesList .= $agentUserType['agent_user_types_id'] . ', ';
        }
        
        $returnArray = array(
            'id' => $motd->id,
            'lastUpdated' => $motd->last_updated,
            'displayFrom' => $motd->display_from,
            'displayTo' => $motd->display_to,
            'agentTypes' => $agentTypesList,
            'agentUserTypes' => $agentUserTypesList,
            'motdTitle' => $motd->title,
            'message' => $motd->message,
            'active' => $motd->active,
            'displayWidth' => $motd->display_width,
            'isArchived' => $motd->is_archived
        );
        
        return $returnArray;
    }
    
    /**
     * Save changes to an existing MOTD.
     *
     * @param int $id
     * The MOTD identifier.
     *
     * @param Zend_Date $lastUpdated
     * Zend_Date object with the date of this change.
     *
     * @param Zend_Date $displayFrom
     * Indicates when this MOTD should be displayed from.
     *
     * @param Zend_Date $displayTo
     * Indicates when this MOTD should cease being displayed.
     *
     * @param array $agentTypes
     * Array of agent type ids.
     *
     * @param array $agentUserTypes
     * Array of agent user type ids.
     *
     * @param string $title
     * The MOTD title.
     *
     * @param string $message
     * The message displayed by the MOTD.
     *
     * @param boolean $status
     * True if the MOTD should be active, false otherwise.
     *
     * @param int $displayWidth
     * The width of the MOTD window, in pixels.
     *
     * @param boolean $isArchived
     * True if the MOTD is to be archived, false otherwise.
     */
    public function saveChanges($id, $lastUpdated, $displayFrom, $displayTo, $agentTypes, $agentUserTypes, $title, $message, $status, $displayWidth, $isArchived)
    {
        //First update the message of the day table.
        $where = $this->quoteInto('id = ?', $id);
        
        if($isArchived) {
            
            $isArchived = 1;
        } else {
            
            $isArchived = 0;
        }
        
        $data = array(
            'id' => $id,
            'last_updated' => $lastUpdated->toString('YYYY-MM-dd'),
            'display_from' => $displayFrom->toString('YYYY-MM-dd'),
            'display_to' => $displayTo->toString('YYYY-MM-dd'),
            'title' => $title,
            'message' => $message,
            'active' => $status,
            'display_width' => $displayWidth,
            'is_archived' => $isArchived
        );
        
        $this->update($data, $where);
        
        //Update the motd agent types map table.
        $motdAgentTypesMap = new Datasource_Cms_Connect_MotdAgentTypesMap();
        $motdAgentTypesMap->removeAllMaps($id);
        $motdAgentTypesMap->addMap($id, $agentTypes);
        
        //Update the motd agent user types map table.
        $motdAgentUserTypesMap = new Datasource_Cms_Connect_MotdAgentUserTypesMap();
        $motdAgentUserTypesMap->removeAllMaps($id);
        $motdAgentUserTypesMap->addMap($id, $agentUserTypes);
    }
    
    /**
     * Function which sets the status of a specified MOTD to 'activated' or 'deactivated'.
     *
     * @param int $id
     * The unique MOTD identifier.
     *
     * @param boolean $status
     * True to set the MOTD active, false otherwise.
     *
     * @return void
     */
    public function setActiveStatus($id, $status)
    {
        //Convert the boolean to integers that are required by the datasource.
        if($status) {
            
            $value = 1;
        } else {
            
            $value = 0;
        }
        
        $where = $this->quoteInto('id = ?', $id);
        $data = array('active' => $value);
        $this->update($data, $where);
    }
    
    /**
     * Function which sets the archived status of a specified MOTD.
     *
     * If a MOTD is archived, it will no longer appear on the screen, and will
     * no longer be returned in search results.
     *
     * @param int $id
     * The unique MOTD identifier.
     *
     * @param boolean $status
     * True to archive the MOTD, false otherwise.
     *
     * @return void
     */
    public function setArchivedStatus($id, $status)
    {
        //Convert the boolean to integers that are required by the datasource.
        if($status) {
            
            $value = 1;
        } else {
            
            $value = 0;
        }
        
        $where = $this->quoteInto('id = ?', $id);
        $data = array('is_archived' => $value);
        $this->update($data, $where);
    }
    
    /**
     * Adds a new MOTD.
     *
     * @param Zend_Date $lastUpdated
     * Zend_Date object with the date of this change.
     *
     * @param Zend_Date $displayFrom
     * Indicates when this MOTD should be displayed from.
     *
     * @param Zend_Date $displayTo
     * Indicates when this MOTD should cease being displayed.
     *
     * @param array $agentTypes
     * Array of agent type ids.
     *
     * @param array $agentUserTypes
     * Array of agent user type ids.
     *
     * @param string $title
     * The MOTD title.
     *
     * @param string $message
     * The message displayed by the MOTD.
     *
     * @param boolean $status
     * True if the MOTD should be active, false otherwise.
     *
     * @param int $displayWidth
     * The width of the MOTD window, in pixels.
     *
     * @return int
     * Returns the ID of the newly created MOTD.
     */
    public function addNew($lastUpdated, $displayFrom, $displayTo, $agentTypes, $agentUserTypes, $title, $message, $status, $displayWidth)
    {
        //First add the message of the day.                
        $data = array(
            'last_updated' => $lastUpdated->toString('YYYY-MM-dd'),
            'display_from' => $displayFrom->toString('YYYY-MM-dd'),
            'display_to' => $displayTo->toString('YYYY-MM-dd'),
            'title' => $title,
            'message' => $message,
            'active' => $status,
            'display_width' => $displayWidth
        );
        
        $id = $this->insert($data);
        
        //Now add the motd agent types map.
        $motdAgentTypesMap = new Datasource_Cms_Connect_MotdAgentTypesMap();
        $motdAgentTypesMap->addMap($id, $agentTypes);
        
        //Add the motd agent user types map table.
        $motdAgentUserTypesMap = new Datasource_Cms_Connect_MotdAgentUserTypesMap();
        $motdAgentUserTypesMap->addMap($id, $agentUserTypes);
        
        return $id;
    }
    
    /**
     * Deletes an MOTD.
     *
     * @param int $id
     * The ID of the MOTD to delete.
     */
    public function remove($id)
    {
        //First delete from the message of the day table.
        $where = $this->quoteInto('id = ?', $id);
        $this->delete($where);
        
        //Delete from the motd agent types map table.
        $motdAgentTypesMap = new Datasource_Cms_Connect_MotdAgentTypesMap();
        $motdAgentTypesMap->removeAllMaps($id);
        
        //Delete from the motd agent types map table.
        $motdAgentUserTypesMap = new Datasource_Cms_Connect_MotdAgentUserTypesMap();
        $motdAgentUserTypesMap->removeAllMaps($id);
    }
}
?>