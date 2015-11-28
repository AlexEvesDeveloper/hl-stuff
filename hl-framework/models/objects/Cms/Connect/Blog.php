<?php

/**
 * Represents a Connect blog entry.
 * Provides local equivalent to Ben's Blog class in legacy PHP4 Connect.
 *
 * @category   Model
 * @package    Model_Cms
 * @subpackage Blog
 */
class Model_Cms_Connect_Blog extends Model_Abstract {

    private $_id;
    private $_title;
    private $_content;
    private $_summary;
    private $_icon;
    private $_lastUpdatedDate;

    public function __construct($id, $title, $content, $summary, $icon, $lastUpdated) {

        $this->_id = $id;
        $this->_title = $title;
        $this->_content = html_entity_decode($content);
        $this->_summary = $summary;
        $this->_icon = $icon;
        $this->_lastUpdatedDate = $lastUpdated;
    }

    public function getId() {

        return $this->_id;
    }

    public function getTitle() {

        return $this->_title;
    }

    public function getContent() {

        return $this->_content;
    }

    public function getSummary() {

        return $this->_summary;
    }

    public function getIcon() {

        return $this->_icon;
    }

    public function getLastUpdatedDate() {

        return $this->_lastUpdatedDate;
    }
}