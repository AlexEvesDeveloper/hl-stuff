<?php
/**
* Model definition for the cms pages table
*
*/
class Datasource_Cms_Panels extends Zend_Db_Table_Multidb {
    protected $_name = 'panels';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';

    public function getAll() {
        $panels = $this->fetchAll($this->select());

        $return = array();
        foreach ($panels as $panel) {
            $return[] = array(
                'id'            =>  $panel->id,
                'key'           =>  $panel->key,
                'description'   =>  $panel->description,
                'content'       =>  $panel->content
            );
        }

        return $return;
    }

    public function getByID($id) {
        $select = $this->select()
                       ->where('id = ?', $id);
        $panel = $this->fetchRow($select);

        if (count($panel) > 0) {
            return array(
                'id'            =>  $panel->id,
                'key'           =>  $panel->key,
                'description'   =>  $panel->description,
                'content'       =>  $panel->content
            );
        } else {
            return null;
        }
    }

    public function getByKey($key) {
        $select = $this->select()
                       ->where('`key` = ?', $key);
        $panel = $this->fetchRow($select);

        if (count($panel) > 0) {
            return array(
                'id'            =>  $panel->id,
                'key'           =>  $panel->key,
                'description'   =>  $panel->description,
                'content'       =>  $panel->content
            );
        } else {
            // Can't find content for that key - log a warning
            Application_Core_Logger::log('Panel content not found in database (key = ' . $key . ')', 'warning');
            return false;
        }
    }

    public function saveChanges($id, $content) {
        $data = array(
            'id'            =>  $id,
            'content'       =>  $content
        );

        $where = $this->quoteInto('id = ?', $id);
        $this->update($data, $where);

    }
}
?>