<?php
    /**
    * Data Object. These probably need defining in CORE
    * TODO: when this gets merged into 1.5 Check to see if Pauls connect is using anything similar
    */
    class LettingAgents_Object_Contact {
    	
    	/**
    	 * 
    	 * _id, _agency_id and _uid are all required hence set to null to force code to setthem later
    	 * 
    	 */
		private $_id = NULL;
		private $_agency_id = NULL;
		private $_uid = NULL;
		private $_contact_name = "";

    
       	/**
       	 * 
       	 * @return 
       	 */
       	public function get_id()
       	{
       	    return $this->_id;
       	}

       	/**
       	 * 
       	 * @param $id
       	 */
       	public function set_id($id)
       	{
       	    $this->_id = $id;
       	}

       	
    	/**
       	 * 
       	 * @return 
       	 */
       	public function get_uid()
       	{
       	    return $this->_uid;
       	}
       	
       	/**
       	 * @param $uid
       	 */
       	public function set_uid($uid)
       	{
       		
       		$this->_uid = $uid;
       	}
       	
         public function get_agency_id(){
       		return $this->_agency_id;
       	}
       	
       	public function set_agency_id($agency_id){
       		$this->_agency_id = $agency_id;
       	}
       	
    	/**
		 * 
		 * @return 
		 */
		public function get_contact_name()
		{
		    return $this->_contact_name;
		}
	
		/**
		 * 
		 * @param $_contact_name
		 */
		public function set_contact_name($_contact_name)
		{
		    $this->_contact_name = $_contact_name;
		}       	
        
    	/**
    	 * 
    	 * Returns all the member variables as an array
    	 * @param &$props reference array, the properties of this object
    	 * @param $trim bool if true removes the left most _ on private variable names
    	 * 
    	 */
   	
    	public function toArray($props=array(), $trim = true){
    		
    		$props = array_merge($props,get_object_vars($this));
    		
    	//	Zend_Debug::dump($props);
    	    if($trim){
    			// Trim is set so remove any leading underscore
    			$temp = array(); // Temporary array
    			// cycle thru each element setting value as $value
    			reset($props);	
	    		foreach($props as $key => $value){
	    		//	echo "$key = $value<br>";
	    		    	if(strpos($key, "_") == 0){ 
	    					$key = substr_replace($key, "", 0,1); // yes, replace the underscore
	    					$temp[$key] = $value;
	    				}else{
	    					$temp[$key] = $value;
	    				}    				
	    		}
   			
    			$props = $temp;
    		}    	
    	//	Zend_Debug::dump($props); die("Contact");	
  			return $props;
		}
}
