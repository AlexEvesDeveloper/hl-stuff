<?php

/**
 * Datasource for the Channel table. 
 */
class Datasource_Insurance_Channel extends Zend_Db_Table_Multidb {
    
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'channels';
    protected $_primary = 'policynumber';
    

    /**
     * Get channel ID related to a particular quote/policy Number
     *
     * @param string policyNumber
     * 
     * @param string channel id
     */
    public function getChannelId($channelName) {
        try {
            $select = $this->select()
                                    ->setIntegrityCheck(false)
                                    ->from(array('t' => 'channel_types'))
                                    ->where('channel = ?', $channelName);

            $channel = $this->fetchRow($select);
        
	    if(!is_null($channel)) {
            	return $channel->id;
	    } else {
		return null;	
	    }
        } catch (Exception $e) {
            throw new Zend_Exception('getChannelId - Couldn\'t select channel id...');
        }
    }
 
   /**
     * Get channel name related to a particular policyNumber
     *
     * @param string policyNumber
     * 
     * @param string channel name
     */
    public function getChannelName($policyNumber) {
        try {
            $select = $this->select()
                                    ->setIntegrityCheck(false)
                                    ->from(array('c' => $this->_name) )
                                    ->join(array('t' => 'channel_types'), 'c.channel_id = t.id')
                                    ->where('policynumber = ?', $policyNumber);
            $channel = $this->fetchRow($select);
              
	    if(!is_null($channel)) {
            	return $channel->channel;
	    } else { 
		return null; 
	    }
        } catch (Exception $e) {
            throw new Zend_Exception('getChannelName - Couldn\'t select channel name...');
        }
    }
      
   /**
     * Update channel related to a particular policyNumber
     *
     * @param string policyNumber
     * @param string channelName
     * 
     * @param string channel
     */
    public function updateChannel($policyNumber, $channelName) {

        $channelId = $this->getChannelId($channelName);
        
        try {
            $data = array(
			'policynumber'	=> $policyNumber,
			'channel_id'	=> $channelId			
		);
            $where = $this->quoteInto('policynumber = ?', $policyNumber);
            $update = $this->update($data, $where);
            
            return $update;
       } catch (Exception $e) {
            throw new Zend_Exception('updateChannel - Couldn\'t update channel...');
       }                       
    }
    
   /**
     * Insert new channel related to a particular policyNumber
     *
     * @param string policyNumber
     * @param string channel
     * 
     * @param string channel
     */
    public function insertChannel($policyNumber, $channelName) {
     
        $channelId = $this->getChannelId($channelName);
        
        try {
            $data = array(
			'policynumber'	=> $policyNumber,
			'channel_id'	=> $channelId			
		);
         
            return $this->insert($data);
        } catch (Exception $e) {
            throw new Zend_Exception('insertChannel - Couldn\'t insert channel...');
        }
    }
    
    /**
     * Update channel field to change a quote number to policy number.
     */
    public function changeQuoteToPolicy($quoteNumber, $policyNumber = null) {
		
	//If policyNumber is empty then assume the QHLI should be replaced with PHLI.
	if(empty($policyNumber)) {		
		$policyNumber = preg_replace('/^Q/', 'P', $quoteNumber);
	}
	
        try {
            $data = array('policynumber' => $policyNumber);
            $where = $this->quoteInto('policynumber = ?', $quoteNumber);
	
            return $this->update($data, $where);
         } catch (Exception $e) {
                throw new Zend_Exception('changeQuoteToPolicy - Couldn\'t conver quote to policy...');
         }
    }
    
}
?>
