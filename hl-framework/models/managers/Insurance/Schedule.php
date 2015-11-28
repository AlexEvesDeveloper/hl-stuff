<?php
/**
 * Manager Class to provide logic for Schedule
 * 
 * This object write to the schdule table
 *  For Monthly DD: The current month the schedule is zero the next month is doubled and the remaining 10 are single payments
 *  For Monthly CC:
 *  For Annual DD:
 *  For Annual CC:
 *  Fees come from the Fees Object
 *  Payment refno comes from the cc or dd table depending on payment type
 */
class Manager_Insurance_Schedule {
    protected $_scheduleModel;
    
    /**
    * Save a new schedule record,
    * This function needs various bit of information to create a schedule entry
    * @param String $refNo Policy reference number
    */
    public function save($refNo, $quote){
    #$session = new Zend_Session_Namespace('tenants_insurance_quote');
    $scheduleObject = new Model_Insurance_Schedule();
    
    // Get the quote data
   # $quote = new Manager_Insurance_TenantsContentsPlus_Quote($refNo);
   
     // Get policy start date
    list($year, $month, $day) = explode("-",date("Y-m-d",strtotime($quote->getStartDate())));
            
    // This is the month the policy is incepted, there will be no payment on this day (0.00)
    $startMonth = date("F", strtotime("$year-$month-$day"));
            
    // This is the month that first payment is made, this will be 2x monthly value
    $firstPayMonth = date("F", mktime(0,0,0,($month++ > 12) ? 1 : $month , $day, $year));
    //Get the payment Refernce Number
    $scheduleObject->policyNumber = $quote->getPolicyNumber();

    if (strtolower($quote->getPayBy()) == "monthly"){
        /**
         * Direct Debit monthly set the current month to 0, next month is doubled remaining months to a single premium
         * ddfee set to Fees value
         * 
         **/
	         
	        // TENANTS FEES
	        $feesObject = new Model_Insurance_Fee();
	   		$feesObject  = $quote->getFees();
    	 $scheduleObject->ddFee = $feesObject->monthlyFeeSP;
    	 
     	if($quote->getPolicyName() == "landlordsp"){
	     		$quoteManager = new Manager_Insurance_LandlordsPlus_Quote($quote->getID());
	     		$landlordsFees = $quoteManager->getFees();
	     		$scheduleObject->ddFee = $landlordsFees['landlords_insurance_plus_monthly_admin'];
            }
        if( strtolower($quote->getPayMethod()) == "directdebit" || strtolower($quote->getPayMethod()) == "dd" ){
            $ddPayment = new Manager_Core_Directdebit();
            // Get Payment data no from dd table
            $ddData = $ddPayment->getByRefNo($refNo);
            // Set Payment refno
            $scheduleObject->paymentRefNo = $ddData->paymentRefNo;
            // Set dd Fee
            
            // Iterate thru the months and apply the monthly payment
            while(key($scheduleObject->months)){
                $current = key($scheduleObject->months);
                if($current != strtolower($startMonth) && $current != strtolower($firstPayMonth) ){
                    $scheduleObject->months[$current] = round($quote->getPolicyQuote(),2);
            
                }
                if($current == strtolower($firstPayMonth)){
                    $scheduleObject->months[$current] = round($quote->getPolicyQuote() * 2,2);
            
                }
                next($scheduleObject->months);
            }
        }
        
        /**
         * Creditcard sets this month to zero remaining months to a single payment and sets banked to one payment value
         * ddfee get set to fees value
         **/
        elseif( strtolower($quote->getPayMethod()) == "creditcard" || strtolower($quote->getPayMethod()) == "cc"  ){
            $ccPayment = new Manager_Core_CreditCard();
            // Get Payment data no from cc table
            $ccData = $ccPayment->getByRefNo($refNo);
            // Set Payment refno
            $scheduleObject->paymentRefNo = $ccData->paymentRefNo;
            // Set cc Fee
            
            $amount = $quote->getPolicyQuote();
            
            
            // This is the month the policy is incepted, there will be no payment on this day (0.00)
            // Iterate thru the months and apply the monthly payment
            while(key($scheduleObject->months)){
                $current = key($scheduleObject->months);
                if($current != strtolower($startMonth)){
                    $scheduleObject->months[$current] = round($amount,2);
                }
                next($scheduleObject->months);
            }
                   
        }
    }else{
        /* Annual Stuff happens here */
        /**
         * Direct Debit annual set next month payment to full ammount
         **/
        if( strtolower($quote->getPayMethod()) == "directdebit" || strtolower($quote->getPayMethod()) == "dd"  ){
            $ddPayment = new Manager_Core_Directdebit();
            // Get Payment data no from dd table
            $ddData = $ddPayment->getByRefNo($refNo);
            // Set Payment refno
            $scheduleObject->paymentRefNo = $ddData->paymentRefNo;
            // Set Next month to full ammount
            $current= strtolower($firstPayMonth);        
            
	            $scheduleObject->months[$current] = round($quote->getPolicyQuote(),2);
	            
        }
        elseif( strtolower($quote->getPayMethod()) == "creditcard" || strtolower($quote->getPayMethod()) == "cc"  ){
            /**
             *  Credit Card annual sets all months to zero and Banked to full ammount
             **/
            $ccPayment = new Manager_Core_CreditCard();
            // Get Payment data no from cc table
            $ccData = $ccPayment->getByRefNo($refNo);
            // Set Payment refno
            $scheduleObject->paymentRefNo = $ccData->paymentRefNo;
            // Set firstPayment as banked
	            $amount = $quote->getPolicyQuote();
                  
                  
        }
    }
    
    $schedule = new Datasource_Insurance_Schedules();
    $schedule->insertNew($scheduleObject);
    }

}
?>
