<?php

/**
* Model definition for the user_notices datasource.
*/
class Datasource_Referencing_UserNotices extends Zend_Db_Table_Multidb {    
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'user_notices';
    protected $_primary = 'id';
    /**#@-*/
    
    
    /**
     * Returns a user notice.
     *
     * @param integer $userNoticeId
     * Indicates which user notice to return.
     *
     * @return mixed
     * Returns a string user notice, or null if the user notice cannot be found.
     */
    public function getNotice($userNoticeId) {
        
        $select = $this->select();
        $select->where('id = ?', $userNoticeId);
        $noticeRow = $this->fetchRow($select);
        
        $notice = null;
        if(!empty($noticeRow)) {

            $notice = $noticeRow->notice;
        }

        return $notice;
    }
}

?>