<?php

/**
* Model definition for the page meta expando table.
*/

class Datasource_Cms_Page_MetaExpando extends Zend_Db_Table_Multidb {

    protected $_name = 'page_template_meta_expando';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';



    /**
     * Fetches any meta expando data for a page.
     *
     * @param int $pageId
     *
     * @return array
     */
    public function fetch($pageId) {

        // SELECT `mf`.`meta_name`, `me`.* FROM `page_template_meta_expando` AS `me`
        //   LEFT JOIN `page_template_meta` AS `m` ON `me`.`meta_content_id` = `m`.`id`
        //   LEFT JOIN `page_template_meta_fields` AS `mf` ON `m`.`meta_field_id` = `mf`.`id`
        //   LEFT JOIN `page_templates` AS `pt` ON `mf`.`page_template_id` = `pt`.`id`
        //   LEFT JOIN `pages` AS `p` ON `pt`.`id` = `p`.`template_id`
        //   WHERE `m`.`page_id` = {$pageId}
        //   AND `p`.`id` = {$pageId}
        //   ORDER BY `me`.`ordinal`;

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('me' => 'page_template_meta_expando')
            )
            ->joinLeft(
                array('m' => 'page_template_meta'),
                'me.meta_content_id = m.id',
                array()
            )
            ->joinLeft(
                array('mf' => 'page_template_meta_fields'),
                'm.meta_field_id = mf.id',
                array('mf.meta_name')
            )
            ->joinLeft(
                array('pt' => 'page_templates'),
                'mf.page_template_id = pt.id',
                array()
            )
            ->joinLeft(
                array('p' => 'pages'),
                'pt.id = p.template_id',
                array()
            )
            ->where('m.page_id = ?', $pageId)
            ->where('p.id = ?', $pageId)
            ->order('me.ordinal');

        $rows = $this->fetchAll($select);

        $rawMetaExpandoData = $rows->toArray();

        // Now massage data into deeper name-based arrays for the benefit of no
        //   manager model, no domain objects and a fat controller
        $metaExpandoData = array();
        foreach($rawMetaExpandoData as $data) {
            $data['content'] = unserialize($data['content']);
            $metaExpandoData[$data['meta_name']][] = $data;
        }

        return $metaExpandoData;
    }



    /**
     * Takes a page ID and some raw form data, deletes all the page's current
     * meta expando data, and then saves any matching meta expando data present
     * in the raw form data.
     *
     * @param int $pageId
     * @param array $rawFormData
     *
     * @return void
     */
    public function save($pageId, $rawFormData) {

        // Firstly delete all meta expando data associated with this page
        //$this->delete($pageId);

        // Identify meta data on this page that allows expando data
        // TODO: This should use the meta data datasource - once that datasource
        //   has some methods...
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('m' => 'page_template_meta'),
                array('m.id')
            )
            ->joinLeft(
                array('mf' => 'page_template_meta_fields'),
                'm.meta_field_id = mf.id',
                array('mf.meta_type')
            )
            ->joinLeft(
                array('pt' => 'page_templates'),
                'mf.page_template_id = pt.id',
                array()
            )
            ->joinLeft(
                array('p' => 'pages'),
                'pt.id = p.template_id',
                array()
            )
            ->where('m.page_id = ?', $pageId)
            ->where('p.id = ?', $pageId);
        $data = $this->fetchAll($select);
        $metaFieldArray = $data->toArray();

        foreach($metaFieldArray as $metaField) {
print_r($metaField);
            switch($metaField['meta_type']) {
                case 'icon_links':
                    break;
            }
        }
//exit();
    }



    public function delete($pageId) {

        // DELETE FROM `page_template_meta_expando` AS `me` WHERE `me`.`meta_content_id` IN
        //   (SELECT `m`.`id` FROM `page_template_meta` AS `m`
        //   LEFT JOIN `page_template_meta_fields` AS `mf` ON `m`.`meta_field_id` = `mf`.`id`
        //   LEFT JOIN `page_templates` AS `pt` ON `mf`.`page_template_id` = `pt`.`id`
        //   LEFT JOIN `pages` AS `p` ON `pt`.`id` = `p`.`template_id`
        //   WHERE `m`.`page_id` = {$pageId}
        //   AND `p`.`id` = {$pageId});

        $subSelect = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('m' => 'page_template_meta'),
                array('m.id')
            )
            ->joinLeft(
                array('mf' => 'page_template_meta_fields'),
                'm.meta_field_id = mf.id',
                array()
            )
            ->joinLeft(
                array('pt' => 'page_templates'),
                'mf.page_template_id = pt.id',
                array()
            )
            ->joinLeft(
                array('p' => 'pages'),
                'pt.id = p.template_id',
                array()
            )
            ->where('m.page_id = ?', $pageId)
            ->where('p.id = ?', $pageId);
        $delete = $this->delete("meta_content_id IN ({$subSelect})");
    }

}