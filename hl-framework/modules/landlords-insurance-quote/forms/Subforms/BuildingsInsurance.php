<?php

class LandlordsInsuranceQuote_Form_Subforms_BuildingsInsurance extends Zend_Form_SubForm
{
    /**
     * Create insured address subform
     *
     * @return void
     */
    public function init() {

        // Add need buildings insurance element
        $this->addElement('radio', 'need_building_insurance', array(
            'label' => 'Do you need buildings insurance?',
            'required' => true,
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
                            'isEmpty' => 'Please select if you need buildings insurance or not',
                            'notEmptyInvalid' => 'Please select if you need buildings insurance or not'
                        )
                    )
                )
            )
        ));

        // Add 500k + question
        $this->addElement('radio', 'override_dsi', array(
            'label' => 'Do you need cover for a rebuild value <strong>over</strong> &pound;500,000?',
            'required' => false,
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'separator' => '',
            'label_placement' => 'prepend',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select if you need cover for a rebuild value over &pound;500,000',
                            'notEmptyInvalid' => 'Please select if you need cover for a rebuild value over &pound;500,000'
                        )
                    )
                )
            )
        ));


        $this->addElement('text', 'building_value', array(
            'label' => 'A rebuild value is required. Please enter a rebuild value.',
            'required' => false,
            'attribs' => array(
                'class' => 'currency'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please tell us the rebuild value of your property',
                            'notEmptyInvalid' => 'Please tell us the rebuild value of your property'
                        )
                    )
                )
            ),
            'data-required' => 'required',
            'data-validate' => 'validate',
            'data-type' => 'currency',
            'class' => 'form-control',
        ));

        // Add a filter suitable for currency input - this strips anything after a decimal point and then anything
        //   non-digit such as pound symbols and commas
        $buildingValue = $this->getElement('building_value');
        $buildingValue->addFilter('callback', function($v) {
            return preg_replace(array('/\..+$/', '/\D/'), array('', ''), $v);
        });

        $this->addElement('select', 'building_built', array(
            'label' => 'Approximately when was your property built?',
            'required' => true,
            'multiOptions' => array(
                ''  => '--- please select ---',
                'older' => 'Before 1850',
                '1850-1899' => '1850-1899',
                '1900-1919' => '1900-1919',
                '1920-1945' => '1920-1945',
                '1946-1979' => '1946-1979',
                '1980-1990' => '1980-1990',
                '1991-2000' => '1991-2000',
                '2001+' => '2001+'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please tell us when your property was built',
                            'notEmptyInvalid' => 'Please tell us when your property was built'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));
        
        $this->addElement('select', 'building_bedrooms', array(
            'label' => 'How many bedrooms does your property have?',
            'required' => true,
            'multiOptions' => array(
                ''  => '--- please select ---',
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '5+'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please tell us how many bedrooms your property has',
                            'notEmptyInvalid' => 'Please tell us how many bedrooms your property has'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));
        
        $this->addElement('select', 'building_type', array(
            'label' => 'Which description best suits your property type?',
            'required' => true,
            'multiOptions' => array(
                '' => '--- please select ---',
                'Bungalow' => 'Bungalow',
                'Detached' => 'Detached',
                'Semi-Detached' => 'Semi-Detached',
                'Terraced' => 'Terraced',
                'Other' => 'Other'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please tell us what type of property you are insuring',
                            'notEmptyInvalid' => 'Please tell us what type of property you are insuring'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        $this->addElement('select', 'building_insurance_excess', array(
            'label' => 'What level of Excess would you like?',
            'required' => true,
            'multiOptions' => array(
                '' => '--- please select ---',
                '0' => html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8').'0',
                '100' => html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8').'100',
                '250' => html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8').'250',
                '500' => html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8').'500',
                '1000' => html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8').'1000'
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
        
        $this->addElement('radio', 'building_accidental_damage', array(
            'label' => 'Would you like cover for Accidental Damage?',
            'required' => true,
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
            array('ViewScript', array('viewScript' => 'subforms/building-insurance.phtml'))
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Grab view and add the agent lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $view->headScript()->appendFile(
            '/assets/landlords-insurance-quote/js/buildingInsurance.js',
            'text/javascript'
        );
        
        $this->addPrefixPath('Application_Form_Element', 'Application/Form/Element', 'ELEMENT');
    }
    
    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     *
     * @return bool
     */
    public function isValid($formData = array()) {
        if (isset($formData['need_building_insurance']) && $formData['need_building_insurance'] == 'yes') {
            // User has said they want building insurance so we need to make the extra information mandatory
            $this->getElement('building_built')->setRequired(true);
            $this->getElement('building_bedrooms')->setRequired(true);
            $this->getElement('building_type')->setRequired(true);
            $this->getElement('building_insurance_excess')->setRequired(true);
            $this->getElement('building_accidental_damage')->setRequired(true);
            // If landlord chooses to over-ride the DSI value we need to make sure they enter a manual value in
            $this->getElement('override_dsi')->setRequired(true);
            if (isset($formData['override_dsi']) && $formData['override_dsi'] == 1) {
                $this->getElement('building_value')->setRequired(true);
                // Need to add a validator to make sure they enter a value over 500k
                $this->getElement('building_value')->clearValidators();
                $minValueValidator = new Zend_Validate_GreaterThan(array('min' => 500000));
                $minValueValidator->setMessage(
                    'Please enter a building value above &pound;500k',
                    Zend_Validate_GreaterThan::NOT_GREATER
                );
                $this->getElement('building_value')->addValidator($minValueValidator);

            }
            else {
                $this->getElement('building_value')->setRequired(false);

                // They haven't chosen to over-ride but if we don't have a DSI then we still need them to enter one
                $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
                if(isset($pageSession->quoteID)) {
                    $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($pageSession->quoteID);
                    $dsi = $quoteManager->calculateDSI();
                    if ($dsi==0) {
                        $this->getElement('building_value')->setRequired(true);
                        // Need to add a validator as minimum value is 50k
                        $this->getElement('building_value')->clearValidators();
                        $minValueValidator = new Zend_Validate_GreaterThan(array('min' => 50000));
                        $minValueValidator->setMessage(
                            'Please enter a building value above &pound;50k',
                            Zend_Validate_GreaterThan::NOT_GREATER
                        );
                        $this->getElement('building_value')->addValidator($minValueValidator);
                    }
                }
            }

        }
        else {
            $this->getElement('building_built')->setRequired(false);
            $this->getElement('building_bedrooms')->setRequired(false);
            $this->getElement('building_type')->setRequired(false);
            $this->getElement('building_insurance_excess')->setRequired(false);
            $this->getElement('building_accidental_damage')->setRequired(false);
        }

        return parent::isValid($formData);
    }
}
