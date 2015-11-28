<?php
class Manager_Core_Channel
{  
    /**
     * Get Channel Name
     *
     * @param string $policynumber     
     *
     * @return string
     */        
    public function getChannelName($policyNumber){ 
                              
        $channelManager = new Datasource_Insurance_Channel();        
        if($policyNumber) {             
            $channel = $channelManager->getChannelName($policyNumber);
        }
        return $channel;
    }
    
   /**
     * Set Channel Name
     *
     * @param string $policynumber
     * @param string $channel
     * @param int $isNewQuote  
     *
     * @return boolean true/false
     */
    public function setChannel($policyNumber, $channelOn, $isNewQuote){
      
        $quote = new Datasource_Insurance_LegacyQuotes();
        $quoteDetails = $quote->getByPolicyNumber($policyNumber);
  
        
        $params = Zend_Registry::get('params');
        $rateStartdate = $params->lip->rateStartdate ;
         

        if($quoteDetails->issueDate >= $rateStartdate){
            $channelManager = new Datasource_Insurance_Channel();
                       
            if (! $isNewQuote || !is_null($this->getChannelName($policyNumber))) {              
                if (strcasecmp($channelOn , 'Web') == 0) {                    
                    $channelManager->updateChannel($policyNumber,'web');
                } 
                if (strcasecmp($channelOn, 'IAS') == 0 || strcasecmp($channelOn, 'Connect') == 0) {
                    // Do Nothing;                    
                }
            } else {                
                $channelManager->insertChannel($policyNumber, $channelOn);            
            }
            
            return true;
        }
        return false;
    }
    
   
}
