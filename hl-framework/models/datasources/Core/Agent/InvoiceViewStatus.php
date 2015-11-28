<?php

class Datasource_Core_Agent_InvoiceViewStatus extends Zend_Db_Table_Multidb
{
    protected $_name = 'InvoiceViewStatus';
    protected $_primary = array('agentSchemeNumber');
    protected $_multidb = 'db_legacy_homelet';


    public function insertInvoiceViewStatus ($agentSchemeNumber, $agent_id, $agent_name, $month, $year) {

        // Select last day of the reporting month
        $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        try {
               $data = array(
                    'agentSchemeNumber' => $agentSchemeNumber,
                    'invoiceDate' => $year . '-' . $month . '-' . $day,
                    'invoiceViewed' => 1,
                    'invoiceViewedAt' => new Zend_Db_Expr('NOW()'),
                    'invoiceViewedById' => $agent_id,
                    'invoiceViewedByName' => $agent_name
                );

            // Insert the data into a new row in the table
            if ($this->insert($data)) {
                return true;
            } else {
                // Failed insertion
                Application_Core_Logger::log("Can't insert into table {$this->_name} (AGENTSCHEMENUMBER = {$agentSchemeNumber})", 'error');
                return false;
            }

        } catch (Exception $e) {
            throw new Zend_Exception('insertInvoiceEmailStatus - Couldn\'t insert...: ' . $e->getMessage());
        }
    }

}
