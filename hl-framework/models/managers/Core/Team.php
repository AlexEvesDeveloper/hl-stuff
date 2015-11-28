<?php
class Manager_Core_Team
{
    protected $team;
    private $teamMemberIds = array();
    
    /**
     * Returns an array of Model_Core_CSU_TeamMember objects by searching by
     * team manager CSU id.
     * 
     * @param int $csuId
     */
    public function getTeamByManagerCsuId($csuId)
    {
        $csuTeamDs = new Datasource_Core_Team_CsuTeam();
        $this->team = $csuTeamDs->getMembersByManagerCsuId($csuId);
        $this->setTeamMemberIds();
        return $this->team;
        
    }
    
    /**
     * Returns an array of Model_Core_CSU_TeamMember objects by searching by
     * the CSU realname property with a LIKE sss% match.
     * 
     * @param str $name
     */
    public function getTeamMembersByNameSearch($name)
    {
        $csuTeamMemDs = new Datasource_Core_Team_CsuTeamMember();
        $this->team = $csuTeamMemDs->getCsuTeamMemberByName($name);
        $this->setTeamMemberIds();
        return $this->team;
    }
    
    /**
     * Sets up an array of team member ids - useful for the form generation.
     */
    private function setTeamMemberIds()
    {
        foreach ($this->team as $teamMember) {
        	$this->teamMemberIds[] = $teamMember->getId();
        }    
    }
    
    /**
     * Returns an array of team member ids if they are set. Exception otherwise.
     */
    public function getTeamMemberIds()
    {
        return count($this->teamMemberIds) ? 
            $this->teamMemberIds : new Zend_Exception();
    }
    
}