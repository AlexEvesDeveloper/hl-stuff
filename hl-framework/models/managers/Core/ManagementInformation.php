<?php

/**
 * Manager class responsible handeling Management Information data models
 *
 * More MI specific models to go here
 */
class Manager_Core_ManagementInformation {

    /**
     * Save a sigle record to the GeneratedBy table
     * @param $data array The data to be saved
     */
    public function saveGeneratedBy($data){       
        $d = new Datasource_Core_ManagementInformation_GeneratedBy();
        $d->save($data);        
    }
    
    
    /**
    * Save the Answer to the Marketing Question
    * 
    */
    public function saveMarketingAnswers($policyNumber, $refno, $answer){
        $d= new Datasource_Core_ManagementInformation_MarketingAnswers();
        $d->save($policyNumber, $refno, $answer);
    }
    
    /**
     * Get the Answer to the Marketing Question
     * @param string $policyNumber
     * @param string $refno
     */
    public function getMarketingAnswers($policyNumber) {
        $d= new Datasource_Core_ManagementInformation_MarketingAnswers();
        return $d->getAnswer($policyNumber);
    }
}
?>