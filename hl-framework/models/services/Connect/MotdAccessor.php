<?php

/**
 * Class for remotely retrieving MOTDs.
 */
class Service_Connect_MotdAccessor {
    
    /**
     * Retrieves all active MOTDs.
     *
     * @return array
     * An associative array containing the details of the active MOTDs, or null
     * if there are no active MOTDs.
     */
    public function getActiveMotds() {
        
        $returnArray = array();
        
        //Retrieve all MOTDs.
        $motds = new Datasource_Cms_Connect_Motd();
        $motdsArray = $motds->getAll();
        
        foreach($motdsArray as $currentMotd) {
            
            if($currentMotd['active'] == 1) {

                //Ensure the today is captured in the MOTD date range.
                $displayFrom = new Zend_Date($currentMotd['displayFrom'], Zend_Date::ISO_8601);
                $displayTo = new Zend_Date($currentMotd['displayTo'], Zend_Date::ISO_8601);
                $now = Zend_Date::now();
                
                if($displayFrom->isToday() || $displayTo->isToday()) {
                    
                    $returnArray[] = $currentMotd;
                }
                else if($now->isLater($displayFrom) && $now->isEarlier($displayTo)) {
                        
                    $returnArray[] = $currentMotd;
                }
            }
        }
        
        
        //Clean up the return value consistent with this function's contract.
        if(empty($returnArray)) {
            
            $returnVal = null;
        }
        else {
            
            $returnVal = $returnArray;
        }

        return $returnVal;
    }
}

?>