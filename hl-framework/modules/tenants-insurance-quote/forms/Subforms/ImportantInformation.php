<?php

class TenantsInsuranceQuote_Form_Subforms_ImportantInformation extends Zend_Form_SubForm
{
    /**
     * Create important information subform
     * @todo the questions in subform-importantinformation.phtml are currently hard coded and should be pulled out of the DB/model
     *
     * @return void
     */
    public function init()
    {
        // Invoke the previous claims manager
        $claimsManager = new Manager_Insurance_PreviousClaims();

        // Create array of claim types
        $claimDescriptions = array('' => 'Please select...');
        $claimDescriptionsObj = $claimsManager->getPreviousClaimTypes(Model_Insurance_ProductNames::TENANTCONTENTSPLUS);
        foreach($claimDescriptionsObj as $claimDescriptionObj) {
        	
        	$claimTypeId = $claimDescriptionObj->getClaimTypeID();
            $claimDescriptions[$claimTypeId] = $claimDescriptionObj->getClaimTypeText();
        }

        // Add declaration question 1 element
        $this->addElement('radio', 'declaration1', array(
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
                            'isEmpty' => 'Please select an answer for declaration question 1',
                            'notEmptyInvalid' => 'Please select an answer for declaration question 1'
                        )
                    )
                )
            )
        ));

        // Add conditional details for Q1
        $this->addElement('textarea', 'declaration1_details', array(
            'label'     => 'Please provide details',
            'required'  => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter details for declaration question 1',
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-ctfilter' => 'yes',
                'class' => 'declarationAnswer form-control',
            )
        ));

        // Add declaration question 2 element
        $this->addElement('radio', 'declaration2', array(
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
                            'isEmpty' => 'Please select an answer for declaration question 2',
                            'notEmptyInvalid' => 'Please select an answer for declaration question 2'
                        )
                    )
                )
            )
        ));

        $this->addElement('hidden', 'claim_confirm', array(
            'label'     => '',
            'required'  => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'You must add the claim to complete this step',
                            'notEmptyInvalid' => 'You must add the claim to complete this step'
                        )
                    )
                )
            )
        ));
    
        // Add conditional details for Q2
        // Add claim type element
        $this->addElement('select', 'claim_type', array(
            'label'     => 'Type of claim',
            'required'  => false,
            'multiOptions' => $claimDescriptions,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your claim type',
                            'notEmptyInvalid' => 'Please select your claim type'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'class' => 'form-control',
            )
        ));

        $this->addElement('select', 'claim_month', array(
            'label'     => 'Month of claim',
            'required'  => false,
            'multiOptions' => array(
                '' => 'Please select...',
                '01' => 'Jan',
                '02' => 'Feb',
                '03' => 'Mar',
                '04' => 'Apr',
                '05' => 'May',
                '06' => 'Jun',
                '07' => 'Jul',
                '08' => 'Aug',
                '09' => 'Sep',
                '10' => 'Oct',
                '11' => 'Nov',
                '12' => 'Dec'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your claim month',
                            'notEmptyInvalid' => 'Please select your claim month'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));
        $claimYears = array('' => 'Please select...');
        $nowYear = date('Y');
        for($i = $nowYear; $i >= $nowYear - 5; $i--) {
            $claimYears[$i] = $i;
        }
        $this->addElement('select', 'claim_year', array(
            'label'     => 'Year of claim',
            'required'  => false,
            'multiOptions' => $claimYears,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your claim year',
                            'notEmptyInvalid' => 'Please select your claim year'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));
        $this->addElement('text', 'claim_value', array(
            'required'   => false,
            'attribs' 	=> array(
                'class'=>'currency'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the claim value',
                            'notEmptyInvalid' => 'Please enter the claim value'
                        )
                    )
                ),
                array(
                    'GreaterThan', true, array(
                        'min' => 0,
                        'messages' => 'Claim value must be above zero'
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        // Add declaration question 3 element
        $this->addElement('radio', 'declaration3', array(
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
                            'isEmpty' => 'Please select an answer for declaration question 3',
                            'notEmptyInvalid' => 'Please select an answer for declaration question 3'
                        )
                    )
                )
            )
        ));

        // Add conditional details for Q3
        $this->addElement('textarea', 'declaration3_details', array(
            'label'     => 'Please provide details',
            'required'  => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter details for declaration question 3',
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-ctfilter' => 'yes',
                'class' => 'declarationAnswer form-control',
            )
        ));

        // Add declaration question 4 element
        $this->addElement('radio', 'declaration4', array(
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
                            'isEmpty' => 'Please select an answer for declaration question 4',
                            'notEmptyInvalid' => 'Please select an answer for declaration question 4'
                        )
                    )
                )
            )
        ));

        // Add conditional details for Q4
        $this->addElement('textarea', 'declaration4_details', array(
            'label'     => 'Please provide details',
            'required'  => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter details for declaration question 4',
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-ctfilter' => 'yes',
                'class' => 'declarationAnswer form-control',
            )
        ));

        // Add declaration confirmation element
        $this->addElement('radio', 'declaration_confirmation', array(
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
                            'isEmpty' => 'Please select an answer for the confirmation statement',
                            'notEmptyInvalid' => 'Please select an answer for the confirmation statement'
                        )
                    )
                ),
                array(
                    'Identical', true, array(
                        'token' => 'yes',
                        'messages' => array(
                            'notSame' => 'Please agree to the confirmation statement'
                        )
                    )
                )
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/important-information.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Grab view and add the declarations JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headScript()->appendFile(
            '/assets/tenants-insurance-quote/js/declarations.js',
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
        
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');

        // Filter for currency elements
        $currencyFilterElements = array(
            'claim_value'
        );
        foreach($currencyFilterElements as $filterElement) {
            if (isset($formData[$filterElement])) {
                $formData[$filterElement] = preg_replace(
                    array('/[^\d\.]/'),
                    array(''),
                    $formData[$filterElement]
                );
            }
        }

        // Check if this is an AJAX request, and ignore unneeded fields for validation by making them non-mandatory
        if (
            isset($formData['addClaim']) ||
            isset($formData['removeClaim'])
        ) {
            $this->getElement('declaration1')->setRequired(false);
            $this->getElement('declaration2')->setRequired(false);
            $this->getElement('declaration3')->setRequired(false);
            $this->getElement('declaration4')->setRequired(false);
            $this->getElement('declaration_confirmation')->setRequired(false);
        } else {
            // Not an AJAX request
            // Selectively make details sections mandatory if their corresponding Qs are marked "Yes"
            if (isset($formData['declaration1']) && $formData['declaration1'] == 'yes') {
                $this->getElement('declaration1_details')->setRequired(true);
            }
            if (isset($formData['declaration2']) && $formData['declaration2'] == 'yes') {
                $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
                // Invoke the previous claims manager
                $claimsManager = new Manager_Insurance_PreviousClaims();

                // If no claims have been entered, make the claim input fields mandatory
                if ($claimsManager->countPreviousClaims($pageSession->CustomerRefNo) == 0) {
                    $this->getElement('claim_type')->setRequired(true);
                    $this->getElement('claim_month')->setRequired(true);
                    $this->getElement('claim_year')->setRequired(true);
                    $this->getElement('claim_value')->setRequired(true);
                    $this->getElement('claim_confirm')->setRequired(true);
                }

            }
            if (isset($formData['declaration3']) && $formData['declaration3'] == 'yes') {
                $this->getElement('declaration3_details')->setRequired(true);
            }
            if (isset($formData['declaration4']) && $formData['declaration4'] == 'yes') {
                $this->getElement('declaration4_details')->setRequired(true);
            }
        }

        // Check if a new claim's details is being added and if so make fields mandatory
        if (isset($formData['addClaim']) && $formData['addClaim'] == 1) {
            $this->getElement('claim_type')->setRequired(true);
            $this->getElement('claim_month')->setRequired(true);
            $this->getElement('claim_year')->setRequired(true);
            $this->getElement('claim_value')->setRequired(true);
            $this->getElement('claim_confirm')->setRequired(true);
        }

        // Call original isValid()
        return parent::isValid($formData);

    }
}