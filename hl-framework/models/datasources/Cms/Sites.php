<?php

/**
* Phil-style model definition for the CMS sites table
*
*/

class Datasource_Cms_Sites extends Zend_Db_Table_Multidb {
    protected $_name = 'sites';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';

    public function getByHandle($siteHandle) {
        $select = $this->select();
        $select->where('handle = ?', $siteHandle);
        $row = $this->fetchRow($select);

        if (count($row) > 0) {
            return $row;
        } else {
            return null;
        }
    }

}