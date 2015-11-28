<?php

/**
 * UserNotices manager class.
 */
class Manager_Referencing_UserNotices {	
	
	/**#@+
     * Internal datasource references.
     */
	protected $_userNoticeMapDatasource;
    /**#@-*/
    
    
    /**
     * Instantiates internal datasource references.
     */
    public function __construct() {
        
		$this->_userNoticeMapDatasource = new Datasource_Referencing_UserNoticeMap();
    }
	
	
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
		
		return $this->_userNoticeMapDatasource->getLatestNotice($referenceId);
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
		
		return $this->_userNoticeMapDatasource->insertNoticeMap($noticeId, $referenceId);
	}
}

?>