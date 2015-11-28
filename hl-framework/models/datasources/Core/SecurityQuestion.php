<?php

class Datasource_Core_SecurityQuestion extends Zend_Db_Table_Multidb {
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_homelet';
    protected $_name = 'security_questions';
    protected $_primary = 'id';
    /**#@-*/

    public function getOptions() {
        $select = $this->select();
        return $this->fetchAll($select);
    }
}
