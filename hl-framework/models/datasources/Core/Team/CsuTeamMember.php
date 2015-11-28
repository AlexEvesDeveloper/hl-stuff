<?php
class Datasource_Core_Team_CsuTeamMember extends Zend_Db_Table_Multidb
{
    protected   $_name = 'csuTeamMember';
    protected   $_primary = 'id';
    protected   $_multidb = 'db_legacy_homelet';
    
    /**
     * Fetch a csu by the passed ID
     *
     * @param id id of csuTeamMember
     * @return array
     */
    public function getCsuTeamMemberByID($id){
        $select = $this->select();
        $select->where('id = ?', $id );
        $row = $this->fetchRow($select);
        
        if(count($row)){
        	$data = array(
        	   'id'        		=> $row->id,
        	   'csuTeamID' 		=> $row->csuTeamID,
        	   'csuid'     		=> $row->csuid,
        	   'positionTypeID' => $row->positionTypeID,
        	   'csuName'        => $this->_getName($row->csuid)
        	  	
        	);
        	return $data;          		 
        } else {
        	return false;
        }
        
    }
    
    /**
     * Gets a bunch of CSU team members by name 'like' match. Joins on the csu
     * table.
     * 
     * @param str $name
     * @return array
     */
    public function getCsuTeamMemberByName($name) {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('tm' => $this->_name)
            )
            ->join(
                array('c' => 'csu'),
                'tm.csuid=c.csuid',
                array());
        $where = $this->quoteInto("c.realname LIKE ?", $name . '%');
        $select->where($where);
        $rows = $this->fetchAll($select);
        $ret = array();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $csuTm = new Model_Core_CSU_TeamMember();
                $csuTm->populate($row);
                $ret[] = $csuTm;
            }
        }
        return $ret; 
    }
    
    /**
     * Fetch a csu Team by the csu 
     * For supervise use
     * @param csuid for the search
     * @return csuTeamID
     */
    public function getCsuTeamIDByCsu($csuid) {
        $select = $this->select();
        $select->where('csuid = ?', $csuid);
        $row = $this->fetchRow($select);
        if(count($row)> 0){
            return $row->csuTeamID;
        } else {
       		return false;
        }
    }
    
	/**
     * Fetch a list of member in one team 
     *     
     * @param csuTeamID csuTeamID for csuTeamMember
     * @return array
     */
    public function getTeamList( $csuTeamID ) {
      
        $csus=array();
        
        $select = $this->select();
        $select->where("csuTeamID= ?", $csuTeamID);
              
        
        $row = $this->fetchAll($select);

        foreach ($row as $data) {
            $csus[] = array(
                'id'               => $data['id'],
                'csuid'            => $data['csuid'],
           	    'positonTypeID'    => $data['positionTypeID'],
                'csuName'          => $this->_getName($data['csuid'])
                              
            );
        }

        return $csus;
    }
    
    /**
     * Fetch a team member name
     *     
     * @param csuid 
     * @return csuname
     */
    private function _getName( $csuid ) {
      
        $csu = new Datasource_Core_Team_Csu();
        return $csu->getCsuNameByID($csuid); 
    }   
}
?>

