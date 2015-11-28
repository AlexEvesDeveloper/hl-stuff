<?php

final class Datasource_Insurance_Document_InsuranceRequestHistory extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'doc_request_history';
    protected $_primary = 'request_id';
    
    /**
     * Store the queue record to the history table. Assumes the queue record
     * has previously been retrieved.
     *
     * @param array $queuedata Queue record data array
     * @return int History record Id
     */
    public function storeQueueRecord($queuedata)
    {
        // Check if the data is an instance of Zend_Db_Table_Row_Abstract
        // and convert to array for moving to the history table.
        if ($queuedata instanceof Zend_Db_Table_Row_Abstract)
            $queuedata = $queuedata->toArray();
        
        // Prepare any previous queue table data to insert into the history able
        unset($queuedata['request_id']);
        unset($queuedata['queue_datetime']);
        $queuedata['send_datetime'] = new Zend_Db_Expr('NOW()');
        
        return $this->insert($queuedata);
    }
}
