<?php
    class LettingAgents_Object_AgencyEmail{
    	private $_id;
    	private $_agency_id;
    	private $_address_type;
    	private $_email_address;
    
        	public function getId()
        	{
        	    return $this->_id;
        	}

        	public function setId($id)
        	{
        	    $this->_id = $id;
        	}

        	public function getAgency_id()
        	{
        	    return $this->_agency_id;
        	}

        	public function setAgency_id($agency_id)
        	{
        	    $this->_agency_id = $agency_id;
        	}

        	public function getAddress_type()
        	{
        	    return $this->_address_type;
        	}

        	public function setAddress_type($address_type)
        	{
        	    $this->_address_type = $address_type;
        	}

        	public function getEmail_address()
        	{
        	    return $this->_email_address;
        	}

        	public function setEmail_address($email_address)
        	{
        	    $this->_email_address = $email_address;
        	}
        	
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