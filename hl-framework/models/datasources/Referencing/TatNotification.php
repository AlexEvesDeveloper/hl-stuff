<?php

/**
* Model definition for the TatNotification datasource.
*/
class Datasource_Referencing_TatNotification extends Zend_Db_Table_Multidb {
    
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'tat_notification';
    protected $_primary = 'id';
    /**#@-*/
    
    
    /**
     * Inserts a new Tat notification into the datasource.
     *
     * @param Model_Referencing_TatNotification
     * The tat notification to insert.
     *
     * @return void
     */
    public function insertNotification($tatNotification) {

        $data = array(
            'refno' => $tatNotification->enquiryId,
            'send_date' => $tatNotification->sendDate->toString(Zend_Date::ISO_8601),
            'content' => $tatNotification->content
        );

        $this->insert($data);
    }
    
    
    /**
     * Returns all the tat notifications associated with the Enquiry identifier.
     *
     * @param string $enquiryId
     * The external Enquiry identifier (ERN).
     *
     * @return mixed
     * Returns an array of corresponding Model_Referencing_TatNotification objects,
     * or null if no matches found.
     */
    public function getByEnquiry($enquiryId) {
        
        $select = $this->select();
        $select->where('refno = ?', $enquiryId);
        $select->order('send_date');
        $notificationArray = $this->fetchAll($select);
        
        if($notificationArray->count() == 0) {
            
            $returnVal = null;
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ':Unable to find tat notification.');
        }
        else {
            
            $returnArray = array();
            foreach($notificationArray as $notificationRow) {
                
                $tatNotification = new Model_Referencing_TatNotification();
                $tatNotification->id = $notificationRow->id;
                $tatNotification->enquiryId = $notificationRow->refno;
                $tatNotification->sendDate = new Zend_Date($notificationRow->send_date, Zend_Date::ISO_8601);
                $tatNotification->content = $notificationRow->content;
                
                $returnArray[] = $tatNotification;
            }
            
            $returnVal = $returnArray;
        }
        
        return $returnVal;
    }
}

?>