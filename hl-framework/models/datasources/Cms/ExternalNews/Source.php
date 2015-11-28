<?php

/**
 * Datasource definition for external news sources.
 *
 * @category   Datasource
 * @package    Datasource_Cms
 * @subpackage ExternalNews
 */

class Datasource_Cms_ExternalNews_Source extends Zend_Db_Table_Multidb {
    protected $_name = 'NewsSource';
    protected $_primary = 'id';
    protected $_multidb = 'db_legacy_homelet';

    private $_params;

    public function __construct() {

        $params = Zend_Registry::get('params');
        $this->_params = $params->cms->extnews;

        parent::__construct();
    }

    /**
     * Get a list of all external news sources.
     *
     * @return array Array of populated Model_Cms_ExternalNews_Source objects.
     */
    public function getAllSources() {

        /*
         * SELECT *
         *   FROM `homeletuk_com`.`NewsSource`;
         */
        $select = $this->select();

        $newsSources = $this->fetchAll($select);
        $returnVal = array();
        foreach ($newsSources as $newsSourceRow) {
            $source = new Model_Cms_ExternalNews_Source();
            $source->id                 = $newsSourceRow->id;
            $source->name               = $newsSourceRow->name;
            $source->defaultCategory    = $newsSourceRow->defaultCategory;
            $source->type               = $newsSourceRow->type;
            $source->updated            = $newsSourceRow->updated;
            $source->updateFrequency    = $newsSourceRow->updateFrequency;
            $source->sourceUrl          = $newsSourceRow->sourceUrl;
            $source->infoUrl            = $newsSourceRow->infoUrl;
            $source->description        = $newsSourceRow->description;
            $source->copyright          = $newsSourceRow->copyright;
            $source->imageUrl           = $newsSourceRow->imageUrl;

            $returnVal[$source->id] = $source;
        }

        return $returnVal;
    }

    /**
     * Get a random order list, often just a subset, of news sources that are due to be updated.
     *
     * @return array Array of populated Model_Cms_ExternalNews_Source objects.
     */
    public function getNewsSourcesToUpdate() {

        /*
         * SELECT *
         *   FROM `homeletuk_com`.`NewsSource`
         *   WHERE `type` = 'rss' AND `updated` <= DATE_SUB(NOW(), INTERVAL `updateFrequency` MINUTE)
         *   ORDER BY RAND()
         *   LIMIT $this->_params->updateSourcesMax;
         */
        $where = $this->getAdapter()->quoteInto('`type` = ? AND `updated` <= DATE_SUB(NOW(), INTERVAL `updateFrequency` MINUTE)', 'rss');
        $select = $this->select()
            ->where($where)
            ->order('RAND()')
            ->limit($this->_params->updateSourcesMax);

        $newsSources = $this->fetchAll($select);
        $returnVal = array();
        foreach ($newsSources as $newsSourceRow) {
            $source = new Model_Cms_ExternalNews_Source();
            $source->id                 = $newsSourceRow->id;
            $source->name               = $newsSourceRow->name;
            $source->defaultCategory    = $newsSourceRow->defaultCategory;
            $source->type               = $newsSourceRow->type;
            $source->updated            = new Zend_Date($newsSourceRow->updated);
            $source->updateFrequency    = $newsSourceRow->updateFrequency;
            $source->sourceUrl          = $newsSourceRow->sourceUrl;
            $source->infoUrl            = $newsSourceRow->infoUrl;
            $source->description        = $newsSourceRow->description;
            $source->copyright          = $newsSourceRow->copyright;
            $source->imageUrl           = $newsSourceRow->imageUrl;

            $returnVal[] = $source;
        }

        return $returnVal;
    }

    /**
     * Mark a source's 'updated' field as NOW().
     *
     * @param int $sourceId
     *
     * @return void
     */
    public function markSourceUpdated($sourceId) {
        $data = array(
            'updated' => new Zend_Db_Expr('NOW()')
        );
        $where = $this->getAdapter()->quoteInto('id = ?', $sourceId);
        return $this->update($data, $where);
    }

    /**
     * Mark all sources' 'updated' fields to not updated.
     *
     * Intended for testing purposes only.
     *
     * @return void
     */
    public function markAllSourcesNotUpdated() {
        $data = array(
            'updated' => '0000-00-00 00:00:00'
        );
        $where = '1';
        return $this->update($data, $where);
    }
}