<?php

/**
 * Datasource definition for Deed of Guarantee log.
 *
 * @category   Datasource
 * @package    Datasource_Connect
 * @subpackage Doglog
 */

class Datasource_Connect_Doglog extends Zend_Db_Table_Multidb {
    protected $_name = 'doglog';
    protected $_primary = 'dl_id';
    protected $_multidb = 'db_legacy_homelet';

    /**
     * Push an agent user's ID into the Deed of Guarantee log.
     *
     * @param mixed $userId Agent user's unique ID in the legacy DB.
     *
     * @return mixed
     */
    public function logPush($userId) {

        $data = array(
            'dl_agentid'    => $userId,
            'dl_access'     => new Zend_Db_Expr('NOW()')
        );

        return $this->insert($data);
    }
}
?>