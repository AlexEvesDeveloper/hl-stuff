<?php
class Datasource_Insurance_Policy_Cover extends Zend_Db_Table_Multidb
{
    /**
     * @var string table name
     */
    protected $_name = 'policyCover';

    /**
     * @var string primary id
     */
    protected $_id = 'policyCoverID';

    /**
     * @var string database identifier
     */
    protected $_multidb = 'db_legacy_homelet';
    
	/**
     * Set options
     *
     * @param string $policyNumber
     * @param array $optionsArray An array of policyNumber, policyOptionID, sumInsured & premium
     * @return void
     */
    public function setCover($policyNumber, $optionsArray)
    {
		// Remove existing cover amounts (we don't have a replace into command)
        $where = $this->quoteInto('policyNumber = ?', $policyNumber);
		$this->delete($where);
		
		// Insert new cover amounts
		foreach ($optionsArray as $options) {
			$this->insert($options, $where);
		}
    }
	
    /**
	 * Populate the policyCover table using old pipe-seperated values :(
	 * 
	 * @munt 7
	 */
	public function populateFromLegacy($policyNumber, $policyOptions, $amountsCovered, $optionPremiums)
    {
		if ($policyOptions != '') {
			$policyOptionsArray = explode('|', $policyOptions);
			$coverArray = explode('|', $amountsCovered);
			$premiumsArray = explode('|', $optionPremiums);
		
			// First we need to remove any policy cover items for this policy/quote
			$where = $this->quoteInto('policyNumber = ?', $policyNumber);
			$this->delete($where);
			
			$optionsLookup = new Datasource_Insurance_Policy_Options();
			
			$i = 0;
			foreach($policyOptionsArray as $policyOptionName) {
				$optionID = $optionsLookup->fetchOptionsByName($policyOptionName);
				
				$data = array(
					'policyOptionID' 	=> $optionID,
					'sumInsured'		=> $coverArray[$i],
					'premium'			=> $premiumsArray[$i],
					'policyNumber'		=> $policyNumber
				);
				$this->insert($data);
				$i++;
			}
		}
	}
    
    /**
	 * Description given in the IChangeable interface.
     *
     * @param string $quoteNumber
     * @param string|null $policyNumber
     * @return mixed
     */
    public function changeQuoteToPolicy($quoteNumber, $policyNumber = null)
    {
        //If policyNumber is empty then assume the QHLI should be replaced with PHLI.
		if (empty($policyNumber)) {
            $policyNumber = preg_replace('/^Q/', 'P', $quoteNumber);
		}
		
		$where = $this->quoteInto('policyNumber = ?', $quoteNumber);
		$updatedData = array('policyNumber' => $policyNumber);
		return $this->update($updatedData, $where);		
	}
}