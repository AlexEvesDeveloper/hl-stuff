<?php
/**
 * Model definition for the AgentCommissionRate table
 *
 * @category   Datasource
 * @package    Datasource_Core
 * @subpackage Agent
 */

class Datasource_Core_Agent_AgentCommissionRate extends Zend_Db_Table_Multidb {
    protected $_name = 'AgentCommissionRate';
    protected $_primary = 'agentschemeno';
    protected $_multidb = 'db_legacy_homelet';
    protected $_rateArr = array();
    /**
    * Finds a set of insurer rates by policy option and policy startdate
    *
    * @param int id
    * @param date $date
    * @return Array
    *
    * @example $commRate[]=$commR->getCommissionRatebyDate(1502469,'2011-01-01');
    */
    function getCommissionRate ($agent,$date) {

        $select = $this->select()
                  ->where("agentschemeno = ?", $agent['agentschemeno'])
                  ->where("startdate <= ?", $date)
                  ->where("enddate >= ? OR enddate='0000-00-00' ", $date);
        $row = $this->fetchRow($select);

        if (count($row) > 0) {

            $this->_rateArr= array(
                'newbuscommissionrate'     =>  $row->newbuscommissionrate,
                'commissionrate'           =>  $row->commissionrate,
                'newbuscommissionrate_tc'  =>  $row->newbuscommissionrate_tc,
                'commissionrate_tc'        =>  $row->commissionrate_tc
            );

            return true;
        } else {
            // Can't find content for that key - log a warning
            Application_Core_Logger::log('Agent Commission Rate not found in database (agentschemeno = ' . $agent['agentschemeno'] . ')', 'warning');
            return false;
        }

    }

    function getRate($agent,$term,$date=null,$policyType) {

        $rateArray=array();
        if (is_null($date)) $date = date("Y-m-d");
        if(!$this->getCommissionRate($agent,$date)) $this->_rateArr=$agent;

        $rateArray=$this->_rateArr;



        if($term==1){
            if($agent['hasvariablecommission_newbiz']==1 and $policyType=='T') {
                return $rateArray['newbuscommissionrate_tc'];
            }
            else{
                return $rateArray['newbuscommissionrate'];
            }
        }
        else{
            if($agent['hasvariablecommission']==1 and $policyType=='T') {
                return $rateArray['commissionrate_tc'];
            }
            else{
                return $rateArray['commissionrate'];
            }

        }


    }
}
?>
