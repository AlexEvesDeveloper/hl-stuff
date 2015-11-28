<?php

/**
 * Maps external news categories to agent users.
 *
 * Other maps can link external news categories to other types of users.
 *
 * @category   Datasource
 * @package    Datasource_Cms
 * @subpackage ExternalNews
 */

class Datasource_Cms_ExternalNews_CategoriesAgentsMap extends Zend_Db_Table_Multidb {
    protected $_name = 'NewsCategoryAgent';
    protected $_primary = 'id';
    protected $_multidb = 'db_legacy_homelet';

    /**
     * Fetch the list of external news categories an agent is subscribing to.
     *
     * @param int $userId An agent user's unique ID.
     *
     * @return array Array of (mostly) populated Model_Cms_ExternalNews_Category objects.
     */
    public function getNewsPreferences($userId) {

        /*
         * SELECT `ns`.`id` AS `ns_id`,
         *  `ns`.`name` AS `ns_name`,
         *  `nc`.`id` AS `nc_id`,
         *  `nc`.`name` AS `nc_name`,
         *  `nca`.`id` AS `nca_id`
         *  FROM `NewsSource` AS `ns`
         *  LEFT JOIN `NewsCategory` AS `nc` ON `ns`.`id` = `nc`.`id_ns`
         *  LEFT JOIN `NewsCategoryAgent` AS `nca` ON `nc`.`id` = `nca`.`id_nc`
         *  AND `nca`.`id_ai` = $userId
         *  WHERE `nca`.`id` IS NOT NULL
         *  ORDER BY `ns`.`name` ASC, `nc`.`name` ASC;
         */
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('ns' => 'NewsSource'),
                array('id AS ns_id', 'name AS ns_name')
            )
            ->joinLeft(
                array('nc' => 'NewsCategory'),
                'ns.id = nc.id_ns',
                array('id AS nc_id', 'name AS nc_name')
            )
            ->joinLeft(
                array('nca' => 'NewsCategoryAgent'),
                'nc.id = nca.id_nc AND nca.id_ai = ' . $userId,
                array('id AS nca_id')
            )
            ->where('nca.id IS NOT NULL')
            ->order('ns.name ASC')
            ->order('nc.name ASC');

        $newsPrefs = $this->fetchAll($select);

        $returnVal = array();
        foreach ($newsPrefs as $newsPrefsRow) {
            $category = new Model_Cms_ExternalNews_Category();
            $category->id           = $newsPrefsRow->nc_id;
            $category->name         = $newsPrefsRow->nc_name;
            $category->source       = $newsPrefsRow->ns_name;
            $category->sourceId     = $newsPrefsRow->ns_id; // Redundant if $category->source is populated.

            $returnVal[$category->id] = $category;
        }

        return $returnVal;
    }

    /**
     * Set the news category preferences for an agent user.
     *
     * @param array $newsPrefs Array of news category IDs.
     * @param int $userId An agent user's unique ID.
     *
     * @return void
     */
    public function setNewsPreferences($newsPrefs, $userId) {

        // Delete all old news prefs for this user
        $where = $this->getAdapter()->quoteInto('id_ai = ?', $userId);
        $delete = $this->delete($where);

        // Set new news prefs
        foreach($newsPrefs as $newsCategoryId) {
            $insert = $this->insert(
                array(
                    'id_nc' => $newsCategoryId,
                    'id_ai' => $userId
                )
            );
        }
    }

    /**
     * Remove links between agent users and specific categories.
     *
     * @param array $unusedCategories List of category IDs to prune mappings by.
     *
     * @return void
     */
    public function pruneByCategories($unusedCategoryIds) {
        if (count($unusedCategoryIds) > 0) {

            $unusedCategories = implode(', ', $unusedCategoryIds);
            /* DELETE FROM `NewsCategoryAgent`
             *   WHERE `id_nc` IN ($unusedCategories);
             */
            $where = $this->getAdapter()->quoteInto("`id_nc` IN ({$unusedCategories})", '');
            return $this->delete($where);
        }
    }

}