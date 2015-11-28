<?php

final class Datasource_Connect_Mi_RefSales extends Zend_Db_Table_Multidb
{
    /**#@+
     * Mandatory attributes
     */
    protected $_name = 'Enquiry';
    protected $_primary = 'policynumber';
    protected $_multidb = 'db_legacy_slave_referencing';
    /**#@-*/

    /**
     * Return the referencing sales count by month for the supplied agent scheme
     * number and year.
     *
     * @param int $agentschemeno Agent scheme number
     * @param int $year Year number
     * @return array Count statistics sorted by month/year
     */
    public function refSalesCountByMonthForYear($agentschemeno, $year)
    {
        $data = array();
        $months = array
        (
            'January-'      . $year  => 0,
            'February-'     . $year  => 0,
            'March-'        . $year  => 0,
            'April-'        . $year  => 0,
            'May-'          . $year  => 0,
            'June-'         . $year  => 0,
            'July-'         . $year  => 0,
            'August-'       . $year  => 0,
            'September-'    . $year  => 0,
            'October-'      . $year  => 0,
            'November-'     . $year  => 0,
            'December-'     . $year  => 0,
        );
        $select = $this->select();

        $select->from
        (
            array('e' => $this->_name),
            array
            (
                new Zend_Db_Expr('DATE_FORMAT(pg.firstfin_time, "%M-%Y") as month'),
                new Zend_Db_Expr('count(AgentID) as count')
            )
        );

        $select->joinInner
        (
            array('pg' => 'progress'),
            'e.RefNo = pg.refno',
            array()
        );

        $select->joinLeft
        (
            array('t' => 'Tenant'),
            'e.TenantID = t.ID',
            array()
        );

        $select->where('pg.firstfin_time >= ?', $year . '-01-01 00:00:00');
        $select->where('pg.firstfin_time <= ?', $year . '-12-31 23:59:59');

        $select->where('e.conclusion != "CANCELLED"');
        $select->where('e.PRODUCTID != "14"');
        $select->where('e.AgentID = ?', $agentschemeno);
        $select->group(new Zend_Db_Expr('DATE_FORMAT(pg.firstfin_time, "%m")'));

        $rowset = $this->fetchAll($select);

        // Copy rowset into data arrary
        foreach ($rowset as $row)
            $months[$row['month']] = $row['count'];

        // Turn associate array into list array
        foreach ($months as $month => $count)
            array_push($data, array($month, $count));

        return $data;
    }


    /**
     * Find all references sold for the supplied agent scheme no between the supplied date range
     *
     * @param int $agentschemeno Agent scheme number
     * @param Zend_Date $startdate Start date of range
     * @param Zend_Date $enddate End date of range
     *
     * @return Zend_Db_Table Database query result of references sold
     */
    public function refSalesForMonthYear($agentschemeno, $startdate, $enddate)
    {
        $startdate = $startdate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        $enddate = $enddate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);

        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from
        (
            array('e' => $this->_name),
            array
            (
                'e.RefNo',
                'e.Guarantor',
                'e.PolicyLength',
                'e.compmethod',
                'e.conclusion',
            )
        );

        $select->joinInner
        (
            array('pg' => 'progress'),
            'e.RefNo = pg.refno',
            array
            (
                'pg.result',
                new Zend_Db_Expr('DATE_FORMAT(pg.start_time,"%d/%m/%Y") as start_time')
            )
        );

        $select->joinLeft
        (
            array('t' => 'Tenant'),
            'e.TenantID = t.ID',
            array
            (
                't.title',
                't.firstname',
                't.lastname',
            )
        );

        $select->joinLeft
        (
            array('pt' => 'property'),
            'e.proprefno = pt.refno',
            array
            (
                'pt.address1',
                'pt.town',
            )
        );

        $select->joinLeft
        (
            array('pd' => 'Product'),
            'e.ProductID = pd.ID',
            array
            (
                'pd.Name'
            )
        );

        $select->where('e.ProductID != ""');
        $select->where('pg.start_time >= ?', $startdate);
        $select->where('pg.start_time <= ?', $enddate);
        $select->where('e.AgentID = ?', $agentschemeno);
        
        $select->order('pg.start_time ASC');
        return $this->fetchAll($select);
    }

    /**
     * Finds non-cancelled references sold for the supplied agent scheme number
     * between the supplied date range.  Faster, simplified version of
     * refSalesForMonthYear() for dashboard use etc.
     *
     * @param int $agentschemeno Agent scheme number.
     * @param string $startdate Start date of range in 'Y-m-d' format.
     * @param string $enddate End date of range in 'Y-m-d' format.
     *
     * @return Zend_Db_Table Database query result of references sold.
     */
    public function fastRefSalesByDateRange($agentschemeno, $startdate, $enddate) {

        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('e' => $this->_name),
            array(
                'e.RefNo'
            )
        );

        $select->joinInner(
            array('pg' => 'progress'),
            'e.RefNo = pg.refno',
            array(
                'pg.start_time'
            )
        );

        $select->joinLeft(
            array('t' => 'Tenant'),
            'e.TenantID = t.ID',
            array(
                't.ID'
            )
        );

        $select->joinLeft(
            array('pt' => 'property'),
            'e.proprefno = pt.refno',
            array(
                'pt.refno'
            )
        );

        $select->joinLeft(
            array('pd' => 'Product'),
            'e.ProductID = pd.ID',
            array(
                'pd.Name'
            )
        );

        $select->where('e.ProductID != ""');
        $select->where('pd.Name != "None"');
        $select->where('pd.Name != "none"');
        $select->where('pg.start_time >= ?', $startdate);
        $select->where('pg.start_time <= ?', $enddate);
        $select->where('e.AgentID = ?', $agentschemeno);

        return $this->fetchAll($select);
    }

    /**
     * Finds the number of non-complete, non-cancelled references grouped by
     * product name.
     *
     * @param int $agentschemeno Agent scheme number.
     *
     * @return Zend_Db_Table Database query result of references sold.
     */
    public function openRefSalesByProduct($agentschemeno) {

        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('e' => $this->_name),
            array(
                new Zend_Db_Expr('COUNT(e.RefNo) as Total')
            )
        );

        $select->joinInner(
            array('pg' => 'progress'),
            'e.RefNo = pg.refno',
            array(
                'pg.start_time'
            )
        );

        $select->joinLeft(
            array('t' => 'Tenant'),
            'e.TenantID = t.ID',
            array(
                't.ID'
            )
        );

        $select->joinLeft(
            array('pt' => 'property'),
            'e.proprefno = pt.refno',
            array(
                'pt.refno'
            )
        );

        $select->joinLeft(
            array('pd' => 'Product'),
            'e.ProductID = pd.ID',
            array(
                'pd.Name',
            )
        );

        $select->where('e.ProductID != ""');
        $select->where('pd.Name != "None"');
        $select->where('pd.Name != "none"');
        $select->where('DATE(tx_time) = "0000-00-00"');
        $select->where('DATE(firstfin_time) = "0000-00-00"');
        $select->where('e.AgentID = ?', $agentschemeno);
        $select->group('pd.Name');
        $select->order('Total DESC');
        $select->order('pd.Name ASC');

        return $this->fetchAll($select);
    }

    /**
     * Finds the number of non-complete, non-cancelled references grouped by
     * start date.
     *
     * @param int $agentschemeno Agent scheme number.
     *
     * @return Zend_Db_Table Database query result of references sold.
     */
    public function openRefSalesByProgress($agentschemeno) {

        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('e' => $this->_name),
            array(
                new Zend_Db_Expr('COUNT(e.RefNo) as Total')
            )
        );

        $select->joinInner(
            array('pg' => 'progress'),
            'e.RefNo = pg.refno',
            array(
                new Zend_Db_Expr('DATEDIFF(CURDATE(), pg.start_time) as ref_age')
            )
        );

        $select->joinLeft(
            array('t' => 'Tenant'),
            'e.TenantID = t.ID',
            array(
                't.ID'
            )
        );

        $select->joinLeft(
            array('pt' => 'property'),
            'e.proprefno = pt.refno',
            array(
                'pt.refno'
            )
        );

        $select->joinLeft(
            array('pd' => 'Product'),
            'e.ProductID = pd.ID',
            array(
                'pd.Name',
            )
        );

        $select->where('e.ProductID != ""');
        $select->where('pd.Name != "None"');
        $select->where('pd.Name != "none"');
        $select->where('DATE(tx_time) = "0000-00-00"');
        $select->where('DATE(firstfin_time) = "0000-00-00"');
        $select->where('e.AgentID = ?', $agentschemeno);
        $select->group('ref_age');
        $select->order('ref_age ASC');

        return $this->fetchAll($select);
    }

    public function refSalesOverviewByProductForMonthYear($agentschemeno, $startdate, $enddate)
    {
        $startdate = $startdate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        $enddate = $enddate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);

        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from
        (
            array('e' => $this->_name),
            array
            (
            )
        );

        $select->joinInner
        (
            array('pg' => 'progress'),
            'e.RefNo = pg.refno',
            array
            (
            )
        );

        $select->joinLeft
        (
            array('pd' => 'Product'),
            'e.ProductID = pd.ID',
            array
            (
                'pd.ID',
                'pd.Name as name',
                'count(pd.ID) as count'
            )
        );

        $select->where('e.ProductID != ""');
        $select->where('pg.start_time >= ?', $startdate);
        $select->where('pg.start_time <= ?', $enddate);
        $select->where('e.AgentID = ?', $agentschemeno);
        $select->group('pd.ID');

        return $this->fetchAll($select);
    }

    public function refSalesOverviewByApplicantTypeForMonthYear($agentschemeno, $startdate, $enddate)
    {
        $startdate = $startdate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        $enddate = $enddate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);

        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from
        (
            array('e' => $this->_name),
            array
            (
                new Zend_Db_Expr('SUM(IF(e.Guarantor = 0, 1, 0)) as countTenant'),
                new Zend_Db_Expr('SUM(IF(e.Guarantor >= 1, 1, 0)) as countGuarantor'),
            )
        );

        $select->joinInner
        (
            array('pg' => 'progress'),
            'e.RefNo = pg.refno',
            array
            (
            )
        );

        $select->where('e.ProductID != ""');
        $select->where('pg.start_time >= ?', $startdate);
        $select->where('pg.start_time <= ?', $enddate);
        $select->where('e.AgentID = ?', $agentschemeno);

        return $this->fetchAll($select);
    }

    public function refSalesOverviewBySubmissionTypeForMonthYear($agentschemeno, $startdate, $enddate)
    {
        $startdate = $startdate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        $enddate = $enddate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);

        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from
        (
            array('e' => $this->_name),
            array
            (
                new Zend_Db_Expr('SUM(IF(e.compmethod = "email", 1, 0)) as countTenant'),
                new Zend_Db_Expr('SUM(IF(e.compmethod = "Complete", 1, 0)) as countAgent'),
                new Zend_Db_Expr('SUM(IF(e.compmethod = "printit", 1, 0)) as countHomelet'),
                new Zend_Db_Expr('SUM(IF(e.compmethod = "", 1, 0)) as countOthers'),
            )
        );

        $select->joinInner
        (
            array('pg' => 'progress'),
            'e.RefNo = pg.refno',
            array
            (
            )
        );

        $select->where('e.ProductID != ""');
        $select->where('pg.start_time >= ?', $startdate);
        $select->where('pg.start_time <= ?', $enddate);
        $select->where('e.AgentID = ?', $agentschemeno);

        return $this->fetchAll($select);
    }

    /**
     * Calculate references matching SLA time periods
     *
     * @param int $agentschemeno Agent scheme number
     * @param int $month Month number
     * @param int $year Year number
     * @return array Referencing SLA time periods labels and count
     */
    public function refSlaForMonthYear($agentschemeno, $month, $year)
    {
        // Labels and counts, starting at 0. Must be in reverse order
        $slaperiods = array
        (
            72      => array('label' => '72+',      'count' => 0),
            48      => array('label' => '48 - 72',  'count' => 0),
            24      => array('label' => '24 - 48',  'count' => 0),
            0       => array('label' => '0 - 24',   'count' => 0),
        );

        // Select data for period
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from
        (
            array('e' => $this->_name),
            array()
        );

        $select->joinInner
        (
            array('pg' => 'progress'),
            'e.RefNo = pg.refno',
            array
            (
                new Zend_Db_Expr('DATE(pg.tx_time) as tx_time'),
                new Zend_Db_Expr('DATE(pg.finrep_time) as finrep_time')
            )
        );

        $select->where('AgentID = ?', $agentschemeno);

        $select->where('DATE(tx_time) != "0000-00-00"');
        $select->where('DATE(firstfin_time) != "0000-00-00"');

        $select->where('MONTH(tx_time) = ?', $month);
        $select->where('YEAR(tx_time) = ?', $year);

        $rows = $this->fetchAll($select);

        // For each record found, calculate processing times
        foreach ($rows as $row)
        {
            $startdate = new DateTime($row['tx_time']);
            $enddate = new DateTime($row['finrep_time']);

            list($startdate, $enddate) = $this->_adjustDates($startdate, $enddate); // Adjust for weekend boundaries
            $daysdiff = $this->_completionDays($startdate, $enddate); // Calculate difference
            $daysdiff -= $this->_holidayDays($startdate, $enddate); // Remove holiday days

            // Sort record into sla period range
            foreach (array_keys($slaperiods) as $key)
            {
                if ($daysdiff >= $key)
                {
                    $slaperiods[$key]['count']++;
                    break;
                }
            }
        }

        // Return labels and counts
        return $slaperiods;
    }

    /**
     * Adjust the start/end dates around weekend boundaries
     *
     * @param DateTime $startdate Start date of date range
     * @param DateTime $enddate End date of date range
     * @return array() Start date and end date modified
     */
    public function _adjustDates(DateTime $startdate, DateTime $enddate)
    {
        // Adjust for weekends on boundarys
        if ($startdate->format('w') == 5) // Saturday
            $startdate->add(new DateInterval('P2D'));

        if ($startdate->format('w') == 6) // Sunday
            $startdate->add(new DateInterval('P1D'));

        if ($enddate->format('w') == 5) // Saturday
            $enddate->add(new DateInterval('P2D'));

        if ($enddate->format('w') == 6) // Sunday
            $enddate->add(new DateInterval('P1D'));

        return array($startdate, $enddate);
    }

    /**
     * Calculates the number of business days inbetween a given start and end date
     *
     * @param string $startdate Start date
     * @param string $enddate End date
     * @return int Business days within range
     */
    private function _completionDays(DateTime $startdate, DateTime $enddate)
    {
        // Days difference
        $daysdiff = date_diff($startdate, $enddate);
        $daysdiff = $daysdiff->format('%d'); // requires %, which is different to the main DateTime format method - a bit poor in consistency of API.

        // Subtract all the weekends found between the start and end dates.
        $weekend_days = (($enddate->format('W') - $startdate->format('W')) * 2);
        $daysdiff -= $weekend_days;

        return $daysdiff;
    }

    private function _holidayDays(DateTime $startdate, DateTime $enddate)
    {
        $holidays = new Datasource_Core_Holidays();
        return $holidays->getHolidayDaysBetween($startdate->format('Y-m-d'), $enddate->format('Y-m-d'));
    }
}
