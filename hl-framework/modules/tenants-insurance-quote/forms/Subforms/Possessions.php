<?php

class TenantsInsuranceQuote_Form_Subforms_Possessions extends Zend_Form_SubForm
{
    public $gender = 'unknown';

    /**
     * Create possessions subform
     *
     * @return void
     */
    public function init()
    {
        $params = Zend_Registry::get('params');

        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        $possession = new Datasource_Insurance_Policy_SpecPossessions($pageSession->PolicyNumber);

        $maximumReached = ($possession->countPossessions() == $possession->maxPossessions);

        if (isset($pageSession->CustomerRefNo)) {
            $customerManager = new Manager_Core_Customer();
            $customer = $customerManager->getCustomer(
                Model_Core_Customer::LEGACY_IDENTIFIER,
                $pageSession->CustomerRefNo);

            switch($customer->getTitle('title')) {

                case 'Mr':
                case 'Sir':
                    $this->gender = 'male';
                    break;
                case 'Mrs':
                case 'Ms':
                case 'Miss':
                    $this->gender = 'female';
                    break;
                default:
                    $this->gender = 'unknown';
                    break;
            }
        }

        // Add taking items from house element
        $this->addElement('radio', 'away_from_home', array(
            'label'     => 'Would you like to cover your belongings that you take away from the home?',
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
                            'isEmpty' => 'Please select whether you take items away from the home',
                            'notEmptyInvalid' => 'Please select whether you take items away from the home'
                        )
                    )
                )
            )
        ));

        // Add possessions cover select element
        $this->addElement('select', 'possessions_cover', array(
            'label' => 'Cover level',
            'required'  => false,
            'multiOptions' => array(
                '' => '--- please select ---',
                '1000' => html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8'). '1000',
                '2000' => html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8'). '2000',
                '4000' => html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8'). '4000',
                '6000' => html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8'). '6000'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a level of cover',
                            'notEmptyInvalid' => 'Please select a level of cover'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        // Add above x amount element
        $this->addElement('radio', 'above_x', array(
            'label' => 'Would you like to cover any individual items that can be taken out and about that are worth more than &pound;1,000?',
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
                            'isEmpty' => 'Please select if you would like to cover any individual items with a value of more than &pound;1,000',
                            'notEmptyInvalid' => 'Please select if you would like to cover any individual items with a value of more than &pound;1,000'
                        )
                    )
                )
            )
        ));
        

        // Add possessions cover select element
        $this->addElement('select', 'possession_categoryId', array(
            'label' => 'Category',
            'required'  => false,
            'multiOptions' => $possession->listCategories(),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a category',
                            'notEmptyInvalid' => 'Please select a category'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        // Add fields for new possession types and values (the visible fields associated with the "Add possession" button)
        $this->addElement('text', 'possession_description', array(
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a description of the possession',
                            'notEmptyInvalid' => 'Please enter a description of the possession'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z\ \-\,\(\)\.\']{3,}$/i',
                        'messages' => 'Possession description must contain at least three alphanumeric characters and only basic punctuation (space, hyphen, comma, round brackets, full stop and single quote)'
                    )
                )
            ),
            'attribs' => array(
                'data-validate' => 'validate',
                'data-required' => 'required',
                'class' => 'form-control',
            )
        ));
        
        $this->addElement('text', 'possession_value', array(
            'label' => "Item value",
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the value of the possession',
                            'notEmptyInvalid' => 'Please enter the value of the possession'
                        )
                    )
                ),
                array(
                    'GreaterThan', true, array(
                        'min' => $params->uw->mv->tenantsp->specPossession,
                        'messages' => 'Possession value must be above &pound;1,000'
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'currency',
                'class' => 'form-control',
            )
        ));

        // Add a filter suitable for currency input - this strips anything non-digit and non-decimal point such as pound
        //   symbols and commas
        $possessionValue = $this->getElement('possession_value');
        $possessionValue->addFilter('callback', function($v) {
            return preg_replace('/[^\d\.]/', '', $v);
        });

        if ($maximumReached) {
            // Disable the possessions entry forms
            $this->possession_description->setAttrib('disabled', 'disabled');
            $this->possession_jewellery->setAttrib('disabled', 'disabled');
            $this->possession_value->setAttrib('disabled', 'disabled');
        }

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/possessions.phtml'))
        ));

        // Strip all tags to prevent XSS errors - done iteratively so not to overwrite any existing filters
        foreach($this->getElements() as $element) {
            $element->addFilter('StripTags');
        }

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headScript()->appendFile(
            '/assets/tenants-insurance-quote/js/possessions.js',
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

        $session = new Zend_Session_Namespace('homelet_global');
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        
        // Check if someone said "Yes" to wanting cover for possessions, but hasn't added any, and if so make fields mandatory
        if (isset($formData['above_x']) && $formData['above_x'] == 'yes') {
            if (trim($formData['possession_categoryId']) != '' || trim($formData['possession_value']) != '') {
                $this->getElement('possession_categoryId')->setRequired(true);
                $this->getElement('possession_description')->setRequired(true);
                $this->getElement('possession_value')->setRequired(true);
            }
        }

        // Call original isValid()
        return parent::isValid($formData);
    }
}