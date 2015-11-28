<?php

/**
* Business rules class providing disbursement services.
*/
class Manager_Core_Disbursement {

    /**#@+
     * References for newtransactions, Transactions, PaymentTransaction
     *
     */
	public $_policynumber;
	public $_policyname;
	public $_termid;
    public $_startdate;
    public $_transaction; 
    public $_newtransaction;
    public $_paymentTransaction;
    public $_transactionSupport;
    public $_grosspremium=0.00;
    public $_policypremium=0.00;
    public $_policynetprem=0.00;
    public $_policyIPT=0.00;
    public $_handlingcharge=0.00;
    public $_agentcommission=0.00;
    public $_netOption=0.00;
    public $_iptOption=0.00;
    public $_premOption=0.00;
    public $_grossOption=0.00;
    public $_sumInsOption=0.00;
    public $_aCommOption=0.00;
    public $_ptranID;
    public $_months;
    public $_optionID;
    public $_discOption=0;
    public $_netOptionIns=0.00;
    public $_iptOptionIns=0.00;
    public $_premOptionIns=0.00;
    public $_grossOptionIns=0.00;
    public $_insurerID;
    public $_banked=0.00;
    public $_income=0.00;
    public $_introComm=0.00; 
    public $_introCommOption=0.00;
    public $_premTcontents=0.00;
    public $_sumTcontents=0.00;
    public $_disTcontents=0.00;
    public $_iptTcontents=0.00;
    public $_premTpedel=0.00;
    public $_sumTpedel=0.00;
    public $_disTpedel=0.00;
    public $_iptTpedel=0.00;
    public $_premTposs=0.00;
    public $_sumTposs=0.00;
    public $_disTposs=0.00;
    public $_iptTposs=0.00;
    public $_hpc=0.00;
    public $_transIDDW;
    public $_transID;
    public $_amount;
    public $_mult;
    public $_disLbuilding=0.00;
    public $_sumLbuilding=0.00;
    public $_premLbuilding=0.00;
    public $_iptLbuilding=0.00;
    public $_disLBA=0.00;
    public $_sumLBA=0.00;
    public $_premLBA=0.00;
    public $_iptLBA=0.00;
    public $_disLcontents=0.00;
    public $_sumLcontents=0.00;
    public $_premLcontents=0.00;
    public $_iptLcontents=0.00;
    public $_disLCA=0.00;
    public $_sumLCA=0.00;
    public $_premLCA=0.00;
    public $_iptLCA=0.00;
    public $_disLG=0.00;
    public $_sumLG=0.00;
    public $_premLG=0.00;
    public $_iptLG=0.00;
    public $_disLR=0.00;
    public $_sumLR=0.00;
    public $_premLR=0.00;
    public $_iptLR=0.00;
    public $_disES=0.00;
    public $_sumES=0.00;
    public $_premES=0.00;
    public $_iptES=0.00;
    public $_disEB=0.00;
    public $_sumEB=0.00;
    public $_premEB=0.00;
    public $_iptEB=0.00;
    
    /**#@-*/

    
    public function __construct() {
        
             
        $this->_transaction = new Datasource_Core_Disbursement_Transactions();
        $this->_newtransaction = new Datasource_Core_Disbursement_Newtransactions();
        $this->_paymentTransaction = new Datasource_Core_Disbursement_PaymentTransaction();
        $this->_transactionSupport = new Datasource_Core_Disbursement_Transactionsupport();
    }
    
    
     /**
     * Saves the disbursement info to the relevant table 
     *
     * @param String policy, double $amount, int $months, String $paymethod, date $paymentdate, double $fee, int $csuID
     *
     * @return 
     *
     * 
     */
    public function processDisbursement($policynumber,$amount,$months,$paymethod,$paymentdate=null,$fee=null,$csuid=0,$transactionType='payment'){
        if(is_null($paymentdate)) $paymentdate=date("Y-m-d");
        /**
        * prepair data to build transactions
        */
        $this->_policynumber=$policynumber;
        $this->_amount=$amount;
        $this->_months=$months;
        $policyDisb = new Datasource_Insurance_LegacyPolicies();
        $policy = $policyDisb->getByPolicyNumber($policynumber);
        $this->_policyname = $policy->policyName;
        $customerDisb = new Datasource_Core_LegacyCustomers();
        $customer = $customerDisb->getCustomer($policy->refNo);
        $scheduleDisb = new Datasource_Insurance_Schedules();
        $schedule = $scheduleDisb->retrieveByPolicyNumber($policynumber);
        $agentDisb = new Datasource_Core_Agents();
        $agentS = $agentDisb->getBySchemeNumber($policy->agentSchemeNumber);
        $agent = $agentDisb->fetchrow($agentS);
        $termDisb = new Datasource_Insurance_Policy_Term();
        $term = $termDisb->getPolicyTerm($policynumber,$policy->startDate);
        $this->_startdate=$policy->startDate;
        $transdata = array();
        $transdata['paymentrefno']   = $schedule->paymentRefNo;
		$transdata['policynumber']   = $policynumber;
		$transdata['paymentdate']    = $paymentdate;
		$transdata['amount']	     = $amount;
		$transdata['handlingcharge'] = (isset($fee)) ? $fee : $schedule->ddFee;
		$transdata['csuid'] 		 = $csuid;
		$transdata['months']         = $months;
        $transdata['paymethod'] 	 = $paymethod;
		$transdata['whitelabelID']   = $agent['twolettercode'];
		$transdata['agentschemeno']	 = $policy->agentSchemeNumber;
        $transdata['premier'] 		 = ucfirst($agent['premier']);
        $transdata['salesman']		 = $agent['salesman'];
        $transdata['riskarea'] 		 = $policy->riskArea;
		$transdata['riskareab']		 = $policy->riskAreaB;
        $transdata['isNewBusiness']  = ($term['term']==1) ? 'yes' : 'no';
		$transdata['isPaidnet'] 	 = $policy->paidNet;        
        $transdata['policyTermID']   = $term['id'];
        $transdata['policyname']     = $policy->policyName;
        $transdata['type']           = $transactionType;
        $this->_transID              = $this->_newtransaction->saveDetails($transdata);
        $transdata['trans_id']       = $this->_transID;
        $this->_termid = $transdata['policyTermID'];
        /*
         Create transaction record for MI
        */
        
        $transupportdata = array();
        
        $this->_transIDDW                      = $this->_transaction->saveDetails($transdata);
        $transupportdata['trans_id']           = $this->_transIDDW;
        $transupportdata['customerTitle']      = $customer->getTitle();
		$transupportdata['customerFirstName']  = $customer->getFirstName();
		$transupportdata['customerLastName']   = $customer->getLastName();
        $transupportdata['riskAddress1']       = $policy->propertyAddress1;
		$transupportdata['riskAddress2']       = $policy->propertyAddress2;
		$transupportdata['riskAddress3']       = $policy->propertyAddress3;
		$transupportdata['riskPostcode']       = $policy->propertyPostcode;
		$transupportdata['policytype']         = $policy->policyType;
		$transupportdata['payby']              = $policy->payBy;
		$transupportdata['policyLength']       = $policy->policyLength;
        
        $this->_transactionSupport->saveDetails($transupportdata);
           
        $this->_ptranID = $this->_paymentTransaction->saveDetails($transdata);
        
        $this->_getMult($policy);
        
        /*
         calculate for disbursement
        */
       
        $this->_calculateGrossprem($policy);
              
        $this->_calculateAgentComm($policy,$agent,$term['term']);
        
                
        $policyOptionsArray = explode("|", $policy->policyOptions);
        
        
        $es=array(4,28,29,30);
        foreach ($policyOptionsArray as $key => $value){
          $this->_initialise();
          $option=new Datasource_Insurance_Policy_Options($policy->policyType);
          
          $this->_optionID=$option->fetchOptionsByName($value);
         
          $this->_sumInsOption=$policyDisb->getPolicyOptionMatch($policy->policyOptions,$value,$policy->amountsCovered);
            
          if($this->_sumInsOption>0 || in_array($this->_optionID,$es)){
          $this->_premOption=$policyDisb->getPolicyOptionMatch($policy->policyOptions,$value,$policy->optionPremiums)*$this->_mult;
          $this->_discOption=$policyDisb->getPolicyOptionMatch($policy->policyOptions,$value,$policy->optionDiscounts);
          $this->_calculateAgentCommOption();
          $this->_calculateTax();
          $this->_calculateNet($policy,$value);
          $this->_storeOptionDisbursement();
          }
         
          $this->_policynetprem+=$this->_netOption;
          $this->_policyIPT+=$this->_iptOption;
          
          if($value=="contentstp"){
            $this->_disTcontents  =  $this->_netOption;
            $this->_iptTcontents  =  $this->_iptOption;
            $this->_premTcontents =  $this->_premOption;
            $this->_sumTcontents  =  $this->_sumInsOption;
                                
          }
          elseif($value=="pedalcyclesp"){
            $this->_disTpedel     =  $this->_netOption;
            $this->_iptTpedel     =  $this->_iptOption;
            $this->_premTpedel    =  $this->_premOption;
            $this->_sumTpedel     =  $this->_sumInsOption;         
          }
          elseif($value=="possessionsp" || $value=="specpossessionsp"){
            $this->_disTposs     +=  $this->_netOption;
            $this->_iptTposs     +=  $this->_iptOption;
            $this->_premTposs    +=  $this->_premOption;
            $this->_sumTposs     +=  $this->_sumInsOption;
            
          }
          elseif($value=="buildingsp" || $value=="buildingslflood"){
          	$this->_disLbuilding     += $this->_netOption;
          	$this->_iptLbuilding     +=  $this->_iptOption;
            $this->_premLbuilding    +=  $this->_premOption;
            $this->_sumLbuilding     =  $this->_sumInsOption;
          }
          elseif($value=="buildingsAccidentalDamagep"){
          	$this->_disLBA     =  $this->_netOption;
            $this->_iptLBA     =  $this->_iptOption;
            $this->_premLBA    =  $this->_premOption;
            $this->_sumLBA     =  $this->_sumInsOption;     
          }
          elseif($value=="limitedcontentsp"){
          	$this->_disLcontents     =  $this->_netOption;
            $this->_iptLcontents     =  $this->_iptOption;
            $this->_premLcontents    =  $this->_premOption;
            $this->_sumLcontents     =  $this->_sumInsOption; 
          }
          elseif($value=="contentslp" || $value=="contentslflood"){
          	$this->_disLcontents     +=  $this->_netOption;
            $this->_iptLcontents     +=  $this->_iptOption;
            $this->_premLcontents    +=  $this->_premOption;
            $this->_sumLcontents     =  $this->_sumInsOption;
          	
          }
          elseif($value=="contentslAccidentalDamagep"){
          	$this->_disLCA     =  $this->_netOption;
            $this->_iptLCA     =  $this->_iptOption;
            $this->_premLCA    =  $this->_premOption;
            $this->_sumLCA     =  $this->_sumInsOption; 
          }
         elseif($value=="emergencyassistance" || $value=="emergencyassistancestandalone"){
          	$this->_disES     +=  $this->_netOption;
            $this->_iptES     +=  $this->_iptOption;
            $this->_premES    +=  $this->_premOption;
            $this->_sumES     =  $this->_sumInsOption; 
          }
         elseif($value=="emergencyassistancebahbuildings" || $value=="emergencyassistancebahstandalone"){
          	$this->_disEB     +=  $this->_netOption;
            $this->_iptEB     +=  $this->_iptOption;
            $this->_premEB    +=  $this->_premOption;
            $this->_sumEB     =  $this->_sumInsOption; 
          }
          
           //Zend_Debug::dump($this);
        }
        $this->_grosspremium=round($this->_policypremium+$this->_policyIPT,2);
        $this->_calCulateBalance();
        if($this->_policyname=="tenantsp"){
        	$this->_newtransaction->updateNewTranForTenant($this);
        
       		$this->_transaction->updateTranForTenant($this);
        }
        
        if($this->_policyname=="landlordsp"){
        	$this->_newtransaction->updateTranForLandlord($this);
        	$this->_transaction->updateTranForLandlord($this);
        }
        $this->_paymentTransaction->updatePaymentTransaction($this);
        
    /*
    * TODO reverse or refund disbursement
    */
        if($amount<0){
                    
        }
    }
    
     /**
     * Member function to set banked value
     * @param Double  
     * return void
     */
   
    public function createTermImage ($policynumber){
    
        /**
        * prepair data to build TermImage
        */
        $this->_policynumber=$policynumber;
        $this->_amount=0;
        $this->_months=12;
        $policyDisb = new Datasource_Insurance_LegacyPolicies();
        $policy = $policyDisb->getByPolicyNumber($policynumber);
        $this->_policyname = $policy->policyName;
        $termDisb = new Datasource_Insurance_Policy_Term();
        $term = $termDisb->getPolicyTerm($policynumber,$policy->startDate);
        $this->_startdate=$policy->startDate;
        $policy->termid= $term['id'];  
        $this->_getMult($policy);
        
        $policyOptionsArray = explode("|", $policy->policyOptions);
          
    	$pItemHist = new Datasource_Insurance_Policy_PolicyTermItemHist();
        foreach ($policyOptionsArray as $key => $value){
          $this->_initialise();
          $option=new Datasource_Insurance_Policy_Options($policy->policyType);
          
          $this->_optionID=$option->fetchOptionsByName($value);
         
          $this->_sumInsOption=$policyDisb->getPolicyOptionMatch($policy->policyOptions,$value,$policy->amountsCovered);
          $cover->sumInsured=$this->_sumInsOption;

          if($this->_sumInsOption>0){
          $cover = new Model_Insurance_Cover();
          $cover->policyOptionID=$this->_optionID;
          $cover->sumInsured=$this->_sumInsOption;   
          $this->_premOption=$policyDisb->getPolicyOptionMatch($policy->policyOptions,$value,$policy->optionPremiums)*$this->_mult;
          $this->_discOption=$policyDisb->getPolicyOptionMatch($policy->policyOptions,$value,$policy->optionDiscounts);
          $this->_calculateTax();
          $this->_calculateNet($policy,$value);
          $cover->grosspremium=$this->_premOption+$this->_iptOption;
          $cover->premium=$this->_premOption;
          $cover->netpremium=$this->_netOption;
          $cover->ipt=$this->_iptOption;
          $pItemHist->setItemHist($policy,$cover);
          }
         
        }     
    }
    
    
    public function setBanked($banked){
        
       $this->_banked=$banked;
    }
    /*
     calculate grosspremium
    */
     private function _calculateGrossprem($policy) {
        
         
         $policyPremArray=explode("|", $policy->optionPremiums);
         $prem=0;
         foreach ($policyPremArray as $key => $value){
            $prem += $value;
            
         }
        
         $this->_policypremium=round($prem*$this->_mult,2);
    }
    
    /*
     calculate agent commission
    */
     private function _calculateAgentComm($policy,$agent,$term) {
        
        $commRateDisb = new Datasource_Core_Agent_AgentCommissionRate();
        $commRate = $commRateDisb->getRate($agent,$term,$policy->startDate,$policy->policyType);
        $this->_agentcommission = round($this->_policypremium*$commRate,2);
        
    }
    /*
     calculate netpremium   
    */
     private function _calculateNet($policy, $optionName) {
     	
       $discount=1+($this->_discOption/100);
       if($discount<1) $discount=1;
       $net=0.000;
       if($this->_policyname=="landlordsp"){ 
      	 $pItem=new Datasource_Insurance_Policy_PolicyTermItemHist();
      	 $net=$pItem->getRowToDisb($this);
      }
       
       if($this->_policyname=="tenantsp"){
          $oDisb=new Datasource_Core_Disbursement_NetRate();
          $oDisbRate;
           if($optionName=="contentstp" or $optionName=="possessionsp"){
              $band=$this->_getBand();
              $oDisbRate=$oDisb->getNetRatebyOption($optionName,$policy->rateSetID,$policy->riskArea,$policy->startDate,$band);
              }
       		else{
      		  $oDisbRate=$oDisb->getNetRatebyOption($optionName,$policy->rateSetID,$policy->riskArea,$policy->startDate);
      		 }
          $net=$this->_sumInsOption*$oDisbRate;
      	  
       }
       
       $this->_netOption=round($net*$this->_months*$discount/12,4);
        
     }
     
     /*
     get band
     */
   
     private function _getBand(){
        $bandInfo=new Datasource_Insurance_Policy_PolicyOptionsBandLookup();
        $bandUplimit=array();
        $bandUplimit=$bandInfo->getBandbyOptionID($this->_optionID);
      
         if ($this->_sumInsOption <= $bandUplimit['upperLimitBandA']) {
                             return 'A';
         }
         if ($this->_sumInsOption > $bandUplimit['upperLimitBandA'] && $this->_sumInsOption <= $bandUplimit['upperLimitBandB']) {
                             return 'B';
         }
         if ($this->_sumInsOption > $bandUplimit['upperLimitBandB'] && $this->_sumInsOption <= $bandUplimit['upperLimitBandC']) {
                             return 'C';
         }
         if ($this->_sumInsOption > $bandUplimit['upperLimitBandC']) {
                             return 'D';
         }

      }
      
      /*
       calculate Tax
      */
      
      private function _calculateTax() {
        $policyDisb = new Datasource_Insurance_LegacyPolicies();
        $policy = $policyDisb->getByPolicyNumber($this->_policynumber);
        $postcode = $policy->propertyPostcode;
        
        $taxInfo=new Datasource_Core_Tax();
        $tax=$taxInfo->getTaxbyTypeAndPostcode('IPT',$postcode, $this->_startdate);
        $taxrate = $tax['rate'];
        
        $this->_iptOption=round($this->_premOption * ($taxrate / 100), 4);
      }
      
      /*
       calculate agent commission per option
      
      */
      
      private function _calculateAgentCommOption(){
        $this->_aCommOption=round($this->_agentcommission*$this->_premOption/$this->_policypremium,4);
       }
      /*
      store option disbursement
      
      */
      
      private function _storeOptionDisbursement(){
        
        $pItemInfo = new Datasource_Core_Disbursement_PaymentTransactionItem();
        $pItemInfo->saveDetails($this);
        $insurerInfo=new Datasource_Core_Disbursement_InsurerRate();
        $row=$insurerInfo->getInsurerRatebyDate($this->_optionID,$this->_startdate);
        $ea=array(4,28,29,30);
       foreach($row as $currentRow) {						
		if(!is_null($currentRow['insurerID'])){
		  if(in_array($this->_optionID,$ea)){
		  	if($currentRow['insurerID']==3){
		  		$this->_iptOptionIns=0;
		  	}
		  	else{
		  		$this->_iptOptionIns=$this->_iptOption;
		  	}
		  }
		  else{
		  	$this->_iptOptionIns=round($this->_iptOption*$currentRow['rate'],4);
		  }
          $this->_insurerID=$currentRow['insurerID'];
          $this->_netOptionIns=round($this->_netOption*$currentRow['rate'],4);
          $this->_premOptionIns=round($this->_premOption*$currentRow['rate'],4);          
          $this->_grossOptionIns=$this->_premOptionIns+$this->_iptOptionIns;
        
          $iItemInfo= new Datasource_Core_Disbursement_PaymentItemInsurer();
          $iItemInfo->saveDetails($this);
            
        }
       }         
      }
      
      /*
       calculate balance figure after disbursement 
      */
      
      private function _calCulateBalance() {
        $this->_hpc=$this->_amount-$this->_agentcommission-round($this->_policynetprem,2)-round($this->_policyIPT,2);
        $this->_income = $this->_hpc+$this->_banked;
        
      }
      
      /*
       initialise
      */
      
      private function _initialise() {
        $this->_netOption=0.00;
        $this->_iptOption=0.00;
        $this->_premOption=0.00;
        $this->_grossOption=0.00;
        $this->_sumInsOption=0.00;
        $this->_aCommOption=0.00;
        $this->_discOption=0;
        $this->_netOptionIns=0.00;
        $this->_iptOptionIns=0.00;
        $this->_premOptionIns=0.00;
        $this->_grossOptionIns=0.00;
        $this->_introCommOption=0.00;
           
      }
      
      /*
      get Mult
      */
      
      private function _getMult($policy) {
        if($policy->payBy=="Annually"){
          $this->_mult=$this->_months/$policy->policyLength;
        }
        else{
          $this->_mult=$this->_months;        
            
        }
        
      }
      
}



?>
