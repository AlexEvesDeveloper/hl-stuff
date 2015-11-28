<?php

/**
* Model definition for the UserNoticeMap datasource.
*/
class Datasource_Referencing_UserNoticeMap extends Zend_Db_Table_Multidb {    
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'user_notice_map';
    protected $_primary = 'id';
    /**#@-*/
    
    
	/**
	 * Returns the latest user notice flagged against the reference, if any.
	 * 
	 * @param integer $referenceId
	 * The unique internal Reference identifer.
	 *
	 * @return mixed
	 * Returns the user notice as a string, or null if none found.
	 */
	public function getLatestNotice($referenceId) {
            
        $select = $this->select();
		$select->where('reference_id = ?', $referenceId);
        $select->order('id DESC');
        $select->limit(1, 0);
        $mapRow = $this->fetchRow($select);
        
        $notice = null;
        if(!empty($mapRow)) {

            $userNotices = new Datasource_Referencing_UserNotices();
            $notice = $userNotices->getNotice($mapRow->user_notice_id);
        }
        
        return $notice;
    }
    

	/**
	 * Inserts a user notice into the datasource.
	 *
	 * @param integer $noticeId
	 * The unique user notice identifier.
	 * 
	 * @param integer $referenceId
	 * The unique Reference (Enquiry) identifer.
	 *
	 * @return boolean
	 * True on success, false otherwise.
	 */
	public function insertNoticeMap($noticeId, $referenceId) {
    
		$data = array(
			'user_notice_id' => $noticeId,
			'reference_id' => $referenceId
			);
		
		$result = $this->insert($data);
		if(is_int($result)) {
			
			return true;
		}
		return false;
    }
}

?>