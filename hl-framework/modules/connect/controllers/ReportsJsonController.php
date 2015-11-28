<?php

final class Connect_ReportsJsonController extends Zend_Controller_Action
{
    protected $_agentSchemeNumber;
    
    /**
     * Turn off view rendering for json output
     *
     * @return void
     */
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);
    }
    
    /**
     * Check identity of client, if they are logged in
     *
     * @return bool User has identity, true or false
     */
    private function _checkIdent()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));
        
        if ($auth->hasIdentity())
            $this->_agentSchemeNumber = $auth->getStorage()->read()->agentschemeno;
        
        return $auth->hasIdentity();
    }
    
    /**
     * Visual reports
     *
     * @return void
     */
    public function visualAction()
    {
        if ($this->_checkIdent())
        {
            $data = array
            (
                'data'  => array(),
                'legend' => array()
            );
            
            // Obtain parameters in request for instruction of what data to return
            $request = $this->getRequest();
            
            $refdata = $request->getParam('refdata');
            $landdata = $request->getParam('landdata');
            $tendata = $request->getParam('tendata');
            $commdata = $request->getParam('commdata');
            
            $yearnum_1 = $request->getParam('yearnum_1');
            $yearnum_2 = $request->getParam('yearnum_2');
            $yearnum_3 = $request->getParam('yearnum_3');
            
            // Obtain start/end years for 3 year history or data
            $endyear = date("Y");
            $startyear = $endyear - 2;
            
            if ($refdata != null)
            {
                // Ref data requested
                $mireport = new Datasource_Connect_Mi_RefSales();
                $year = $startyear;
                $seriesdata = array();
                
                if ($yearnum_1) // year 1 data
                {
                    $reportdata = $mireport->refSalesCountByMonthForYear($this->_agentSchemeNumber, $year);
                    $seriesdata = array_merge($seriesdata, $reportdata);
                }
                $year++;
                
                if ($yearnum_2) // year 2 data
                {
                    $reportdata = $mireport->refSalesCountByMonthForYear($this->_agentSchemeNumber, $year);
                    $seriesdata = array_merge($seriesdata, $reportdata);
                }
                $year++;
                
                if ($yearnum_3) // year 3 data
                {
                    $reportdata = $mireport->refSalesCountByMonthForYear($this->_agentSchemeNumber, $year);
                    $seriesdata = array_merge($seriesdata, $reportdata);
                }
                
                array_push($data['data'], $seriesdata);
                array_push($data['legend'], 'Referencing');
            }
            
            if ($landdata != null)
            {
                // Ref data requested
                $mireport = new Datasource_Connect_Mi_InsuranceSales();
                $year = $startyear;
                $seriesdata = array();
                
                if ($yearnum_1) // year 1 data
                {
                    $reportdata = $mireport->landlordsSalesCountByMonthForYear($this->_agentSchemeNumber, $year);
                    $seriesdata = array_merge($seriesdata, $reportdata);
                }
                $year++;
                
                if ($yearnum_2) // year 2 data
                {
                    $reportdata = $mireport->landlordsSalesCountByMonthForYear($this->_agentSchemeNumber, $year);
                    $seriesdata = array_merge($seriesdata, $reportdata);
                }
                $year++;
                
                if ($yearnum_3) // year 3 data
                {
                    $reportdata = $mireport->landlordsSalesCountByMonthForYear($this->_agentSchemeNumber, $year);
                    $seriesdata = array_merge($seriesdata, $reportdata);
                }
                
                array_push($data['data'], $seriesdata);
                array_push($data['legend'], 'Landlords');
            }
            
            if ($tendata != null)
            {
                // Ref data requested
                $mireport = new Datasource_Connect_Mi_InsuranceSales();
                $year = $startyear;
                $seriesdata = array();
                
                if ($yearnum_1) // year 1 data
                {
                    $reportdata = $mireport->tenantSalesCountByMonthForYear($this->_agentSchemeNumber, $year);
                    $seriesdata = array_merge($seriesdata, $reportdata);
                }
                $year++;
                
                if ($yearnum_2) // year 2 data
                {
                    $reportdata = $mireport->tenantSalesCountByMonthForYear($this->_agentSchemeNumber, $year);
                    $seriesdata = array_merge($seriesdata, $reportdata);
                }
                $year++;
                
                if ($yearnum_3) // year 3 data
                {
                    $reportdata = $mireport->tenantSalesCountByMonthForYear($this->_agentSchemeNumber, $year);
                    $seriesdata = array_merge($seriesdata, $reportdata);
                }
                
                array_push($data['data'], $seriesdata);
                array_push($data['legend'], 'Tenants');
            }
            
            if ($commdata != null)
            {
                // Ref data requested
                // Ref data requested
                $mireport = new Datasource_Connect_Mi_InsuranceSales();
                $year = $startyear;
                $seriesdata = array();
                
                if ($yearnum_1) // year 1 data
                {
                    $reportdata = $mireport->commmissionByMonthForYear($this->_agentSchemeNumber, $year);
                    $seriesdata = array_merge($seriesdata, $reportdata);
                }
                $year++;
                
                if ($yearnum_2) // year 2 data
                {
                    $reportdata = $mireport->commmissionByMonthForYear($this->_agentSchemeNumber, $year);
                    $seriesdata = array_merge($seriesdata, $reportdata);
                }
                $year++;
                
                if ($yearnum_3) // year 3 data
                {
                    $reportdata = $mireport->commmissionByMonthForYear($this->_agentSchemeNumber, $year);
                    $seriesdata = array_merge($seriesdata, $reportdata);
                }
                
                array_push($data['data'], $seriesdata);
                array_push($data['legend'], 'Commission');
            }
            
            
            echo Zend_Json::Encode($data);
        }
    }
}
