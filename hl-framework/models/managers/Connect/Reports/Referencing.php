<?php

/**
 * Manager class for collecting, generating and outputting agent-centric
 * referencing stats.
 *
 * @category   Manager
 * @package    Manager_Connect
 * @subpackage Reports
 */
class Manager_Connect_Reports_Referencing {

    /**#@+
     * References to common aspects stored in the datasources.
     */
    protected $_agentDatasource;
    protected $_miRefSalesDatasource;
    protected $_agentStatsDatasource;
    /**#@-*/

    public function __construct() {

        // Instantiate datasources
        $this->_agentDatasource = new Datasource_Core_Agents();
        $this->_miRefSalesDatasource = new Datasource_Connect_Mi_RefSales();
        $this->_agentStatsDatasource = new Datasource_Core_Agent_Stats();
    }

    public function updateStats() {

        // Get list of active agents
        $asnList = $this->_agentDatasource->getSchemeNumbers();
//$asnList = array(1502469);

        $startDate = date('Y-m-d', strtotime('-14 day'));
        $endDate = date('Y-m-d');

        // Initialise blank stat array for past 14 days
        $blankDateTotal = array();
        $dateMappings = array();
        for ($i = -14; $i < 0; $i++) {
            $dateString = date('Y-m-d', strtotime("{$i} day"));
            $blankDateTotal[$dateString] = 0;
        }

        // Initialise a single stat object to re-use, saves CPU time and memory
        $stat = new Model_Core_Agent_Stat();

        // Stat type ID lookups taken out of loop
        $statTypeId_newRefsByDay            = Model_Core_Agent_StatType::NEW_REFS_BY_DAY;
        $statTypeId_openRefsByProduct       = Model_Core_Agent_StatType::OPEN_REFS_BY_PRODUCT;
        $statTypeId_openRefsByProgress      = Model_Core_Agent_StatType::OPEN_REFS_BY_PROGRESS;
        $statTypeId_newPoliciesByDay        = Model_Core_Agent_StatType::NEW_POLICIES_BY_DAY;
        $statTypeId_openPoliciesByProduct   = Model_Core_Agent_StatType::OPEN_POLICIES_BY_PRODUCT;
        $statTypeId_openPoliciesByInception = Model_Core_Agent_StatType::OPEN_POLICIES_BY_INCEPTION;

        // Generate stats for each agent
        foreach($asnList as $asn) {

            /**
             * New Refs By Day
             */

            // Look up ref sales
            $refSales = $this->_miRefSalesDatasource->fastRefSalesByDateRange($asn, $startDate, $endDate);

            // Initialise stat array for past 14 days
            $dateTotal = $blankDateTotal;

            // Count how many non-cancelled references there are for each day
            foreach ($refSales as $row) {
                $dateTotal[substr($row['start_time'], 0, 10)]++;
            }

            // Erase stats for this agent/stat type
            $this->_agentStatsDatasource->eraseStat(
                $statTypeId_newRefsByDay,
                $asn
            );

            // Write stats into DB
            foreach($dateTotal as $date => $count) {
                $stat->statTypeId           = $statTypeId_newRefsByDay;
                $stat->agentSchemeNumber    = $asn;
                $stat->dateApplicable       = $date;
                $stat->variant              = null;
                $stat->value                = $count;

                $this->_agentStatsDatasource->setStat($stat, false);
            }

            /**
             * Open Refs By Product
             */

            // Look up ref sales
            $refSales = $this->_miRefSalesDatasource->openRefSalesByProduct($asn);

            // Erase stats for this agent/stat type
            $this->_agentStatsDatasource->eraseStat(
                $statTypeId_openRefsByProduct,
                $asn
            );

            // Write stat into DB
            foreach($refSales as $refSale) {
                $stat->statTypeId           = $statTypeId_openRefsByProduct;
                $stat->agentSchemeNumber    = $asn;
                $stat->dateApplicable       = null;
                $stat->variant              = $refSale['Name'];
                $stat->value                = $refSale['Total'];

                $this->_agentStatsDatasource->setStat($stat, false);
            }

            /**
             * Open Refs By Progress
             */

            // Look up ref sales
            $refSales = $this->_miRefSalesDatasource->openRefSalesByProgress($asn);

            // Erase stats for this agent/stat type
            $this->_agentStatsDatasource->eraseStat(
                $statTypeId_openRefsByProgress,
                $asn
            );

            // Write stat into DB
            foreach($refSales as $refSale) {
                $stat->statTypeId           = $statTypeId_openRefsByProgress;
                $stat->agentSchemeNumber    = $asn;
                $stat->dateApplicable       = null;
                $stat->variant              = $refSale['ref_age'];
                $stat->value                = $refSale['Total'];

                $this->_agentStatsDatasource->setStat($stat, false);
            }
        }

    }
}