<?php

/**
* Model definition for the TatInvitation datasource.
*/
class Datasource_Referencing_TatInvitation extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'tat_invitation';
    protected $_primary = 'id';
    /**#@-*/
    
    
    /**
     * Inserts a Tat invitation flag into the datasource.
     *
     * @param string $enquiryId
     * The unique Enquiry identifier (external).
     *
     * @return void
     */
    public function insertInvitation($enquiryId) {
        
        $dateSent = Zend_Date::now();
        $dateSent = $dateSent->toString(Zend_Date::ISO_8601);
        $data = array(
            'refno' => $enquiryId,
            'date_sent' => $dateSent
        );

        $this->insert($data);
    }
    
    
    /**
     * Indicates if the initial TAT invitation to the reference subject has been sent.
     *
     * Can be used by calling code to prevent multiple invites being sent.
     *
     * @param string $enquiryId
     * The unique Enquiry identifier (external).
     *
     * @return boolean
     * True if the TAT invitation has been sent, false otherwise.
     */
    public function getIsTatInvitationSent($enquiryId) {
        
        $select = $this->select();
        $select->where('refno = ?', $enquiryId);
        $tatInvitationRow = $this->fetchRow($select);
        
        if(empty($tatInvitationRow)) {

            $returnVal = false;
        }
        else {
            
            $returnVal = true;
        }
        
        return $returnVal;
    }
}

?>