<?php

class TenantsInsuranceQuote_Form_Subforms_LettingAgent extends Zend_Form_SubForm
{
    /**
     * Create letting agent details subform
     *
     * @return void
     */
    public function init()
    {
        // Add have a letting agent element
        $this->addElement('radio', 'letting_agent_has', array(
            'label'     => 'Do you have a HomeLet Letting Agent?',
            'required'  => true,
            'multiOptions' => array(
                'yes' => 'Yes',
                'no' => 'No'
            ),
            'separator' => '',
            'label_placement' => 'prepend',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select if you have a HomeLet Letting Agent',
                            'notEmptyInvalid' => 'Please select if you have a HomeLet Letting Agent'
                        )
                    )
                )
            )
        ));

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
                        'messages' => 'Letting Agent\'s name must contain at least three alphanumeric characters and only basic punctuation (space, hyphen, comma, round brackets, full stop, single quote, ampersand, exclamation mark, percent, asterisk, plus, forward slash, semi-colon, commercial at, square brackets, underscore and backtick)'
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
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/letting-agent.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        

        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Grab view and add the agent lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headScript()->appendFile(
            '/assets/tenants-insurance-quote/js/agentLookup.js',
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
        // Need to load the letting agents into the drop down or it fails validation
        // with an invalid id - by default zend does an inArray check on select boxes
        
        if (array_key_exists('letting_agent_asn', $formData)) {
            $agentLookup = new Datasource_Core_Agents();
            
            $agentList = $agentLookup->searchByAsnOrNameAndAddress(
                $formData['letting_agent_asn'],
                $formData['letting_agent_name'],
                $formData['letting_agent_town']
            );
            
            $agentDropdownValues = array();
            if ($agentList) {
                foreach ($agentList as $agent) {
                    $agentDropdownValues[$agent['asn']] = $agent['name'] . ', '. $agent['address'];
                }
            }
            $agentDropdown = $this->letting_agent;
            $agentDropdown->setMultiOptions($agentDropdownValues);
            $agentDropdown->setRequired(true);
        }
        
        // Check if letting_agent_agent is 'Yes', if so then either:
        //   letting_agent_name AND letting_agent_town are mandatory, OR
        //   letting_agent_asn is mandatory
        if (isset($formData['letting_agent_has']) && $formData['letting_agent_has'] == 'yes') {
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
        } elseif(isset($formData['letting_agent_has']) && $formData['letting_agent_has'] == 'no') {
            // The user has said they don't have a letting agent so ignore all the validation
            // TODO: This is a dirty fix to QC4364 - this code is so complex and dirty that I can't actually find
            //       why it's throwing weird haystack validation errors. Needs re-factoring.
            return true;
        }

        // Call original isValid()
        return parent::isValid($formData);

    }
}