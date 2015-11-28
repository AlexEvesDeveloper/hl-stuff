<?php
    /**
    * Data Object. These probably need defining in CORE
    * TODO: when this gets merged into 1.5 Check to see if Pauls connect is using anything similar
    */
    class LettingAgents_Object_PartnershipContact extends LettingAgents_Object_Contact {
    	
		private $_birth_date = "";
		private $_address1 = "";
		private $_address2 = "";
		private $_address3 = "";
		private $_postcode = "";

    
 		
		/**
       	 * 
       	 * @return 
       	 */
       	public function get_birth_date()
       	{
       	    return $this->_birth_date;
       	}

       	/**
       	 * 
       	 * @param $birth_date
       	 */
       	public function set_birth_date($birth_date)
       	{
       	    $this->_birth_date = $birth_date;
       	}	

		/**
       	 * 
       	 * @param $address1
       	 */
       	public function set_address1($address1)
       	{
       	    $this->_address1 = $address1;
       	}

       	/**
       	 * 
       	 * @return 
       	 */
       	public function get_address2()
       	{
       	    return $this->_address2;
       	}

       	/**
       	 * 
       	 * @param $address2
       	 */
       	public function set_address2($address2)
       	{
       	    $this->_address2 = $address2;
       	}

       	/**
       	 * 
       	 * @return 
       	 */
       	public function get_address3()
       	{
       	    return $this->_address3;
       	}

       	/**
       	 * 
       	 * @param $address3
       	 */
       	public function set_address3($address3)
       	{
       	    $this->_address3 = $address3;
       	}

       	/**
       	 * 
       	 * @return 
       	 */
       	public function get_postcode()
       	{
       	    return $this->_postcode;
       	}

       	/**
         * 
         * @param $postcode
         */
        public function set_postcode($postcode)
        {
            $this->_postcode = $postcode;
        }

        
    	/**
    	 * 
    	 * Returns all the member variables as an array
    	 * @param &$props reference array, the properties of this object
    	 * @param $trim bool if true removes the left most _ on private variable names
    	 * 
    	 */
    	
    	public function toArray($props=array(),$trim = true){
    		#Zend_Debug::dump($props);
    		$props = array_merge($props,get_object_vars($this));
    		
			$props = parent::toArray($props);
		//	Zend_Debug::dump($props);// die("Partnership");
			return $props;
  			 
		}
		
}
