<?php
/**
 * Class for Li+ channel trigger points
 *
 */
class Service_Core_ChannelAccessor {
    
    public function getChannelName($policyNumber) {       
        $core_channel = new Manager_Core_Channel();
        return $core_channel->getChannelName($policyNumber);
    }
    
    public function setChannel($policyNumber, $channelOn, $isNewQuote) {
        $core_channel = new Manager_Core_Channel();
        return $core_channel->setChannel($policyNumber, $channelOn, $isNewQuote);
    }
    
}
