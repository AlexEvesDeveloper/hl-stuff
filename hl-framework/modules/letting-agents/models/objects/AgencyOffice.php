<?php
    /**
    * Data Object. These probably need defining in CORE
    * TODO: when this gets merged into 1.5 Check to see if Pauls connect is using anything similar
    */
    class LettingAgents_Object_AgencyOffice{
    	/*
    	 * id
    	 */
		private $_id;
		
		/**
		 * Unique id
		 */
		private $_uid;
		
		/**
		 * Agency Id
		 */
		private $_agency_id;
		
		/**
		 * Office Type
		 */
		private $_office_type;
		
		/**
		 * Address Line 1 
		 * 
		 */
		private $_address_1;
		
		/**
		 * Address Line 2 
		 * 
		 */
		private $_address_2;
		
		/**
		 * Address Line 3
		 * 
		 */
		private $_address_3;
		
		/**
		 * Postcode
		 */
		private $_postcode;
		
		/**
		 * Phone number
		 */
		//private $_phone_number;
		
		/**
		 * Fax number
		 */
		//private $_fax_number;
		
		/**
		 * email address
		 */
		//private $_email_address;  	
		
		

    	/**
    	 * 
    	 * Get id
    	 * @return Integer
    	 */
		public function get_id()
    	{
    	    return $this->_id;
    	}

    	/**
    	 * 
    	 * Set id
    	 * @param Integer $_id
    	 */
    	public function set_id($_id)
    	{
    	    $this->_id = $_id;
    	}

    	/**
    	 * 
    	 * Get unique id
    	 * @return Integer
    	 */
    	public function get_uid()
    	{
    	    return $this->_uid;
    	}

    	/**
    	 * 
    	 * Set Unique id
    	 * @param string $_uid
    	 */
    	public function set_uid($_uid)
    	{
    	    $this->_uid = $_uid;
    	}

    	/**
    	 * Get Agency_id
    	 * @return integer
    	 */
    	public function get_agency_id()
    	{
    	    return $this->_agency_id;
    	}

    	/**
    	 * 
    	 * set agency_ig
    	 * @param integer $_agency_id
    	 */
    	public function set_agency_id($_agency_id)
    	{
    	    $this->_agency_id = $_agency_id;
    	}

    	/**
    	 * 
    	 * Get office type
    	 * @return integer
    	 */
    	public function get_office_type()
    	{
    	    return $this->_office_type;
    	}

    	/**
    	 * 
    	 * Set office type
    	 * @param Integer $_office_type
    	 */
    	public function set_office_type($_office_type)
    	{
    	    $this->_office_type = $_office_type;
    	}

    	/**
    	 * 
    	 * Get Address Line 1
    	 * @return string
    	 */
    	public function get_address_1()
    	{
    	    return $this->_address_1;
    	}

    	/**
    	 * 
    	 * Set address line 1
    	 * @param string $_address_1
    	 */
    	public function set_address_1($_address_1)
    	{
    	    $this->_address_1 = $_address_1;
    	}

    	/**
    	 * 
    	 * get address line 2
    	 * @return string
    	 */
    	public function get_address_2()
    	{
    	    return $this->_address_2;
    	}

    	/**
    	 * 
    	 * Set address line 2
    	 * @param String $_address_2
    	 */
    	public function set_address_2($_address_2)
    	{
    	    $this->_address_2 = $_address_2;
    	}

    	/**
    	 * 
    	 * get address line 3
    	 * @return string
    	 */
    	public function get_address_3()
    	{
    	    return $this->_address_3;
    	}

    	/**
    	 * 
    	 * Set address line 3
    	 * @param string $_address_3
    	 */
    	public function set_address_3($_address_3)
    	{
    	    $this->_address_3 = $_address_3;
    	}

    	/**
    	 * 
    	 * Get postcode
    	 * @return string
    	 */
    	public function get_postcode()
    	{
    	    return $this->_postcode;
    	}

    	/**
    	 * 
    	 * Set postcode
    	 * @param string $_postcode
    	 */
    	public function set_postcode($_postcode)
    	{
    	    $this->_postcode = $_postcode;
    	}

    	/**
    	 * 
    	 * Get phone number
    	 * @return string 
    	 */
    	public function get_phone_number()
    	{
    	    return $this->_phone_number;
    	}

    	/**
    	 * 
    	 * Set phone number
    	 * @param string $_phone_number
    	 */
    	public function set_phone_number($_phone_number)
    	{
    	    $this->_phone_number = $_phone_number;
    	}

    	/**
    	 * 
    	 * Get fax number
    	 * return string
    	 */
    	public function get_fax_number()
    	{
    	    return $this->_fax_number;
    	}

    	/**
    	 * 
    	 * Set fax number
    	 * @param string $_fax_number
    	 */
    	public function set_fax_number($_fax_number)
    	{
    	    $this->_fax_number = $_fax_number;
    	}

    	/**
    	 * 
    	 * Get email address
    	 * @return string
    	 */
    	public function get_email_address()
    	{
    	    return $this->_email_address;
    	}

    	/**
    	 * 
    	 * Set email address
    	 * @param string $_email_address
    	 */
    	public function set_email_address($_email_address)
    	{
    	    $this->_email_address = $_email_address;
    	}
    	
    	// Convert the object member variables to an array
    	/**
    	 * 
    	 * Returns all the member variables as an array
    	 * @param &$props reference array, the properties of this object
    	 * @param $trim bool if true removes the left most _ on private variable names
    	 * 
    	 */
    	
    	public function toArray(&$props,$trim = true){
			#$props = array();
			$name = "";
		    $reflect = new ReflectionClass($this);
  			  foreach ($reflect->getProperties() as $prop) {
  			  	$name  = $prop->getName();
  			  	$r = $reflect->getProperty($name);
  			  	$r->setAccessible(true);
  			  	if($trim && strpos($name, "_") == 0){
  			  		// Remove the first char is its an _ 
  			  		$name = substr_replace($name, "", 0,1);
  			  	}
  			  	
  			  	$props[$name] = $r->getValue($this);
  			  }	
		}     	
}