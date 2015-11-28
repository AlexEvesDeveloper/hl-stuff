<?php
class Model_Core_Csu extends Model_Abstract
{
    protected $csuid;
    protected $realname;
    protected $username;
    protected $password;
    protected $centre;
    protected $whitelabelID;
    protected $profileID;
    protected $riskaddress;
    protected $editsched;
    protected $backpay;
    protected $editDates;
    protected $email;
    protected $directdial;
    protected $directfax;
    protected $adminFeeChanger;
    protected $switchNetGross;
    protected $cancelGS;
    protected $isAssessor;
    protected $isDataEnterer;
    protected $isSupervisor;
    protected $isManager;
    protected $isAccounts;
    protected $caseLimit;
    protected $superiorid;
    protected $changeRates;
    protected $groupSchemesUser;
    protected $piUser;
    protected $hasleft;
    protected $useAgentAdmin;
    protected $agentOnStop;
    protected $premiumOverride;
    protected $isCRM;
    protected $isTeam;
    protected $isTester;
    protected $isWebDataCapturer;
    protected $isCSUPA;
    protected $isWebDataCapturerAdmin;
    protected $pnpPassword;
    
    /**
     * Does a basic population job
     */
    public function populate($data)
    {
        $this->setcsuid((isset($data['csuid'])) ? $data['csuid'] : null);
        $this->setrealname((isset($data['realname'])) ? $data['realname'] : null);
        $this->setusername((isset($data['username'])) ? $data['username'] : null);
        $this->setpassword((isset($data['password'])) ? $data['password'] : null);
        $this->setcentre((isset($data['centre'])) ? $data['centre'] : null);
        $this->setwhitelabelID((isset($data['whitelabelID'])) ? $data['whitelabelID'] : null);
        $this->setprofileID((isset($data['profileID'])) ? $data['profileID'] : null);
        $this->setriskaddress((isset($data['riskaddress'])) ? $data['riskaddress'] : null);
        $this->seteditsched((isset($data['editsched'])) ? $data['editsched'] : null);
        $this->setbackpay((isset($data['backpay'])) ? $data['backpay'] : null);
        $this->seteditDates((isset($data['editDates'])) ? $data['editDates'] : null);
        $this->setemail((isset($data['email'])) ? $data['email'] : null);
        $this->setdirectdial((isset($data['directdial'])) ? $data['directdial'] : null);
        $this->setdirectfax((isset($data['directfax'])) ? $data['directfax'] : null);
        $this->setadminFeeChanger((isset($data['adminFeeChanger'])) ? $data['adminFeeChanger'] : null);
        $this->setswitchNetGross((isset($data['switchNetGross'])) ? $data['switchNetGross'] : null);
        $this->setcancelGS((isset($data['cancelGS'])) ? $data['cancelGS'] : null);
        $this->setisAssessor((isset($data['isAssessor'])) ? $data['isAssessor'] : null);
        $this->setisDataEnterer((isset($data['isDataEnterer'])) ? $data['isDataEnterer'] : null);
        $this->setisSupervisor((isset($data['isSupervisor'])) ? $data['isSupervisor'] : null);
        $this->setisManager((isset($data['isManager'])) ? $data['isManager'] : null);
        $this->setisAccounts((isset($data['isAccounts'])) ? $data['isAccounts'] : null);
        $this->setcaseLimit((isset($data['caseLimit'])) ? $data['caseLimit'] : null);
        $this->setsuperiorid((isset($data['superiorid'])) ? $data['superiorid'] : null);
        $this->setchangeRates((isset($data['changeRates'])) ? $data['changeRates'] : null);
        $this->setgroupSchemesUser((isset($data['groupSchemesUser'])) ? $data['groupSchemesUser'] : null);
        $this->setpiUser((isset($data['piUser'])) ? $data['piUser'] : null);
        $this->sethasleft((isset($data['hasleft'])) ? $data['hasleft'] : null);
        $this->setuseAgentAdmin((isset($data['useAgentAdmin'])) ? $data['useAgentAdmin'] : null);
        $this->setagentOnStop((isset($data['agentOnStop'])) ? $data['agentOnStop'] : null);
        $this->setpremiumOverride((isset($data['premiumOverride'])) ? $data['premiumOverride'] : null);
        $this->setisCRM((isset($data['isCRM'])) ? $data['isCRM'] : null);
        $this->setisTeam((isset($data['isTeam'])) ? $data['isTeam'] : null);
        $this->setisTester((isset($data['isTester'])) ? $data['isTester'] : null);
        $this->setisWebDataCapturer((isset($data['isWebDataCapturer'])) ? $data['isWebDataCapturer'] : null);
        $this->setisCSUPA((isset($data['isCSUPA'])) ? $data['isCSUPA'] : null);
        $this->setisWebDataCapturerAdmin((isset($data['isWebDataCapturerAdmin'])) ? $data['isWebDataCapturerAdmin'] : null);
        $this->setpnpPassword((isset($data['pnpPassword'])) ? $data['pnpPassword'] : null);
    }
    
	/**
     * @return the $csuid
     */
    public function getCsuid ()
    {
        return $this->csuid;
    }

	/**
     * @return the $realname
     */
    public function getRealname ()
    {
        return $this->realname;
    }

	/**
     * @return the $username
     */
    public function getUsername ()
    {
        return $this->username;
    }

	/**
     * @return the $password
     */
    public function getPassword ()
    {
        return $this->password;
    }

	/**
     * @return the $centre
     */
    public function getCentre ()
    {
        return $this->centre;
    }

	/**
     * @return the $whitelabelID
     */
    public function getWhitelabelID ()
    {
        return $this->whitelabelID;
    }

	/**
     * @return the $profileID
     */
    public function getProfileID ()
    {
        return $this->profileID;
    }

	/**
     * @return the $riskaddress
     */
    public function getRiskaddress ()
    {
        return $this->riskaddress;
    }

	/**
     * @return the $editsched
     */
    public function getEditsched ()
    {
        return $this->editsched;
    }

	/**
     * @return the $backpay
     */
    public function getBackpay ()
    {
        return $this->backpay;
    }

	/**
     * @return the $editDates
     */
    public function getEditDates ()
    {
        return $this->editDates;
    }

	/**
     * @return the $email
     */
    public function getEmail ()
    {
        return $this->email;
    }

	/**
     * @return the $directdial
     */
    public function getDirectdial ()
    {
        return $this->directdial;
    }

	/**
     * @return the $directfax
     */
    public function getDirectfax ()
    {
        return $this->directfax;
    }

	/**
     * @return the $adminFeeChanger
     */
    public function getAdminFeeChanger ()
    {
        return $this->adminFeeChanger;
    }

	/**
     * @return the $switchNetGross
     */
    public function getSwitchNetGross ()
    {
        return $this->switchNetGross;
    }

	/**
     * @return the $cancelGS
     */
    public function getCancelGS ()
    {
        return $this->cancelGS;
    }

	/**
     * @return the $isAssessor
     */
    public function getIsAssessor ()
    {
        return $this->isAssessor;
    }

	/**
     * @return the $isDataEnterer
     */
    public function getIsDataEnterer ()
    {
        return $this->isDataEnterer;
    }

	/**
     * @return the $isSupervisor
     */
    public function getIsSupervisor ()
    {
        return $this->isSupervisor;
    }

	/**
     * @return the $isManager
     */
    public function getIsManager ()
    {
        return $this->isManager;
    }

	/**
     * @return the $isAccounts
     */
    public function getIsAccounts ()
    {
        return $this->isAccounts;
    }

	/**
     * @return the $caseLimit
     */
    public function getCaseLimit ()
    {
        return $this->caseLimit;
    }

	/**
     * @return the $superiorid
     */
    public function getSuperiorid ()
    {
        return $this->superiorid;
    }

	/**
     * @return the $changeRates
     */
    public function getChangeRates ()
    {
        return $this->changeRates;
    }

	/**
     * @return the $groupSchemesUser
     */
    public function getGroupSchemesUser ()
    {
        return $this->groupSchemesUser;
    }

	/**
     * @return the $piUser
     */
    public function getPiUser ()
    {
        return $this->piUser;
    }

	/**
     * @return the $hasleft
     */
    public function getHasleft ()
    {
        return $this->hasleft;
    }

	/**
     * @return the $useAgentAdmin
     */
    public function getUseAgentAdmin ()
    {
        return $this->useAgentAdmin;
    }

	/**
     * @return the $agentOnStop
     */
    public function getAgentOnStop ()
    {
        return $this->agentOnStop;
    }

	/**
     * @return the $premiumOverride
     */
    public function getPremiumOverride ()
    {
        return $this->premiumOverride;
    }

	/**
     * @return the $isCRM
     */
    public function getIsCRM ()
    {
        return $this->isCRM;
    }

	/**
     * @return the $isTeam
     */
    public function getIsTeam ()
    {
        return $this->isTeam;
    }

	/**
     * @return the $isTester
     */
    public function getIsTester ()
    {
        return $this->isTester;
    }

	/**
     * @return the $isWebDataCapturer
     */
    public function getIsWebDataCapturer ()
    {
        return $this->isWebDataCapturer;
    }

	/**
     * @return the $isCSUPA
     */
    public function getIsCSUPA ()
    {
        return $this->isCSUPA;
    }

	/**
     * @return the $isWebDataCapturerAdmin
     */
    public function getIsWebDataCapturerAdmin ()
    {
        return $this->isWebDataCapturerAdmin;
    }

	/**
     * @return the $pnpPassword
     */
    public function getPnpPassword ()
    {
        return $this->pnpPassword;
    }

	/**
     * @param $csuid the $csuid to set
     */
    public function setCsuid ($csuid)
    {
        $this->csuid = $csuid;
    }

	/**
     * @param $realname the $realname to set
     */
    public function setRealname ($realname)
    {
        $this->realname = $realname;
    }

	/**
     * @param $username the $username to set
     */
    public function setUsername ($username)
    {
        $this->username = $username;
    }

	/**
     * @param $password the $password to set
     */
    public function setPassword ($password)
    {
        $this->password = $password;
    }

	/**
     * @param $centre the $centre to set
     */
    public function setCentre ($centre)
    {
        $this->centre = $centre;
    }

	/**
     * @param $whitelabelID the $whitelabelID to set
     */
    public function setWhitelabelID ($whitelabelID)
    {
        $this->whitelabelID = $whitelabelID;
    }

	/**
     * @param $profileID the $profileID to set
     */
    public function setProfileID ($profileID)
    {
        $this->profileID = $profileID;
    }

	/**
     * @param $riskaddress the $riskaddress to set
     */
    public function setRiskaddress ($riskaddress)
    {
        $this->riskaddress = $riskaddress;
    }

	/**
     * @param $editsched the $editsched to set
     */
    public function setEditsched ($editsched)
    {
        $this->editsched = $editsched;
    }

	/**
     * @param $backpay the $backpay to set
     */
    public function setBackpay ($backpay)
    {
        $this->backpay = $backpay;
    }

	/**
     * @param $editDates the $editDates to set
     */
    public function setEditDates ($editDates)
    {
        $this->editDates = $editDates;
    }

	/**
     * @param $email the $email to set
     */
    public function setEmail ($email)
    {
        $this->email = $email;
    }

	/**
     * @param $directdial the $directdial to set
     */
    public function setDirectdial ($directdial)
    {
        $this->directdial = $directdial;
    }

	/**
     * @param $directfax the $directfax to set
     */
    public function setDirectfax ($directfax)
    {
        $this->directfax = $directfax;
    }

	/**
     * @param $adminFeeChanger the $adminFeeChanger to set
     */
    public function setAdminFeeChanger ($adminFeeChanger)
    {
        $this->adminFeeChanger = $adminFeeChanger;
    }

	/**
     * @param $switchNetGross the $switchNetGross to set
     */
    public function setSwitchNetGross ($switchNetGross)
    {
        $this->switchNetGross = $switchNetGross;
    }

	/**
     * @param $cancelGS the $cancelGS to set
     */
    public function setCancelGS ($cancelGS)
    {
        $this->cancelGS = $cancelGS;
    }

	/**
     * @param $isAssessor the $isAssessor to set
     */
    public function setIsAssessor ($isAssessor)
    {
        $this->isAssessor = $isAssessor;
    }

	/**
     * @param $isDataEnterer the $isDataEnterer to set
     */
    public function setIsDataEnterer ($isDataEnterer)
    {
        $this->isDataEnterer = $isDataEnterer;
    }

	/**
     * @param $isSupervisor the $isSupervisor to set
     */
    public function setIsSupervisor ($isSupervisor)
    {
        $this->isSupervisor = $isSupervisor;
    }

	/**
     * @param $isManager the $isManager to set
     */
    public function setIsManager ($isManager)
    {
        $this->isManager = $isManager;
    }

	/**
     * @param $isAccounts the $isAccounts to set
     */
    public function setIsAccounts ($isAccounts)
    {
        $this->isAccounts = $isAccounts;
    }

	/**
     * @param $caseLimit the $caseLimit to set
     */
    public function setCaseLimit ($caseLimit)
    {
        $this->caseLimit = $caseLimit;
    }

	/**
     * @param $superiorid the $superiorid to set
     */
    public function setSuperiorid ($superiorid)
    {
        $this->superiorid = $superiorid;
    }

	/**
     * @param $changeRates the $changeRates to set
     */
    public function setChangeRates ($changeRates)
    {
        $this->changeRates = $changeRates;
    }

	/**
     * @param $groupSchemesUser the $groupSchemesUser to set
     */
    public function setGroupSchemesUser ($groupSchemesUser)
    {
        $this->groupSchemesUser = $groupSchemesUser;
    }

	/**
     * @param $piUser the $piUser to set
     */
    public function setPiUser ($piUser)
    {
        $this->piUser = $piUser;
    }

	/**
     * @param $hasleft the $hasleft to set
     */
    public function setHasleft ($hasleft)
    {
        $this->hasleft = $hasleft;
    }

	/**
     * @param $useAgentAdmin the $useAgentAdmin to set
     */
    public function setUseAgentAdmin ($useAgentAdmin)
    {
        $this->useAgentAdmin = $useAgentAdmin;
    }

	/**
     * @param $agentOnStop the $agentOnStop to set
     */
    public function setAgentOnStop ($agentOnStop)
    {
        $this->agentOnStop = $agentOnStop;
    }

	/**
     * @param $premiumOverride the $premiumOverride to set
     */
    public function setPremiumOverride ($premiumOverride)
    {
        $this->premiumOverride = $premiumOverride;
    }

	/**
     * @param $isCRM the $isCRM to set
     */
    public function setIsCRM ($isCRM)
    {
        $this->isCRM = $isCRM;
    }

	/**
     * @param $isTeam the $isTeam to set
     */
    public function setIsTeam ($isTeam)
    {
        $this->isTeam = $isTeam;
    }

	/**
     * @param $isTester the $isTester to set
     */
    public function setIsTester ($isTester)
    {
        $this->isTester = $isTester;
    }

	/**
     * @param $isWebDataCapturer the $isWebDataCapturer to set
     */
    public function setIsWebDataCapturer ($isWebDataCapturer)
    {
        $this->isWebDataCapturer = $isWebDataCapturer;
    }

	/**
     * @param $isCSUPA the $isCSUPA to set
     */
    public function setIsCSUPA ($isCSUPA)
    {
        $this->isCSUPA = $isCSUPA;
    }

	/**
     * @param $isWebDataCapturerAdmin the $isWebDataCapturerAdmin to set
     */
    public function setIsWebDataCapturerAdmin (
    $isWebDataCapturerAdmin)
    {
        $this->isWebDataCapturerAdmin = $isWebDataCapturerAdmin;
    }

	/**
     * @param $pnpPassword the $pnpPassword to set
     */
    public function setPnpPassword ($pnpPassword)
    {
        $this->pnpPassword = $pnpPassword;
    }

    
    
}