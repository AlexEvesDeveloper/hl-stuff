<?php

class Datasource_Core_Agent_Invoice extends Zend_Db_Table_Multidb
{
    protected $_name = 'invoiceHashLookup';
    protected $_primary = array('filename', 'agentSchemeNo', 'invoiceDate', 'revision');
    protected $_multidb = 'db_legacy_homelet';


    /**
     * Return the details of all agents have been already invoiced
     * on the month_year required
     *
     * @param int $date mm_yyyy required
     *
     * @return rowSet | null
     */
    public function getAgentDetailsForInvoicing($date) {

        try {
            $select =  $this->select(array('invoiceDate' => 'invoiceHashLookup.invoiceDate'))
                ->setIntegrityCheck(false)
                ->from(array('a' => 'newagents' ),array(new Zend_Db_Expr('IF(e1.id is null,e2.email_address,e1.email_address) as email_address'), 's.id', 's.emailSent', 'a.invoicesend', 'a.name'))
                #   ->from(array('p' => 'agentPaySchedule'),array('invoiceDate',new Zend_Db_Expr('IF(e1.id is null,e2.email_address,e1.email_address) as email_address')))
                ->joinLeft(array('e1' => 'agent_emails'), 'a.agentschemeno = e1.scheme_number and e1.category_id=5', array())
                ->joinLeft(array('e2' => 'agent_emails'), 'a.agentschemeno = e2.scheme_number and e2.category_id=1', array())
                ->joinLeft(array('s' => 'InvoiceEmailStatus'), 'invoiceHashLookup.agentSchemeNo = s.agentSchemeNumber and DATE_FORMAT(s.InvoiceDate, "%m_%Y")=invoiceHashLookup.invoiceDate', array())
                ->where('invoiceHashLookup.invoiceDate = ?', $date)
                ->where('a.agentschemeno = invoiceHashLookup.agentSchemeNo')
                ->group('invoiceHashLookup.agentSchemeNo');

            $rowSet = $this->fetchAll($select);

            if (count($rowSet) > 0) {
                return $rowSet;
            } else {
                return null;
            }

        } catch (Exception $e) {
            throw new Zend_Exception('getAgentDetailsForInvoicing - Couldn\'t select agent details...: ' . $e->getMessage());
        }
    }

    /**
     * Return the invoice pdf filename (from homeletuk_com.invoiceHashLookup table) for the given agentSchemeNumber
     * on the month/year required
     *
     * @param int $agentschemeno Agent scheme number
     * @param int $month Month required
     * @param int $year Year required
     * @return row|null Filename of invoice on fileserver
     */
    public function getInvoiceFilename($agentschemeno, $month, $year)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from
        (
            array('e' => $this->_name),
            array('filename')
        );

        $select->where('agentSchemeNo = ?', $agentschemeno);
        $select->where('invoiceDate = ?', $month . '_' . $year);
        $select->order('revision DESC');
        $row = $this->fetchRow($select);

        if (isset($row['filename']))
            return $row['filename'];

        return null;
    }

}
