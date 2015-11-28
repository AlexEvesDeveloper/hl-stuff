<?php

final class Datasource_Connect_Mi_InsuranceSales extends Zend_Db_Table_Multidb
{
    /**#@+
     * Mandatory attributes
     */
    protected $_name = 'policy';
    protected $_primary = 'policynumber';
    protected $_multidb = 'db_legacy_slave_homelet';
    /**#@-*/
    
    public function tenantSalesCountByMonthForYear($agentschemeno, $year)
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
            $this->_name,
            array
            (
                new Zend_Db_Expr('DATE_FORMAT(issuedate, "%M-%Y") as month'),
                new Zend_Db_Expr('count(companyschemenumber) as count')
            )
        );
        
        $select->where('policytype = "T"');
        $select->where('issuedate >= ?', $year . '-01-01');
        $select->where('issuedate <= ?', $year . '-12-31');
        $select->where('paystatus not in ("CANCELLED", "DECLINED", "DELETED", "LAPSED", "Void")');
        $select->where('companyschemenumber = ?', $agentschemeno);
        $select->group(new Zend_Db_Expr('DATE_FORMAT(issuedate, "%m")'));
        
        $rowset = $this->fetchAll($select);
        
        // Copy rowset into data arrary
        foreach ($rowset as $row)
            $months[$row['month']] = $row['count'];
        
        // Turn associate array into list array
        foreach ($months as $month => $count)
            array_push($data, array($month, $count));
        
        return $data;
    }
    
    public function landlordsSalesCountByMonthForYear($agentschemeno, $year)
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
            $this->_name,
            array
            (
                new Zend_Db_Expr('DATE_FORMAT(issuedate, "%M-%Y") as month'),
                new Zend_Db_Expr('count(companyschemenumber) as count')
            )
        );
        
        $select->where('policytype = "L"');
        $select->where('issuedate >= ?', $year . '-01-01');
        $select->where('issuedate <= ?', $year . '-12-31');
        $select->where('paystatus not in ("CANCELLED", "DECLINED", "DELETED", "LAPSED", "Void")');
        $select->where('companyschemenumber = ?', $agentschemeno);
        $select->group(new Zend_Db_Expr('DATE_FORMAT(issuedate, "%m")'));
        
        $rowset = $this->fetchAll($select);
        
        // Copy rowset into data arrary
        foreach ($rowset as $row)
            $months[$row['month']] = $row['count'];
        
        // Turn associate array into list array
        foreach ($months as $month => $count)
            array_push($data, array($month, $count));
        
        return $data;
    }
    
    public function commmissionByMonthForYear($agentschemeno, $year)
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
            array('p' => $this->_name),
            array
            (
                new Zend_Db_Expr('DATE_FORMAT(paymentdate, "%M-%Y") as month'),
                new Zend_Db_Expr('SUM(t.agentCommission) as count')
            )
        );
        
        $select->joinLeft
        (
            array('t' => 'newtransactions'),
            't.policynumber = p.policynumber',
            array()
        );
        
        $select->joinLeft
        (
            array('c' => 'customer'),
            'c.refno = p.refno',
            array()
        );
        
        $select->where('paymentdate >= ?', $year . '-01-01');
        $select->where('paymentdate <= ?', $year . '-12-31');
        $select->where('t.agentschemeno = ?', $agentschemeno);
        $select->group(new Zend_Db_Expr('DATE_FORMAT(paymentdate, "%m")'));
        
        $rowset = $this->fetchAll($select);
        
        // Copy rowset into data arrary
        foreach ($rowset as $row)
            $months[$row['month']] = $row['count'];
        
        // Turn associate array into list array
        foreach ($months as $month => $count)
            array_push($data, array($month, $count));
        
        return $data;
    }
    
    public function liveRGPolicyForMonthYear($agentschemeno, $startdate, $enddate)
    {
        $startdate = $startdate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        $enddate = $enddate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        
        $select = $this->select();
        $select->setIntegrityCheck(false);
        
        $select->from
        (
            array('p' => $this->_name),
            array
            (
                'p.policynumber',
                'p.propaddress1',
                'p.propaddress5',
                new Zend_Db_Expr('DATE_FORMAT(p.startdate, "%d/%m/%Y") as startdate'),
                new Zend_Db_Expr('DATE_FORMAT(p.enddate, "%d/%m/%Y") as enddate'),
            )
        );
        
        $select->joinLeft
        (
            array('c' => 'customer'),
            'p.refno = c.refno',
            array
            (
                'c.firstname',
                'c.lastname',
                'p.policylength',
                'p.premium',
            )
        );
        
        $select->joinLeft
        (
            array('e' => 'referencing_uk.Enquiry'),
            'e.policynumber = p.policynumber',
            array
            (
            )
        );
        
        $select->joinLeft
        (
            array('o' => 'referencing_uk.Product'),
            'o.ID = e.ProductID',
            array
            (
                new Zend_Db_Expr('substring(o.Name,1,length(o.Name)-length("Rent Guarantee")) as Name')
            )
        );
        
        $select->where('p.paystatus not in ("CANCELLED", "DECLINED", "DELETED", "LAPSED", "Void")');
        $select->where('p.companyschemenumber = ?', $agentschemeno);
        $select->where('p.policyname in ("premier", "Premier Xpress")');
        $select->where('p.startdate >= ?', $startdate);
        $select->where('p.startdate <= ?', $enddate);
        
        return $this->fetchAll($select);
    }
    
    public function lapsedRGPolicyForMonthYear($agentschemeno, $startdate, $enddate)
    {
        $startdate = $startdate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        $enddate = $enddate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        
        $select = $this->select();
        $select->setIntegrityCheck(false);
        
        $select->from
        (
            array('p' => $this->_name),
            array
            (
                'p.policynumber',
                'p.propaddress1',
                'p.propaddress5',
                new Zend_Db_Expr('DATE_FORMAT(p.enddate, "%d/%m/%Y") as enddate'),
            )
        );
        
        $select->joinLeft
        (
            array('c' => 'customer'),
            'p.refno = c.refno',
            array
            (
                'c.firstname',
                'c.lastname',
                'p.policylength',
            )
        );
        
        $select->joinLeft
        (
            array('e' => 'referencing_uk.Enquiry'),
            'e.policynumber = p.policynumber',
            array
            (
            )
        );
        
        $select->joinLeft
        (
            array('o' => 'referencing_uk.Product'),
            'o.ID = e.ProductID',
            array
            (
                new Zend_Db_Expr('substring(o.Name,1,length(o.Name)-length("Rent Guarantee")) as Name')
            )
        );
        
        $select->where('p.paystatus in ("CANCELLED", "DECLINED", "DELETED", "LAPSED", "Void")');
        $select->where('p.companyschemenumber = ?', $agentschemeno);
        $select->where('p.policyname in ("premier", "Premier Xpress")');
        $select->where('p.startdate >= ?', $startdate);
        $select->where('p.startdate <= ?', $enddate);
        
        return $this->fetchAll($select);
    }
    
    public function landlordsInsuranceSalesForMonthYear($agentschemeno, $startdate, $enddate)
    {
        $startdate = $startdate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        $enddate = $enddate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        
        $select = $this->select();
        $select->setIntegrityCheck(false);
        
        $select->from
        (
            array('p' => $this->_name),
            array
            (
                'p.policyname',
                'p.policylength',
                'p.paystatus',
                'p.policynumber',
                'p.propaddress1',
                'p.propaddress5',
                new Zend_Db_Expr('DATE_FORMAT(p.startdate, "%d/%m/%Y") as startdate'),
                new Zend_Db_Expr('DATE_FORMAT(p.enddate, "%d/%m/%Y") as enddate'),
            )
        );
        
        $select->joinLeft
        (
            array('c' => 'customer'),
            'p.refno = c.refno',
            array
            (
                'c.firstname',
                'c.lastname',
            )
        );
        
        $select->where('p.policyname IN ("landlords", "landlordsp", "lowcostlandlords", "portfolio")');
        $select->where('p.issuedate >= ?', $startdate);
        $select->where('p.issuedate <= ?', $enddate);
        $select->where('p.companyschemenumber = ?', $agentschemeno);
        $select->order('p.issuedate ASC');
        
        return $this->fetchAll($select);
    }
    
    public function tenantsInsuranceSalesForMonthYear($agentschemeno, $startdate, $enddate)
    {
        $startdate = $startdate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        $enddate = $enddate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        
        $select = $this->select();
        $select->setIntegrityCheck(false);
        
        $select->from
        (
            array('p' => $this->_name),
            array
            (
                'p.policyname',
                'p.policylength',
                'p.paystatus',
                'p.policynumber',
                'p.propaddress1',
                'p.propaddress5',
                new Zend_Db_Expr('DATE_FORMAT(p.startdate, "%d/%m/%Y") as startdate'),
                new Zend_Db_Expr('DATE_FORMAT(p.enddate, "%d/%m/%Y") as enddate'),
            )
        );
        
        $select->joinLeft
        (
            array('c' => 'customer'),
            'p.refno = c.refno',
            array
            (
                'c.firstname',
                'c.lastname',
            )
        );
        
        $select->where('p.policyname IN ("tenantsp", "tenants")');
        $select->where('p.issuedate >= ?', $startdate);
        $select->where('p.issuedate <= ?', $enddate);
        $select->where('p.companyschemenumber = ?', $agentschemeno);
        $select->order('p.issuedate ASC');
        
        return $this->fetchAll($select);
    }
    
    public function commissionForMonthYear($agentschemeno, $startdate, $enddate)
    {
        $commissiondata = array();
        
        $startdate = $startdate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        $enddate = $enddate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY);
        
        $select = $this->select();
        $select->setIntegrityCheck(false);
        
        $select->from
        (
            array('n' => 'newtransactions'),
            array
            (
                'n.paymentdate',
                new Zend_Db_Expr('SUM(n.agentCommission) as commission'),
                new Zend_Db_Expr('DATE_FORMAT(n.paymentdate, "%M %Y") as paymentdatelong'),
            )
        );
        
        $select->joinInner
        (
            array('p' => 'policy'),
            'n.policynumber = p.policynumber',
            array
            (
                'p.policytype',
            )
        );
        
        $select->where('n.paymentdate >= ?', $startdate);
        $select->where('n.paymentdate < ?', $enddate);
        $select->where('n.agentschemeno = ?', $agentschemeno);
        $select->group(array('paymentdatelong', 'policytype'));
        $select->order('n.paymentdate ASC');
        
        $data = $this->fetchAll($select);
        foreach ($data as $datum)
        {
            $paymentdate = $datum['paymentdatelong'];
            $policytype = $datum['policytype'];
            $commission = $datum['commission'];
            
            if (!isset($commissiondata[$paymentdate]))
                $commissiondata[$paymentdate] = array();
            
            $commissiondata[$paymentdate][$policytype] = $commission;
        }
        
        return $commissiondata;
    }
}
