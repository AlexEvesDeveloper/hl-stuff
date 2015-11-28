<?php

/**
* Model definition for the legacy reporthistory datasource.
*/
class Datasource_ReferencingLegacy_ReportHistory extends Zend_Db_Table_Multidb 
{
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'reporthistory';
    protected $_primary = 'refno';
    /**#@-*/

    public function getTimeReportGenerated($refno, $type) 
    {
        if(empty($refno)) {            
            return null;
        }
        
        $sql = "select unix_timestamp(timegenerated) AS  timegenerated
                from reporthistory
                where refno='{$refno}' and type='{$type}'
                order by timegenerated desc limit 1";        
        $res = $this->getAdapter()->query($sql);
        $timegenerated = $res->fetchAll();

        if(empty($timegenerated)) {
            $returnVal = null;
        }
        else {
            $returnVal = $timegenerated[0]['timegenerated'];
        }

        return $returnVal;    
    }

    /**
     * Get the latest report generated of any type.
     *
     * @param string $refno External reference number
     * @param strubg $type Optional type restriction
     * @return Model_ReferencingLegacy_Report 
     */
    public function getLatestReport($refno, $type = null)
    {
        $report = null;
        $select = $this->select()
                       ->where('refno = ?', $refno)
                       ->order(array('timegenerated DESC'))
                       ->limit(1);

        // Add report type restriction if given
        if ($type) {
            $select->where('type = ?', $type);
        }

        $row = $this->fetchRow($select);

        if ($row) {
            // Report exists, populate object and return
            $report = new Model_Referencing_Report();
            $report->externalId = $row['refno'];
            $report->csuid = $row['csuid'];
            $report->generationTime = $row['timegenerated'];
            $report->reportType = $row['type'];
            $report->fileName = $row['filename'];

            // Create a validation key from the filename.
            $report->validationKey = preg_replace('/\/|\.pdf/', '', $report->fileName);
        }

        return $report;
    }
}    
?>

