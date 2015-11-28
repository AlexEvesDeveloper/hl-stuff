<?php

/**
 * Holds a single underwriting endorsement.
 */
class Model_Insurance_Endorsement extends Model_Abstract {

	protected $_id;
	protected $_policyNumber;
	protected $_endorsementType;
	protected $_excess;
	protected $_dateOn;
	protected $_dateOff;
	protected $_effectiveDate;
	
	
	public function __construct() {
		
		//Default values for the optional aspects of an endorsement.
		$this->_excess = new Zend_Currency(
			array(
				'value' => 0,
				'precision' => 2
			));
		
		$this->_dateOn = new Zend_Date();
		$this->_dateOff = '0000-00-00';
		$this->_effectiveDate = new Zend_Date();
		$this->_endorsementType = new Model_Insurance_EndorsementType();
	}
	
	public function getID() {
		
		return $this->_id;
	}
	
	public function getPolicyNumber() {
		
		return $this->_policyNumber;
	}
	
	public function getEndorsementType() {
		
		return $this->_endorsementType;
	}
	
	/**
	 * Getter for the excess.
	 *
	 * Method which returns the endorsement excess as a Zend_Currency
	 * object.
	 *
	 * @return Zend_Currency
	 * The excess encapsulated in a Zend_Currency object.
	 */
	public function getExcess() {
		
		return $this->_excess;
	}
	
	/**
	 * Getter for the date on.
	 *
	 * Returns the date on which this endorsement was created, encapsulated
	 * in a Zend_Date object.
	 *
	 * @return Zend_Date
	 * Always returns a Zend_Date representing the date this policy
	 * endorsment was added, or the date it was created.
	 */
	public function getDateOn() {
		
		return $this->_dateOn;
	}
	
	/**
	 * Getter for date off.
	 *
	 * Returns the date on which this endorsement no longer applies, encapsulated
	 * in a Zend_Date object. Will return null if this is not known.
	 *
	 * @return Zend_Date
	 * Returns a Zend_Date representing the date this policy
	 * endorsment no longer applies, or null if not known.
	 */
	public function getDateOff() {
		
		return $this->_dateOff;
	}
	
	/**
	 * Getter for effective date.
	 *
	 * Returns the date on which this endorsement comes into effect, encapsulated
	 * in a Zend_Date object.
	 *
	 * @return Zend_Date
	 * Always returns a Zend_Date representing the date this policy
	 * endorsment comes into effect.
	 */
	public function getEffectiveDate() {
		
		return $this->_effectiveDate;
	}
	
	public function setID($id) {
		
		$this->_id = $id;
	}
	
	public function setPolicyNumber($policyNumber) {
		
		$this->_policyNumber = $policyNumber;
	}
	
	public function setEndorsementType($endorsementType) {
		
		$this->_endorsementType = $endorsementType;
	}
	
	/**
	 * Setter for the endorsement excess.
	 *
	 * This method sets the endorsement excess to the Zend_Currency
	 * value passed in.
	 *
	 * @param Zend_Currency $excess
	 * Represents the endorsement excess.
	 */
	public function setExcess($excess) {
		
		if(!is_a($excess, 'Zend_Currency')) {
			
			throw new Zend_Exception(get_class() . __FUNCTION__ . ": invalid argument specified.");
		}
		$this->_excess = $excess;
	}
	
	/**
	 * Sets the date on which the endorsement was added.
	 *
	 * Function which sets the date on which the endorsement was
	 * added to the quote or policy. Accepts arguments of type
	 * Zend_Date only.
	 *
	 * @param Zend_Date $dateOn
	 * A Zend_Date representation of when the endorsement was added
	 * to the quote or policy.
	 *
	 * @throws Exception
	 * Throws an Exception if the $dateOn is not a Zend_Date.
	 */
	public function setDateOn($dateOn) {
		
		if(!is_a($dateOn, 'Zend_Date')) {
			
			throw new Exception("Argument of incorrect type received.");
		}
		$this->_dateOn = $dateOn;
	}
	
	/**
	 * Sets the date on which the endorsement no longer applies.
	 *
	 * Function which sets the date on which the endorsement no
	 * longer applies to the quote or policy. Accepts arguments of type
	 * Zend_Date or null only.
	 *
	 * @param Zend_Date $dateOff
	 * A Zend_Date representation of when the endorsement ceases to apply,
	 * or null if not known.
	 *
	 * @throws Exception
	 * Throws an Exception if the $dateOff is not a Zend_Date or null.
	 */
	public function setDateOff($dateOff) {
		
		if(is_a($dateOff, 'Zend_Date') || ($dateOff == null)) {
			
			$this->_dateOff = $dateOff;
		}
		else {
			
			throw new Exception("Argument of incorrect type received.");
		}
	}
	
	/**
	 * Sets the date on which the endorsement applies.
	 *
	 * Function which sets the date on which the endorsement on a quote or
	 * policy comes into effect. Accepts arguments of type Zend_Date only.
	 *
	 * @param Zend_Date $effectiveDate
	 * A Zend_Date representation of when the endorsement comes into force.
	 *
	 * @throws Exception
	 * Throws an Exception if the $effectiveDate is not a Zend_Date.
	 */
	public function setEffectiveDate($effectiveDate) {
		
		if(!is_a($effectiveDate, 'Zend_Date')) {
			
			throw new Exception("Argument of incorrect type received.");
		}
		$this->_effectiveDate = $effectiveDate;
	}
}

?>