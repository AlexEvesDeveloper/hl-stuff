<?php
    /**
    * Data Object to represent a record in the datasouce
    */
    class LettingAgents_Object_AgentApplication {
		/**
		 * The id of each record.
		 * primary key, unique
		 * @var Integer
		 */ 
    	private $_id;

    	/**
    	 * 
    	 * Unique id generated by php uniqid()
    	 * @var string
    	 */
    	private $_uid;
    	
    	/**
    	 * 
    	 * Has the applicant been a client before
    	 * @var Bool
    	 */
    	private $_is_previous_client = "";
    	
    	/**
    	 * 
    	 * Any Current campaign code. we don't currenly use this in any senible way 
    	 * @var string
    	 */
    	private $_campaign_code = "";
    	
    	/**
    	 * Legal entity name of company
    	 * @var string
    	 */
    	private $_legal_name = "";
    	
    	/** 
    	 * 
    	 * Name the company trades as
    	 * @var string
    	 */   	
    	private $_trading_name = "";
    	
    	
    	/**
    	 * Oraganisation type, key to organisation_type table
    	 * @var Integer
    	 */
    	private $_organisation_type = 0;
    	
    	/**
    	 * Date the company was establised
    	 * @var mixed 
    	 */
    	private $_date_established = "";
    	
    	/**
    	 * Is the company associated with another letting agency question, 
    	 * this will be 1 for yes or zero for no 
    	 * @var bool
    	 *
    	 */
    	private  $_is_associated = 0;
    	
    	/**
    	 * The text entered when the $is_associated is true 
    	 * @var string
    	 */
    	private $_associated_text = "";
    	
		/**
		 * 
		 * Company registration number
		 * @var string
		 */    	
    	private $_company_registration_number = "";
    	
    	private $_current_referencing_supplier = "";
    	private $_number_of_branches = "";
    	private $_number_of_employees = "";
    	private $_number_of_lets = "";
    	private $_number_of_landlords = "";
    	private $_fax_number = "";
    	private $_company_website_address="";
    			
        private $_contact_email = "";
		private $_contact_number = "";
		private $_contact_name = "";
    	private $_application_type ="";

    	
    
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
		 * 
		 * @param $uid
		 */
		public function set_uid($uid)
		{
		    $this->_uid = $uid;
		}
	
		/**
		 * 
		 * @return 
		 */
		public function get_is_previous_client()
		{
		    return $this->_is_previous_client;
		}
	
		/**
		 * 
		 * @param $_is_previous_client
		 */
		public function set_is_previous_client($_is_previous_client)
		{
		    $this->_is_previous_client = $_is_previous_client;
		}
	
		/**
		 * 
		 * @return 
		 */
		public function get_campaign_code()
		{
		    return $this->_campaign_code;
		}
	
		/**
		 * 
		 * @param $_campaign_code
		 */
		public function set_campaign_code($_campaign_code)
		{
		    $this->_campaign_code = $_campaign_code;
		}
	
		/**
		 * 
		 * @return 
		 */
		public function get_legal_name()
		{
		    return $this->_legal_name;
		}
	
		/**
		 * 
		 * @param $_legal_name
		 */
		public function set_legal_name($_legal_name)
		{
		    $this->_legal_name = $_legal_name;
		}
	
		/**
		 * 
		 * @return 
		 */
		public function get_trading_name()
		{
		    return $this->_trading_name;
		}
	
		/**
		 * 
		 * @param $_trading_name
		 */
		public function set_trading_name($_trading_name)
		{
		    $this->_trading_name = $_trading_name;
		}
	
		/**
		 * 
		 * @return 
		 */
		public function get_organisation_type()
		{
		    return $this->_organisation_type;
		}
	
		/**
		 * 
		 * @param $_organisation_type
		 */
		public function set_organisation_type($_organisation_type)
		{
		    $this->_organisation_type = $_organisation_type;
		}
	
		/**
		 * 
		 * @return 
		 */
		public function get_date_established()
		{
		    return $this->_date_established;
		}
	
		/**
		 * 
		 * @param $_date_established
		 */
		public function set_date_established($_date_established)
		{
		    $this->_date_established = $_date_established;
		}
	
		/**
		 * 
		 * @return 
		 */
		public function get_is_associated()
		{
		    return $this->_is_associated;
		}
	
		/**
		 * 
		 * @param $_is_associated
		 */
		public function set_is_associated($_is_associated)
		{
		    $this->_is_associated = $_is_associated;
		}
	
		/**
		 * 
		 * @return 
		 */
		public function get_associated_text()
		{
		    return $this->_associated_text;
		}
	
		/**
		 * 
		 * @param $_associated_text
		 */
		public function set_associated_text($_associated_text)
		{
		    $this->_associated_text = $_associated_text;
		}

		
		/**
		 *  @return
		 */
		public function get_company_registration_number()
		{
			return $this->_company_registration_number;
		}
		
		/**
		 * @param $company_registration_number
		 */
		public function set_company_registration_number($company_registration_number)
		{
			$this->_company_registration_number = $company_registration_number;
		}
		
		public function get_current_referencing_supplier()
		{
		    return $this->_current_referencing_supplier;
		}

		public function set_current_referencing_supplier($_current_referencing_supplier)
		{
		    $this->_current_referencing_supplier = $_current_referencing_supplier;
		}

		public function get_number_of_branches()
		{
		    return $this->_number_of_branches;
		}

		public function set_number_of_branches($_number_of_branches)
		{
		    $this->_number_of_branches = $_number_of_branches;
		}

		public function get_number_of_employees()
		{
		    return $this->_number_of_employees;
		}

		public function set_number_of_employees($_number_of_employees)
		{
		    $this->_number_of_employees = $_number_of_employees;
		}

		public function get_number_of_lets()
		{
		    return $this->_number_of_lets;
		}

		public function set_number_of_lets($_number_of_lets)
		{
		    $this->_number_of_lets = $_number_of_lets;
		}

		public function get_number_of_landlords()
		{
		    return $this->_number_of_landlords;
		}

		public function set_number_of_landlords($_number_of_landlords)
		{
		    $this->_number_of_landlords = $_number_of_landlords;
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
		 * @param $_general_email
		 */
		public function set_contact_email($_contact_email)
		{
		    $this->_contact_email = $_contact_email;
		}
		public function get_fax_number()
		{
		    return $this->_fax_number;
		}

		public function set_fax_number($_fax_number)
		{
		    $this->_fax_number = $_fax_number;
		}

		public function get_company_website_address()
		{
		    return $this->_company_website_address;
		}

		public function set_company_website_address($_company_website_address)
		{
		    $this->_company_website_address = $_company_website_address;
		}
		
    	/**
		* 
		* @return 
		*/
		public function get_application_type()
		{
		    return $this->_application_type;
		}
	
		/**
		 * 
		 * @param $application_type
		 */
		public function set_application_type($application_type)
		{
		    $this->_application_type = $application_type;
		}

		
    	/**
    	 * @deprecated
    	 * Enter description here ...
    	 */
		public function getAll(){
    		$returnArray = array();
    		$returnArray['id'] = $this->get_id();
    		$returnArray['uid'] = $this->get_uid();
    		$returnArray['is_previous_client'] = $this->get_is_previous_client();
			$returnArray['campaign_code'] = $this->get_campaign_code();
			$returnArray['legal_name'] = $this->get_legal_name();
			$returnArray['trading_name'] = $this->get_trading_name();
			$returnArray['organisation_type'] = $this->get_organisation_type();
			$returnArray['date_established'] = $this->get_date_established();
			$returnArray['is_associated'] = $this->get_is_associated();
			$returnArray['associated_text'] = $this->get_associated_text();
			$returnArray['company_registration_number'] = $this->get_company_registration_number();
			
			return $returnArray;
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