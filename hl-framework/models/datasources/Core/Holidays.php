<?php

final class Datasource_Core_Holidays extends Zend_Db_Table_Multidb
{
    /**#@+
     * Mandatory attributes
     */
    protected $_name = 'holidays';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet';
    /**#@-*/
    
    /**
     * Calculate the number of holidays between two dates.
     *
     * @param string $startdate Start date of date range
     * @param string $enddate End date of date range
     * @return int Number of holiday days between date range
     */
    public function getHolidayDaysBetween($startdate, $enddate)
    {
        // Select data for period
        $select = $this->select();
        $select->setIntegrityCheck(false);
        
        $select->from
        (
            array('h' => $this->_name),
            array
            (
                new Zend_Db_Expr('count(*) as count'),
            )
        );
        
        $select->where('date >= ?', $startdate);
        $select->where('date <= ?', $enddate);
        
        $row = $this->fetchRow($select);
        
        if (isset($row))
            return $row['count'];
            
        return 0;
    }
}
