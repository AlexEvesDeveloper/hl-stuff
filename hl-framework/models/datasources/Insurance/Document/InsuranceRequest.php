<?php

final class Datasource_Insurance_Document_InsuranceRequest extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'doc_request_queue';
    protected $_primary = 'request_id';
    
    /**
     * Creates the necessary request record in the queue table.
     *
     * @param string $policynumber Policy number
     * @param string $templateid Template Id number
     * @param int $csuid Csu ID number
     * @param integer $deliverymethodid Delivery method ID number
     * @param string $deliverytarget Delivery target
     * @param string $requesthash Request hash
     * @param string $xml XML string
     * @return integer|bool Request ID number or false on failure
     */
    public function storeRequest($policynumber, $templateid, $csuid, $deliverymethodid, $deliverytarget, $requesthash, $xml)
    {
        $data = array
        (
            'policy_number'         => $policynumber,
            'template_id'           => $templateid,
            'delivery_method_id'    => $deliverymethodid,
            'delivery_target'       => $deliverytarget,
            'request_hash'          => $requesthash,
            'request_xml'           => $xml,
            'queue_datetime'        =>  new Zend_Db_Expr('NOW()'),
            'csuid'                 => $csuid,
        );
        
        return $this->insert($data);
    }
    
    /**
     * Retrieve a previously stored queue record
     *
     * @param string $uniquerequestid Request hash
     * @return array Queue record data array
     */
    public function retrieveRequest($uniquerequestid)
    {
        // Select all fields from the queue table for a particular request hash
        $queueselect = $this->select()->where('request_hash = ?', $uniquerequestid);
        return $this->fetchRow($queueselect);
    }
    
    /**
     * Retrieve all revious stored queue records older than requested age in seconds
     *
     * @param int $age Age of records in seconds
     * @return array Array of record arrays
     */
    public function retrieveNextRequest($offset, $age = 0)
    {
        // Return all queued documents older than $age seconds
        $queueselect = $this->select()->where('queue_datetime <= ?', new Zend_Db_Expr("DATE_SUB(NOW(), INTERVAL $age SECOND)"))->limit(1, $offset);
        $rows = $this->fetchAll($queueselect);
        return $rows[0];
    }
    
    /**
     * Delete a previously stored queue record
     *
     * @param string $uniquerequestid Request hash
     * @return bool Returns true, or throws an exception on Db errors
     */
    public function deleteRequest($uniquerequestid)
    {
        // Delete the old record from queue table
        $queuedelete = $this->getAdapter()->quoteInto('request_hash = ?', $uniquerequestid);
        $this->delete($queuedelete);
        return true;
    }
}
