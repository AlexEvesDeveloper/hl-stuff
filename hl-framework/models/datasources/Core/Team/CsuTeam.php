<?php
class Datasource_Core_Team_CsuTeam extends Zend_Db_Table_Multidb
{
    protected   $_name = 'csuTeam';
    protected   $_primary = 'id';
    protected   $_multidb = 'db_legacy_homelet';
    
    /**
     * Fetch a csuTeam by the passed ID
     *
     * @param id id of the csuTeam
     * @return array
     */
    public function getCsuTeamByID($id)
    {
        $select = $this->select();
        $select->where('id = ?', $id );
        $row = $this->fetchRow($select);
        if(count($row)){
       	    return $row->toArray();
        } else {
        	return false;
        }
        
    }
    
    /**
     * Fetch a csuteam by the team name
     *
     * @param name name for the search
     * @return array
     */
    public function getCsuTeamByName($name) 
    {
        $select = $this->select();
        $select->where('name = ?', $name);
        $row = $this->fetchRow($select);
        if(count($row) > 0){
       		return $row->toArray();
        } else {
       		return false;
        }
    }
    
    /**
     * 
     * @param int $managerCsuId
     */
    public function getMembersByManagerCsuId($managerCsuId)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('t' => $this->_name),
                array()
            )
            ->join(
                array('tm' => 'csuTeamMember'),
                'tm.csuTeamID = t.id'
            )
            ->where('t.managercsuid = ?', $managerCsuId)
            ->order('tm.id')
            ->group('tm.csuid');
        $row = $this->fetchAll($select);
        $team = array();
        if (count($row)) {
            foreach ($row as $data) {
            	$member = new Model_Core_CSU_TeamMember();
                $member->populate($data->toArray());
                $team[] = $member;
            }
        } 
        return $team;
    }
	
}
?>
