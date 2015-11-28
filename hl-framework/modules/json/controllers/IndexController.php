<?php
class Json_IndexController extends Zend_Controller_Action
{
	public function init() {
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		header('Content-type: application/json');
	}
	
	public function contactUsAction() {
        $filters =array(
            'name'      =>  'StringTrim',
            'tel'       =>  'StringTrim',
            'email'     =>  'StringTrim',
            'enquiry'   =>  'StringTrim');

		$validators = array (
			'name'      =>  'NotEmpty',
            'tel'       =>  'NotEmpty',
            'email'     =>  'NotEmpty',
            'enquiry'   =>  'NotEmpty');
        
		$input = new Zend_Filter_Input($filters, $validators, $_POST);
        $returnArray = array();
        
        if ($input->isValid()) {
			
            $emailer = new Application_Core_Mail();
            $params = Zend_Registry::get('params');
            $emailer->setTo($params->email->contactUs, 'HomeLet');
            $emailer->setFrom($input->email, $input->name);
            $emailer->setSubject('HomeLet - Contact Us Form');
            $bodyHtml  = 'Name : ' . $input->name . '<br />';
            $bodyHtml .= 'Email : ' . $input->email . '<br />';
            $bodyHtml .= 'Tel : ' . $input->tel . '<br />';
            $bodyHtml .= 'Enquiry : <pre>' . $input->enquiry . '</pre><br />';
            $emailer->setBodyHtml($bodyHtml);
            
            if ($emailer->send()) {
                // Email sent successfully
                $returnArray['success']=true;
                $returnArray['errorMessage']='';
            } else {
                $returnArray['success']=false;
                $returnArray['errorMessage']='Problem sending email.';
            }
        } else {
            $returnArray['success']=false;
            $returnArray['errorMessage']=$input->getMessages();
        }
        echo Zend_Json::encode($returnArray);
	}
	
	 /**
           * Validate and return address list for use via AJAX
           *
           * @return void
           */
	public function getpropertiesAction() {
            $output = array();
		
            // Filter input
            $inputPostcode = trim(preg_replace('/[^0-9a-z\ ]/i', '', $_POST['postcode']));
		
            if ($inputPostcode != '') {
                $postcode = new Manager_Core_Postcode();
                $addresses = $postcode->getPropertiesByPostcode($inputPostcode);
			
                $returnArray = array();
                foreach($addresses as $address) {
                    $returnArray[] = array(
                        'addressId' => $address['id'],
                        'addressLine' => $address['singleLineWithoutPostcode']
                    );
                }
			
                if (isset($returnArray[0]['addressId']) && $returnArray[0]['addressId'] != null && $returnArray[0]['addressId'] != '') {
                    if (preg_match('/^IM|^GY|^JE/i', $_POST['postcode']) && preg_match('/ins_postcode|ins_property_postcode/', $_POST['inputId'])) {
                        $output['data'] = array();
                        $output['restriction'] = 1;
                        $output['error'] = "Unfortunately we're unable to offer you a policy, as cover isn't available in the Channel Islands or the Isle of Man. If the property you're looking to insure isn't in the Channel Islands or the Isle of Man then please double check the post code you have entered. If you're still experiencing problems, or if you have any further queries or questions, please call us on 0845 117 6000.";
                    } else {
                        $output['data'] = $returnArray;
                        $output['error'] = '';
                    }
                } else {
                    $output['data'] = array();
                    $output['error'] = "Can't find address";
                }
            } else {
                $output['data'] = array();
                $output['error'] = 'Please enter a valid postcode';
            }
		
                echo Zend_Json::encode($output);
	}
	
	/**
	 * Get a specic property details 
	 *
	 */
	public function getpropertyAction() {
		$addressID = $_POST['addressID'];
		$postcode = new Manager_Core_Postcode();
		$address = $postcode->getPropertyByID($addressID);
		
		$returnArray = array();
		if (count($address)>0) {
			$line1 = '';
			
			// Build an array of up to 5 address lines
			$addressLines = array();
			if ($address['address1']!='') $addressLines[] = $address['address1'];
			if ($address['address2']!='') $addressLines[] = $address['address2'];
			if ($address['address3']!='') $addressLines[] = $address['address3'];
			if ($address['address4']!='') $addressLines[] = $address['address4'];
			if ($address['address5']!='') $addressLines[] = $address['address5'];
			
			// Now we can pop off of this address lines nonsense - to make it just 3 lines
			
			$line3 = array_pop($addressLines);
			$line2 = array_pop($addressLines);
			
			while (count($addressLines)>0) $line1 = array_pop($addressLines) . ', ' . $line1;
			if ($address['buildingName'] != '') $line1 = $address['buildingName'] . ' ' . $line1;
			if ($address['houseNumber'] != '') $line1 = $address['houseNumber'] . ' ' . $line1;
			if ($address['department'] != '') $line1 = $address['department'] . ', ' . $line1;
			if ($address['organisation'] != '') $line1 = $address['organisation'] . ', ' . $line1;
			
			$line1 = trim ($line1, ' ');
			$line1 = trim ($line1, ',');
			
			$postcode = $address['postcode'];
			
			$returnArray = array(
				'line1'			=>  ucwords(strtolower($line1)),
				'line2'			=>	ucwords(strtolower($line2)),
				'line3'			=>	ucwords(strtolower($line3)),
				'postcode'		=>	strtoupper($postcode),
				'landlordsRiskAreas'	=> $address['landlordsRiskAreas']
			);
			
			$output['data'] = $returnArray;
            $output['error'] = '';
		} else {
			$output['data'] = array();
            $output['error'] = "Can't find address";
		}
		
		echo Zend_Json::encode($output);
	}
	
	/**
     * Validate and return agent list for use via AJAX
     *
     * @return void
     */
    public function getagentsAction() {
        $output = array();
		
        $request = $this->getRequest();
        $postdata = $request->getPost();
		
        $agentLookup = new Datasource_Core_Agents();
	
        $output['data'] = $agentLookup->searchByAsnOrNameAndAddress(
            $postdata['letting_agent_asn'],
            $postdata['letting_agent_name'],
            $postdata['letting_agent_town']
        );
		
        $output['errorJs'] = '';
		$output['errorCount'] = 0;
        $output['errorHtml'] = '';
		
        echo Zend_Json::encode($output);

    }
    
    /**
     * Gets the details for a specific agent
     *
     * @return void
     */
    public function getagentdetailsAction() {
    	$output = array();
		
        $request = $this->getRequest();
        $postdata = $request->getPost();
		
        $agentLookup = new Datasource_Core_Agents();
	
        $output['data'] = $agentLookup->getDetailsByASN($postdata['letting_agent_asn']);
		
        $output['errorJs'] = '';
		$output['errorCount'] = 0;
        $output['errorHtml'] = '';
		
        echo Zend_Json::encode($output);
    }
}
?>
