<?php

class Datasource_Core_Agent_InvoiceEmailStatus extends Zend_Db_Table_Multidb
{
    protected $_name = 'InvoiceEmailStatus';
    protected $_primary = array('agentSchemeNumber');
    protected $_multidb = 'db_legacy_homelet';


    public function insertInvoiceEmailStatus ($agentSchemeNumber, $month, $year) {

        // Select last day of the reporting month
        $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        try {
               $data = array(
                    'agentSchemeNumber' => $agentSchemeNumber,
                    'invoiceDate' => $year . '-' . $month . '-' . $day,
                    'emailSent' => 1,
                    'emailSentAt' => new Zend_Db_Expr('NOW()')
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

    public function updateInvoiceEmailStatus ($agentSchemeNumber) {

        try {
                $update_data = array(
                    'emailSent' => 1,
                    'emailSentAt' => new Zend_Db_Expr('NOW()')
                );

            $where = $this->quoteInto('agentSchemeNumber = ?', $agentSchemeNumber);

            // Insert the data into a new row in the table
            if ($this->update($update_data, $where)) {
                return true;
            }
            // Failed update
            Application_Core_Logger::log("Can't update table {$this->_name} (AGENTSCHEMENUMBER = {$agentSchemeNumber})", 'error');
            return false;

        } catch (Exception $e) {
            throw new Zend_Exception('updateInvoiceEmailStatus - Couldn\'t update...: ' . $e->getMessage());
        }
    }

}
