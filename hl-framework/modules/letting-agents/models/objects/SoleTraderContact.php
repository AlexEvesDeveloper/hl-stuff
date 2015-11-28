<?php
    /**
    * Data Object. These probably need defining in CORE
    * TODO: when this gets merged into 1.5 Check to see if Pauls connect is using anything similar
    */
    class LettingAgents_Object_SoleTraderContact extends LettingAgents_Object_PartnershipContact{
    	
    	/**
    	 * 
    	 * _id, _agency_id and _uid are all required hence set to null to force code to setthem later
    	 * 
    	 */

		private $_ni_number = "";
		private $_passport_number = "";
		private $_contact_email = "";
		private $_contact_number = "";
    
 
       	/**
       	 * 
       	 * @return 
       	 */
       	public function get_ni_number()
       	{
       	    return $this->_ni_number;
       	}

       	/**
       	 * 
       	 * @param $ni_number
       	 */
       	public function set_ni_number($ni_number)
       	{
       	    $this->_ni_number = $ni_number;
       	}

       	/**
       	 * 
       	 * @return 
       	 */
       	public function get_passport_number()
       	{
       	    return $this->_passport_number;
       	}

       	/**
       	 * 
       	 * @param $passport_number
       	 */
       	public function set_passport_number($passport_number)
       	{
       	    $this->_passport_number = $passport_number;
       	}

 
	
		/**
		 * 
		 * @return 
		 */
		public function get_contact_number()
		{
		    return $this->_contact_number;
		}
	
		/**
		 * 
		 * @param $_contact_number
		 */
		public function set_contact_number($_contact_number)
		{
		    $this->_contact_number = $_contact_number;
		}
	
		/**
		 * 
		 * @return 
		 */
		public function get_contact_email()
		{
		    return $this->_contact_email;
		}
	
		/**
		 * 
		 * @param $_contact_email
		 */
		public function set_contact_email($_contact_email)
		{
		    $this->_contact_email = $_contact_email;
		}  
		      
        /**
         * 
         * @deprecated Use toArray()
         */
		public function getAll(){
			return $this->toArray();
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
			//Zend_Debug::dump($props);die("Soletrader");
			return $props;
		}
}
