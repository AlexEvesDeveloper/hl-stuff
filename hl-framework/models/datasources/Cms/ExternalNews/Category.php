<?php

/**
 * Datasource definition for external news categories.
 *
 * @category   Datasource
 * @package    Datasource_Cms
 * @subpackage ExternalNews
 *
 * @todo Add some error-trapping
 */

class Datasource_Cms_ExternalNews_Category extends Zend_Db_Table_Multidb {
    protected $_name = 'NewsCategory';
    protected $_primary = 'id';
    protected $_multidb = 'db_legacy_homelet';

    private $_params;

    public function __construct() {

        $params = Zend_Registry::get('params');
        $this->_params = $params->cms->extnews;

        parent::__construct();
    }

    /**
     * Get a list of all external news categories.
     *
     * @return array Array of (mostly) populated Model_Cms_ExternalNews_Category objects.
     */
    public function getAllCategories() {

        /*
         * SELECT *
         *   FROM `homeletuk_com`.`NewsCategory`;
         */
        $select = $this->select();

        $newsCategories = $this->fetchAll($select);
        $returnVal = array();
        foreach ($newsCategories as $newsCategoryRow) {
            $category = new Model_Cms_ExternalNews_Category();
            $category->id           = $newsCategoryRow->id;
            $category->name         = $newsCategoryRow->name;
            $category->added        = new Zend_Date($newsCategoryRow->added);
            $category->updated      = new Zend_Date($newsCategoryRow->updated);
            $category->permanent    = ($newsCategoryRow->permanent == 'yes') ? true : false;

            // $category->items and
            // $category->source are not populated here.

            $category->sourceId     = $newsCategoryRow->id_ns; // Redundant if $category->source is populated.

            $returnVal[$category->id] = $category;
        }

        return $returnVal;
    }

    /**
     * Add a new news category, derived from a news source.
     *
     * @param string $categoryName
     * @param int $sourceId
     * @param bool $permanent
     */
    public function addCategory($categoryName, $sourceId, $permanent = false) {

        /*
         * INSERT INTO `homeletuk_com`.`NewsCategory` SET
         *   `id_ns` = $sourceId, `name` = '$categoryName', `permanent` = '$permanent', `added` = NOW();
         */
        $data = array(
            'id_ns'     => $sourceId,
            'name'      => $categoryName,
            'permanent' => $permanent ? 'yes' : 'no',
            'added'     => new Zend_Db_Expr('NOW()')
        );

        return $this->insert($data);
    }

    /**
     * Mark a category's 'updated' field as NOW().
     *
     * @param int $categoryId
     *
     * @return void
     */
    public function markCategoryUpdated($categoryId) {
        $data = array(
            'updated' => new Zend_Db_Expr('NOW()')
        );
        $where = $this->getAdapter()->quoteInto('id = ?', $categoryId);
        return $this->update($data, $where);
    }

    /**
     * Get list of non-permanent categories that haven't been updated in a while.
     *
     * @return array Array of category IDs.
     */
    public function getPrunable() {
        /*
         * SELECT id FROM `NewsCategory`
         *   WHERE updated <= DATE_SUB(NOW(), INTERVAL $this->_params->categoryLifetime MINUTE) AND permanent = 'no';
         */
        $where = $this->getAdapter()->quoteInto('updated <= DATE_SUB(NOW(), INTERVAL ? MINUTE) AND permanent = \'no\'', $this->_params->categoryLifetime);
        $select = $this->select()
            ->from(
                array('nc' => $this->_name),
                array('id')
            )
            ->where($where);

        $newsCategories = $this->fetchAll($select);
        $returnVal = array();
        foreach ($newsCategories as $newsCategoryRow) {
            $returnVal[] = $newsCategoryRow->id;
        }

        return $returnVal;
    }

    /**
     * Prune old news categories from DB.
     */
    public function prune() {

        /*
         * DELETE FROM `NewsCategory`
         *   WHERE updated <= DATE_SUB(NOW(), INTERVAL $this->_params->categoryLifetime MINUTE) AND permanent = 'no';
         */
        $where = $this->getAdapter()->quoteInto('updated <= DATE_SUB(NOW(), INTERVAL ? MINUTE) AND permanent = \'no\'', $this->_params->categoryLifetime);
        return $this->delete($where);
    }
}