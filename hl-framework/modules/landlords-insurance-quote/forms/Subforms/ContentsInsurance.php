<?php

class LandlordsInsuranceQuote_Form_Subforms_ContentsInsurance extends Zend_Form_SubForm
{
    /**
     * Create insured address subform
     *
     * @return void
     */
    public function init() {
        // Add need contents insurance element
        $this->addElement('radio', 'need_contents_insurance', array(
            'label'     => 'Do you need contents insurance?',
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
                            'isEmpty' => 'Please select if you need contents insurance or not',
                            'notEmptyInvalid' => 'Please select if you need contents insurance or not'
                        )
                    )
                )
            )
        ));
        
        // Add is property furnished insurance element
        $this->addElement('radio', 'property_furnished', array(
            'label'     => 'Is your property furnished?',
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
                            'isEmpty' => 'Please tell us if your property is furnished or not',
                            'notEmptyInvalid' => 'Please tell us if your property is furnished or not'
                        )
                    )
                )
            )
        ));
        
        // Add cover amount element
        $this->addElement('text', 'contents_amount', array(
            'label'      => 'Amount to be insured (please enter a value above &pound;10,000)',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the amount you would like to insure'
                        )
                    )
                ),
                array(
                    'GreaterThan', true, array(
                        'min' => 9999,
                        'messages' => array(
                        	'notGreaterThan' => 'Contents cover amount must be at least &pound;10,000'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'currency',
                'class' => 'currency form-control',
            )
        ));

        // Add a filter suitable for currency input - this strips anything after a decimal point and then anything
        //   non-digit such as pound symbols and commas
        $contentsAmount = $this->getElement('contents_amount');
        $contentsAmount->addFilter('callback', function($v) {
            return preg_replace(array('/\..+$/', '/\D/'), array('', ''), $v);
        });

        $this->addElement('select', 'contents_excess', array(
            'label'     => 'What level of Excess would you like?',
            'required'  => true,
            'multiOptions' => array(
                ''  => '--- please select ---',
                '0' 	=> html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8').'0',
                '100' 	=> html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8').'100',
                '250' 	=> html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8').'250',
                '500' 	=> html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8').'500',
                '1000' 	=> html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8').'1000'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please choose a level of excess',
                            'notEmptyInvalid' => 'Please choose a level of excess'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));
        
        $this->addElement('radio', 'contents_accidental_damage', array(
            'label'     => 'Would you like cover for Accidental Damage?',
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
                            'isEmpty' => 'Please select if you need accidental damage cover or not',
                            'notEmptyInvalid' => 'Please select if you need accidental damage cover or not'
                        )
                    )
                )
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/contents-insurance.phtml'))
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Strip all tags to prevent XSS errors - done iteratively so not to overwrite any existing filters
        foreach($this->getElements() as $element) {
            $element->addFilter('StripTags');
        }

        // Grab view and add the agent lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $view->headScript()->appendFile(
            '/assets/landlords-insurance-quote/js/contentsInsurance.js',
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
        if (isset($formData['need_contents_insurance']) && $formData['need_contents_insurance'] == 'yes') {
            // User wants contents insurance - so we need to know if the property is furnished
            $this->getElement('property_furnished')->setRequired(true);

            if (isset($formData['property_furnished']) && $formData['property_furnished'] == 'yes') {
                // User has said they want contents insurance and the property
                // is furnished - so we need to make the extra information mandatory
                $this->getElement('contents_amount')->setRequired(true);
                $this->getElement('contents_excess')->setRequired(true);
                $this->getElement('contents_accidental_damage')->setRequired(true);
            }
            else {
                // Property is unfurnished so we don't need to know any of the extra data
                $this->getElement('contents_amount')->setRequired(false);
                $this->getElement('contents_excess')->setRequired(false);
                $this->getElement('contents_accidental_damage')->setRequired(false);
                unset($formData['contents_amount']);
                unset($formData['contents_excess']);
                unset($formData['contents_accidental_damage']);
            }
        }
        else {
            // Make the extra data optional as they haven't said they want contents insurance
            $this->getElement('property_furnished')->setRequired(false);
            $this->getElement('contents_amount')->setRequired(false);
            $this->getElement('contents_excess')->setRequired(false);
            $this->getElement('contents_accidental_damage')->setRequired(false);
            unset($formData['contents_amount']);
            unset($formData['contents_excess']);
            unset($formData['contents_accidental_damage']);
        }

        // Call original isValid()
        return parent::isValid($formData);
    }
}