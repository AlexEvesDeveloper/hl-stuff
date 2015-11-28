<?php

/**
 * Manager class responsible for Direct debit payment logic
 */
class Manager_Core_Directdebit {

    /**
     * Saves the direct debit to the dd table 
     *
     * @param Model_Insurance_Directdebit $data
     *
     * @return void
     *
     * Look Ma!!!!, I'm Documenting
     */
    public function save($data){
        $directDebit = new Datasource_Core_DirectDebit_Payment();
        /*
         We will need a payment refno 
        */
        $paymentRefno = new Datasource_Core_NumberTracker();
        
        $data->paymentRefNo = $paymentRefno->getNextPaymentRefNumber();
#        $data->paymentDate = date("Y-m-d");
        /*
         Save that bad boy
        */
        $directDebit->saveDetails($data);
        
    }
    
    public function getPaymentRefNo($refNo){
        $directDebit = new Datasource_Core_DirectDebit_Payment();
        return $directDebit->getPaymentRefNo($refNo);
    }
    
    public function getByRefNo($refNo){
        $directDebit = new Datasource_Core_DirectDebit_Payment();
        return $directDebit->getByRefNo($refNo);
        
    }
    
}
?>