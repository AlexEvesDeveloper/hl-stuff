<?php

class LandlordsInsuranceQuote_Form_Subforms_LettingAgent extends Zend_Form_SubForm
{
    /**
     * Create letting agent details subform
     *
     * @return void
     */
    public function init()
    {
        // Add letting agent name element
        $this->addElement('text', 'letting_agent_name', array(
            'label'      => 'What\'s your Letting Agent\'s name?',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your Letting Agent\'s name',
                            'notEmptyInvalid' => 'Please enter your Letting Agent\'s name'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z\ \-\,\(\)\.\'\&\!\%\*\+\/\;\@\[\]\_\`]{3,}$/i',
                        'messages' => 'Letting Agent\'s name must contain at least three alphanumeric characters and only basic punctuation (space, hyphen, comma, round brackets, full stop, single quote and ampersand)'
                    )
                )
            )
        ));

        // Add letting agent town element
        $this->addElement('text', 'letting_agent_town', array(
            'label'      => 'And in which town are they based?',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your Letting Agent\'s town',
                            'notEmptyInvalid' => 'Please enter your Letting Agent\'s town'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\ \-]{3,}$/i',
                        'messages' => 'Letting Agent\'s town must contain at least three alphabetic characters and only basic punctuation (space and hyphen)'
                    )
                )
            )
        ));

        // Add letting agent scheme number element
        $this->addElement('text', 'letting_agent_asn', array(
            'label'      => 'Or, what is their agent scheme number?',
            'required'   => false,
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your Letting Agent\'s Scheme Number',
                            'notEmptyInvalid' => 'Please enter your Letting Agent\'s Scheme Number'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^\d{5,}$/',
                        'messages' => 'Letting Agent\'s Scheme Number must contain at least five digits'
                    )
                )
            )
        ));

        // Add letting agent select element
        $this->addElement('select', 'letting_agent', array(
            'label'     => 'Please select your Agent from the list',
            'required'  => false,
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your Letting Agent',
                            'notEmptyInvalid' => 'Please select your Letting Agent'
                        )
                    )
                )
            ),
            'multiOptions' => array(
                '' => '--- please select ---'
            )
        ));
        
        $this->getElement('letting_agent')->setRegisterInArrayValidator(false);
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/letting-agent.phtml'))
        ));

        // Strip all tags to prevent XSS errors - done iteratively so not to overwrite any existing filters
        foreach($this->getElements() as $element) {
            $element->addFilter('StripTags');
        }

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Grab view and add the agent lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headScript()->appendFile(
            '/assets/landlords-insurance-quote/js/agentLookup.js',
            'text/javascript'
        );

    }

    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     *
     * @return bool
     */
     public function isValid($formData = array()) {
        // If the ASN, Name and Town are all entered - the form is technically
        //   valid - but we need to check to make sure the ASN is actually a
        //   valid one.
        if (isset($formData['letting_agent_asn']) && $formData['letting_agent_asn'] != '') {
            $agentDatasource = new Datasource_Core_Agents();
            $agent = $agentDatasource->getDetailsByASN($formData['letting_agent_asn']);

            if (count($agent) == 0) {
                // Call the parent isValid to generate other error messages
                parent::isValid($formData);
                $this->getElement('letting_agent_asn')->addError('Not a valid Agent Scheme Number');
                // Return invalid
                return false;
            }
        }
        
        // Check if letting_agent_agent is 'Yes', if so then either:
        //   letting_agent_name AND letting_agent_town are mandatory, OR
        //   letting_agent_asn is mandatory
        if (isset($formData['have_letting_agent']) && $formData['have_letting_agent'] == 'yes') {
            // Set all three to mandatory first
            $this->getElement('letting_agent_name')->setRequired(true);
            $this->getElement('letting_agent_town')->setRequired(true);
            $this->getElement('letting_agent_asn')->setRequired(true);
            // Decide which can be un-mandatory
            if (trim($formData['letting_agent_asn']) != '') {
                $this->getElement('letting_agent_name')->setRequired(false);
                $this->getElement('letting_agent_town')->setRequired(false);
            }
            if (trim($formData['letting_agent_name']) != '' && trim($formData['letting_agent_town']) != '') {
                $this->getElement('letting_agent_asn')->setRequired(false);
            }
        } elseif(isset($formData['have_letting_agent']) && $formData['have_letting_agent'] == 'no') {
            // The user has said they don't have a letting agent so ignore all the validation
            // TODO: This is a dirty fix to QC4364 - this code is so complex and dirty that I can't actually find
            //       why it's throwing weird haystack validation errors. Needs re-factoring.            
            return true;
        }

        // Call original isValid()
        return parent::isValid($formData);

    }
}
