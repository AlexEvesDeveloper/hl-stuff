<?php

/**
 * Manager class responsible for Credit payment logic
 *
 *
 */
class Manager_Core_Creditcard {
    
        public function getByRefNo($refNo){
        $directDebit = new Datasource_Core_CreditCard_Payment();
        return $directDebit->getByRefNo($refNo);
        
    }
}

?>