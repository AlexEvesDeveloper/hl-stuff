<?php

class Form_TenantsReferencingTracker_Login extends Zend_Form {

    /**
     * Valid IRIS login identifier
     */
    const IRIS_LOGIN = 'iris_login';

    /**
     * Define the login form elements
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');
        
        // Add letting agent scheme number element
        $this->addElement('text', 'letting_agent_asn', array(
            'label'      => '*Agent Scheme Number',
            'required'   => true,
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your Letting Agent\'s Scheme Number',
                            'notEmptyInvalid' => 'Please enter your Letting Agent\'s Scheme Number'
                        )
                    )
                )
            )
        ));
        
        // Add tenant reference number element
        $this->addElement('text', 'tenant_reference_number', array(
            'label'      => '*HomeLet Reference Number',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your HomeLet Reference Number',
                            'notEmptyInvalid' => 'Please enter your HomeLet Reference Number'
                        )
                    )
                )
            )
        ));
        
        // Add tenant date of birth element
        $this->addElement('text', 'tenant_dob', array(
            'label'     => '*Date of birth (dd/mm/yyyy)',
            'required'  => true,
            'filters'    => array('StringTrim')
        ));
        
        // Set decorators
        $this->clearDecorators();
        $this->setDecorators(array('Form'));
        $this->setElementDecorators(array ('ViewHelper', 'Label', 'Errors'));
        
        // Add the next button
        $this->addElement('submit', 'next', array(
            'ignore'   => true,
            'label'    => 'Submit',
        ));
        $next = $this->getElement('next');
        $next->clearDecorators();
        $next->setDecorators(array('ViewHelper'));
        
        /*
        // Add some CSRF protection ***** read up and check this works
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
        */
        
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'tenants-referencing-tracker/subforms/login.phtml'))
        ));
    }
    
    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     *
     * @return bool
     */
    public function isValid($formData = array()) {
        
        // Check if all 3 pieces of info are supplied
        $asn = (isset($formData['letting_agent_asn'])) ? preg_replace('/[^\d]/', '', $formData['letting_agent_asn']) : '';
        $irn = (isset($formData['tenant_reference_number'])) ? preg_replace('/[^\d]/', '', $formData['tenant_reference_number']) : '';
        $dob = (isset($formData['tenant_dob'])) ? preg_replace('/[^\d\/]/', '', $formData['tenant_dob']) : '';

        // If the IRN is not actually an IRN, but an IRIS reference number (i.e. prefixed with HLT)
        if (preg_match('/^HLT\d+$/', $formData['tenant_reference_number'])) {
            return self::IRIS_LOGIN;
        }

        if ($dob != '') {
            
            try {
                
                //Check for a valid date of birth. If this causes an exception, end
                //the process here rather than passing up to the overriden method,
                //otherwise the same exception will be thrown again.
                $isDobError = false;
                $dob = new Zend_Date($dob);
            }
            catch(Zend_Exception $e) {
                
                $isDobError = true;
            }
            
            if($isDobError) {
                $this->getElement('tenant_dob')->addError('Please provide a valid date of birth.');
                
                // Validate other fields, to keep the error messages consistent
                unset($formData['tenant_dob']);
                $dummy = parent::isValidPartial($formData);
                return false;
            }
        }
        
        //Continue the validation.
        $displayErrorMessage1 = false;
        $displayErrorMessage2 = false;
        $displayErrorMessage3 = false;
        $displayErrorMessage4 = false;
        
        if ($asn != '' && $irn != '' && $dob != '') {
            // Check if a record can be found
            $tatManager = new Manager_Referencing_Tat($irn);
            if ($tatManager->isLoginValid($asn, $dob)) {
                
                // Check if user allowed to login
                if (!$tatManager->isTatApplicable()) {
                    // Can't log in. Check if this is because the TAT has expired.
                    if($tatManager->isTatExpired()) {
                        
                        $displayErrorMessage1 = true;
                    }
                    else {
                        //If the reference is of type INSIGHT or XPRESS, then provide a specific error message.
                        $referenceManager = new Manager_ReferencingLegacy_Munt();
                        $reference = $referenceManager->getReference($irn);
                        $product = $reference->productSelection->product;
                        
                        if(isset($product->variables[Model_Referencing_ProductVariables::CREDIT_REFERENCE])) {
                            $displayErrorMessage2 = true;
                        }
                        else {
                            //Set the generic form-level error.
                            $displayErrorMessage3 = true;
                        }
                    }
                }
            }
            else {
                
                // Can't find a record, set a form-level error
                $displayErrorMessage3 = true;
            }
        }
        else {
            
            // One or more fields are empty (when filtered), set a form-level error
            $displayErrorMessage4 = true;
        }
        
        //Display the error message if appropriate.
        if($displayErrorMessage1) {
            $this->addError('Unfortunately we only hold information on the Tenant Application Tracker for 30 days and we believe
                             that your application was completed over a month ago. If you have any questions about your tenancy
                             application please speak directly with your letting agent.');
        }
        else if($displayErrorMessage2) {
            $this->addError('Oops, we\'re unable to match the information you\'ve entered, please check your details and try again.');
        }
        else if($displayErrorMessage3) {
            $this->addError('I\'m sorry we\'ve been unable to find your application with the details that you\'ve provided. Please check the details that you entered and try again.');
        }
        else if($displayErrorMessage4) {
            $this->addError('Please ensure you complete all 3 fields before selecting submit.');
        }
        
        // Call original isValid()
        return parent::isValid($formData);
    }
}
?>
