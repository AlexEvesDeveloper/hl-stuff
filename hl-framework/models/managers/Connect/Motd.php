<?php

/**
 * Manager class responsible for implementing MotD-related logic, and for
 * binding together the internal blog domain objects and datasources.  Provides
 * local equivalent to Ben's MotdManager class in legacy PHP4 Connect.
 *
 * @uses Service_Connect_MotdAccessor
 *
 * @category   Manager
 * @package    Manager_Connect
 * @subpackage Motd
 */
class Manager_Connect_Motd {

    private $_params;
    private $_motdClient;
    private $_motdAcceptanceLoggerDatasource;
    private $_currentMotd;
    private $_allActiveMotds;

    public function __construct() {

        $this->_params = Zend_Registry::get('params');

        $this->_motdClient = new Service_Connect_MotdAccessor();
        $this->_motdAcceptanceLoggerDatasource = new Datasource_Cms_Connect_MotdAcceptanceLogger();

        $this->_allActiveMotds = array();
        $result = $this->_motdClient->getActiveMotds();
        if ($result != null) {

            for ($i = 0; $i < count($result); $i++) {

                $this->_allActiveMotds[] = new Model_Cms_Connect_Motd(
                    $result[$i]['id'],
                    $result[$i]['motdTitle'],
                    $result[$i]['message'],
                    $result[$i]['displayWidth'],
                    $result[$i]['agentTypes'],
                    $result[$i]['agentUserTypes']
                );
            }
        }
    }

    /**
     * Returns an active MOTD that is applicable to the agent user.
     *
     * If no MOTDs are applicable to the agent user, then this method will
     * return null.
     *
     * @param integer $agentId
     * Identifies the agent user.
     *
     * @param integer $agentSchemeNumber
     * Identifies the agent user's scheme number.
     *
     * @return mixed
     * Returns a MOTD, if an applicable one can be found from the list of active
     * MOTDs. Otherwise will return null.
     */
    function getMotd($agentId, $agentSchemeNumber) {

        // First identify if any active MOTDs
        if(empty($this->_allActiveMotds)) {

            return null;
        }

        // Determine the agent user type (basic or master)
        $agentUser = new Manager_Core_Agent_User($agentId);
        $agentUserType = $agentUser->getUserRole();

        // Determine the agent type (standard, premier or premier-plus)
        $agentManager = new Manager_Core_Agent($agentSchemeNumber);
        $agent = $agentManager->getAgent();
        $agentType = $agent->premierStatus;

        $isMotdRequired = false;
        foreach($this->_allActiveMotds as $currentMotd) {

            //Identify if the MOTD applies to the agent user type.
            $agentUserTypesList = $currentMotd->getAgentUserTypes();
            $agentUserTypes = explode(',', $agentUserTypesList);
            if(!in_array($agentUserType, $agentUserTypes)) {

                continue;
            }


            //Identify if the MOTD applies to the agent type.
            $agentTypesList = $currentMotd->getAgentTypes();
            $agentTypes = explode(',', $agentTypesList);
            if(!in_array($agentType, $agentTypes)) {

                continue;
            }

            //Identify if the agent user has viewed the MOTD already.
            if ($this->_motdAcceptanceLoggerDatasource->checkMotdAccepted($currentMotd->getId(), $agentId)) {

                continue;
            }

            //If here then the _currentMotd should be displayed to the user.
            $isMotdRequired = true;
            break;
        }

        if($isMotdRequired) {

            $returnVal = $currentMotd;
        }
        else {

            $returnVal = null;
        }

        return $returnVal;
    }

    public function markMotdAccepted($motdId, $agentId) {

        return $this->_motdAcceptanceLoggerDatasource->markMotdAccepted($motdId, $agentId);
    }
}