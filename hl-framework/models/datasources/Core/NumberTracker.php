<?php

/**
 * Model definition for generating a new policy number
 *
 * Here there be dragons!!! Those with a weak disposition or low tolerance to bad code should run away now.
 *
 * History:
 * Allegedly... "The legacy PERL system fetches the number to use as the next policy number, and tries to write back a
 * incremented by one value as the new nextnumber, should this fail the process is repeated 'ad nauseum'"
 *
 * However... this doesn't appear to be the case!

 *
*/
class Datasource_Core_NumberTracker extends Zend_Db_Table_Multidb
{
    /**
     * Next Policy Number identifier for getNextNumberByPolicyName
     */
    const NEXT_POLICY_NUMBER_IDENTIFIER  = 'nextpolicynumber';

    /**
     * Agent Scheme Number identifier for getNextNumberByPolicyName
     */
    const AGENT_SCHEME_NUMBER_IDENTIFIER = 'agentschemeno';

    /**
     * Next Payment Reference Number identifier for getNextNumberByPolicyName
     */
    const NEXT_PAYMENT_REFNO_IDENTIFIER  = 'nextpaymentrefno';

    /**
     * @var string table name
     */
    protected $_name = 'nextnumber';

    /**
     * @var string primary key
     */
    protected $_primary = 'nextpolicynumber';

    /**
     * @var string database
     */
    protected $_multidb = 'db_legacy_homelet';
    protected $_currentNumber;
    
    /**
     * Returns current chosen policy number as a string and increments value in 
     * db table.
     * 
     * @todo this should replace the next two methods below.
     * 
     * Currently works for:
     *  - nextpolicynumber
     *  - agentschemeno
     *  - nextpaymentrefno
     *  - nextpaymentrefno
     */
    public function getNextNumberByPolicyName($policy)
    {
        // Gatekeeper to ensure query integrity
        if (in_array($policy, 
            array(
                'nextpolicynumber', 'agentschemeno', 'nextpaymentrefno', 'nextpaymentrefno'
            )
        )) {
            $row = $this->fetchRow($this->select()->from(
                $this->_name, array('PolicyNumber' => $policy)
            ));
            $this->_currentNumber = $row->toArray();
            $this->update(
                array($policy => $this->_currentNumber['PolicyNumber'] + 1),
                $this->quoteInto(
                    $policy . ' = ?', $this->_currentNumber['PolicyNumber']
                )
            );
        }
        return (string)$this->_currentNumber['PolicyNumber'];
    }
        
    /**
     * @deprecated Please use getNextNumberByPolicyName
     * @todo Replace this with getNextNumberByPolicyName?
     */
    public function getNextPolicyNumber() {
        
        //Retrieve the nextpolicynumber, which will be used to identify the
        //new quote/policy.
        $fields = array('PolicyNumber' => 'nextpolicynumber');
        
        $select = $this->select()
            ->from($this->_name, $fields);
        $row = $this->fetchRow($select);
        $this->_currentNumber =  $row->toArray();
        
        
        //Update the nextnumber table with a new nextpolicynumber.
        $data = array('nextpolicynumber'=> $this->_currentNumber['PolicyNumber']+1);
        $where = $this->quoteInto('nextpolicynumber = ?', $this->_currentNumber['PolicyNumber']);
                
        $this->update($data, $where);
        return (string)$this->_currentNumber['PolicyNumber'];
    }
    
    /**
     * @deprecated Please use getNextNumberByPolicyName
     * @todo Replace this with getNextNumberByPolicyName?
     */
    public function getNextPaymentRefNumber() {
        
        //Retrieve the nextpolicynumber, which will be used to identify the
        //new quote/policy.
        $fields = array('NextRefNumber' => 'nextpaymentrefno');
        
        $select = $this->select()
            ->from($this->_name, $fields);
        $row = $this->fetchRow($select);
        $this->_currentNumber =  $row->toArray();
        
        
        //Update the nextnumber table with a new nextpaymentrefno.
        $data = array('nextpaymentrefno'=> $this->_currentNumber['NextRefNumber']+1);
        $where = $this->quoteInto('nextpaymentrefno = ?', $this->_currentNumber['NextRefNumber']);
                
        $this->update($data, $where);
        return $this->_currentNumber['NextRefNumber'];
    }
    
    /**
     * get Next policy number is an odd one, the old system used a text file to store the next number
     * and then appended a random number to it and prepended the text UWP
     *
     * This will do the same except the number will be stored in the next number table, hey-ho
     * @return string This is the new UWP number format UWPNNNN_NNNNN
     *
     * @deprecated Please use getNextNumberByPolicyName
     * @todo Replace this with getNextNumberByPolicyName?
     */
    public function getNextPortfolioNumber() {
        
        //Retrieve the next portfolio number, which will be used to identify the
        //new quote/policy.
        $fields = array('NextPortfolioNumber' => 'nextportfoliono');
        
        $select = $this->select()
            ->from($this->_name, $fields);
        $row = $this->fetchRow($select);
        $this->_currentNumber =  $row->toArray();
        
        
        //Update the nextnumber table with a new nextpaymentrefno.
        $data = array('nextportfoliono'=> $this->_currentNumber['NextPortfolioNumber']+1);
        $where = $this->quoteInto('nextportfoliono = ?', $this->_currentNumber['NextPortfolioNumber']);
                
        $this->update($data, $where);
        return "UWP".$this->_currentNumber['NextPortfolioNumber']. '_' .rand(9999, 100000);
    }

}
?>