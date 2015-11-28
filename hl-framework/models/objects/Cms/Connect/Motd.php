<?php

/**
 * Encapsulates the details of a single MotD.
 * Provides local equivalent to Ben's Motd class in legacy PHP4 Connect.
 *
 * @category   Model
 * @package    Model_Cms
 * @subpackage Motd
 */
class Model_Cms_Connect_Motd extends Model_Abstract {

    private $_id;
    private $_title;
    private $_message;
    private $_displayWidth;
    private $_agentTypes;
    private $_agentUserTypes;

    public function __construct($id, $title, $message, $displayWidth, $agentTypes, $agentUserTypes) {

        $this->_id = $id;
        $this->_title = $title;
        $this->_message = html_entity_decode($message);
        $this->_displayWidth = $displayWidth;
        $this->_agentTypes = $agentTypes;
        $this->_agentUserTypes = $agentUserTypes;
    }

    public function getId() {

        return $this->_id;
    }

    public function getTitle() {

        return $this->_title;
    }

    public function getMessage() {

        return $this->_message;
    }

    public function getDisplayWidth() {

        return $this->_displayWidth;
    }

    /**
     * Returns a comma-separated string holding integer values representing the
     * agent types this MOTD should be displayed to. Agent types are typically
     * 'standard', 'premier' and 'premier-plus'.
     *
     * @return string The agent types, in a comma-separated string.
     */
    public function getAgentTypes() {

        return $this->_agentTypes;
    }

    /**
     * Returns a comma-separated string holding integer values representing the
     * agent user types this MOTD should be displayed to. Agent user types are
     * typically 'basic', and 'master'.
     *
     * @return string The agent user types, in a comma-separated string.
     */
    public function getAgentUserTypes() {

        return $this->_agentUserTypes;
    }
}