<?php
class Form_PortfolioInsuranceQuote_Subforms_ImportantInformation extends Zend_Form_SubForm
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
        $claimDescriptions = array('' => '--- please select ---');
        $claimDescriptionsObj = $claimsManager->getPreviousClaimTypes(Model_Insurance_ProductNames::LANDLORDSPLUS);
        
        foreach($claimDescriptionsObj as $claimDescriptionObj) {
            $claimTypeId = $claimDescriptionObj->getClaimTypeID();
            $claimDescriptions[$claimTypeId] = $claimDescriptionObj->getClaimTypeText();
        }
        
        // Add declaration question 1 element
        $this->addElement('radio', 'declaration1', array(
            'label'     => '',
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
        
        // Add declaration question 2 element
        $this->addElement('radio', 'declaration2', array(
            'label'     => '',
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
        
        // Add declaration question 3 element
        $this->addElement('radio', 'declaration3', array(
            'label'     => '',
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
                            'isEmpty' => 'Please select an answer for declaration question 2b',
                            'notEmptyInvalid' => 'Please select an answer for declaration question 2b'
                        )
                    )
                )
            )
        ));
        
        // Add declaration question 4 element
        $this->addElement('radio', 'declaration4', array(
            'label'     => '',
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
                            'isEmpty' => 'Please select an answer for declaration question 2c',
                            'notEmptyInvalid' => 'Please select an answer for declaration question 2c'
                        )
                    )
                )
            )
        ));
        
        // Add declaration question 5 element
        $this->addElement('radio', 'declaration5', array(
            'label'     => '',
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
                            'isEmpty' => 'Please select an answer for declaration question 2d',
                            'notEmptyInvalid' => 'Please select an answer for declaration question 2d'
                        )
                    )
                )
            )
        ));
        
        // Add declaration question 6 element
        $this->addElement('radio', 'declaration6', array(
            'label'     => '',
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
        
        // Add declaration question 7 element
        $this->addElement('radio', 'declaration7', array(
            'label'     => '',
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
        
        // Add declaration question 8 element
        $this->addElement('radio', 'declaration8', array(
            'label'     => '',
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
                            'isEmpty' => 'Please select an answer for declaration question 5',
                            'notEmptyInvalid' => 'Please select an answer for declaration question 5'
                        )
                    )
                )
            )
        ));
        
        // Add declaration question 9 element
        $this->addElement('radio', 'declaration9', array(
            'label'     => '',
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
                            'isEmpty' => 'Please select an answer for declaration question 6',
                            'notEmptyInvalid' => 'Please select an answer for declaration question 6'
                        )
                    )
                )
            )
        ));
        
        // Add declaration question 10 element
        $this->addElement('radio', 'declaration10', array(
            'label'     => '',
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
                            'isEmpty' => 'Please select an answer for declaration question 7',
                            'notEmptyInvalid' => 'Please select an answer for declaration question 7'
                        )
                    )
                )
            )
        ));
        
        // Add declaration question 11 element
        $this->addElement('radio', 'declaration11', array(
            'label'     => '',
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
                            'isEmpty' => 'Please select an answer for declaration question 8',
                            'notEmptyInvalid' => 'Please select an answer for declaration question 8'
                        )
                    )
                )
            )
        ));
        
        // Add declaration question 12 element
        $this->addElement('radio', 'declaration12', array(
            'label'     => '',
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
                            'isEmpty' => 'Please select an answer for declaration question 9',
                            'notEmptyInvalid' => 'Please select an answer for declaration question 9'
                        )
                    )
                )
            )
        ));
        
        // Add declaration question 13 element
        $this->addElement('radio', 'declaration13', array(
            'label'     => '',
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
                            'isEmpty' => 'Please select an answer for declaration question 10',
                            'notEmptyInvalid' => 'Please select an answer for declaration question 10'
                        )
                    )
                )
            )
        ));
        
/****** ***/
        $this->addElement('hidden','additionCheck1',array(
            'required'  => false
        ));
        
        $this->addElement('hidden','additionCheck2',array(
            'required'  => false
        ));
        
        $this->addElement('hidden','additionCheck3',array(
            'required'  => false
        ));
        
        $this->addElement('hidden','additionCheck4',array(
            'required'  => false
        ));
        
        $this->addElement('hidden','additionCheck5',array(
            'required'  => false
        ));
        
        $this->addElement('hidden','additionCheck6',array(
            'required'  => false
        ));
        
        $this->addElement('hidden','additionCheck7',array(
            'required'  => false
        ));
        
        $this->addElement('hidden','additionCheck8',array(
            'required'  => false
        ));
        
        $this->addElement('hidden','additionCheck9',array(
            'required'  => false
        ));
        
        $this->addElement('hidden','additionCheck10',array(
            'required'  => false
        ));
        
        $this->addElement('hidden','additionCheck11',array(
            'required'  => false
        ));
        
        $this->addElement('hidden','additionCheck12',array(
            'required'  => false
        ));
        
        $this->addElement('hidden','additionCheck13',array(
            'required'  => false
        ));
/***** ****/
        
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
            array('ViewScript', array('viewScript' => 'portfolio-insurance-quote/subforms/important-information.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        // Grab view and add the declarations JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        /*   $view->headScript()->appendFile(
            '/assets/cms/js/portfolio-insurance-quote/declarations.js',
            'text/javascript'
        );*/
    }
    
    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     *
     * @return bool
     *
     */
    public function isValid($formData = array())
    {
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        
        // Check if this is an AJAX request, and ignore unneeded fields for validation by making them non-mandatory
        $additionalManager = new Manager_Insurance_Portfolio_AdditionalInformation();
        if(isset($formData['declaration1']) && $formData['declaration1'] == "no"){
            $additionCheck = $additionalManager->hasAdditions($pageSession->CustomerRefNo,1);
            if(!$additionCheck){
                $this->getElement('additionCheck1')->setRequired(true)->addErrorMessage('You must add a additional Information for declaration question 1');
            }
        }
        
        if(isset($formData['declaration2']) && $formData['declaration2'] == "no"){
            $additionCheck = $additionalManager->hasAdditions($pageSession->CustomerRefNo,2);
            if(!$additionCheck){
                $this->getElement('additionCheck2')->setRequired(true)->addErrorMessage('You must add a additional Information for declaration question 2');
            }
        }
        
        if(isset($formData['declaration3']) && $formData['declaration3'] == "no"){
            $additionCheck = $additionalManager->hasAdditions($pageSession->CustomerRefNo,3);
            if(!$additionCheck){
                $this->getElement('additionCheck3')->setRequired(true)->addErrorMessage('You must add a additional Information for declaration question 2b');
            }
        }
        
        if(isset($formData['declaration4']) && $formData['declaration4'] == "yes"){
            $additionCheck = $additionalManager->hasAdditions($pageSession->CustomerRefNo,4);
            if(!$additionCheck){
                $this->getElement('additionCheck4')->setRequired(true)->addErrorMessage('You must add a additional Information for declaration question 2c');
            }
        }
        
        if(isset($formData['declaration5']) && $formData['declaration5'] == "yes"){
            $additionCheck = $additionalManager->hasAdditions($pageSession->CustomerRefNo,5);
            if(!$additionCheck){
                $this->getElement('additionCheck5')->setRequired(true)->addErrorMessage('You must add a additional Information for declaration question 2d');
            }
        }
        
        if(isset($formData['declaration6']) && $formData['declaration6'] == "no"){
            $additionCheck = $additionalManager->hasAdditions($pageSession->CustomerRefNo,6);
            if(!$additionCheck){
                $this->getElement('additionCheck6')->setRequired(true)->setRequired(true)->addErrorMessage('You must add a additional Information for declaration question 3');
            }
        }
        
        if(isset($formData['declaration7']) && $formData['declaration7'] == "no"){
            $additionCheck = $additionalManager->hasAdditions($pageSession->CustomerRefNo,7);
            if(!$additionCheck){
                $this->getElement('additionCheck7')->setRequired(true)->addErrorMessage('You must add a additional Information for declaration question 4');
            }
        }
        
        if(isset($formData['declaration8']) && $formData['declaration8'] == "yes"){
            $additionCheck = $additionalManager->hasAdditions($pageSession->CustomerRefNo,8);
            if(!$additionCheck){
                $this->getElement('additionCheck8')->setRequired(true)->addErrorMessage('You must add a additional Information for declaration question 5');
            }
        }
        
        if(isset($formData['declaration9']) && $formData['declaration9'] == "yes"){
            $additionCheck = $additionalManager->hasAdditions($pageSession->CustomerRefNo,9);
            if(!$additionCheck){
                $this->getElement('additionCheck9')->setRequired(true)->addErrorMessage('You must add a additional Information for declaration question 6');
            }
        }
        
        /*
        if(isset($formData['declaration10']) && $formData['declaration10'] == "yes"){
            $additionCheck = $additionalManager->hasAdditions($pageSession->CustomerRefNo,10);
            if(!$additionCheck){
                $this->getElement('additionCheck10')->setRequired(true)->addErrorMessage('You must add a additional Information for declaration question 7');
            }
        }
        */
        
        if(isset($formData['declaration11']) && $formData['declaration11'] == "yes"){
            $additionCheck = $additionalManager->hasAdditions($pageSession->CustomerRefNo,11);
            if(!$additionCheck){
                $this->getElement('additionCheck11')->setRequired(true)->addErrorMessage('You must add a additional Information for declaration question 8');
            }
        }
        
        if(isset($formData['declaration12']) && $formData['declaration12'] == "yes"){
            $additionCheck = $additionalManager->hasAdditions($pageSession->CustomerRefNo,12);
            if(!$additionCheck){
                $this->getElement('additionCheck12')->setRequired(true)->addErrorMessage('You must add a additional Information for declaration question 9');
            }
        }
        
        /*
        if(isset($formData['declaration13']) && $formData['declaration13'] == "yes"){
            $additionCheck = $additionalManager->hasAdditions($pageSession->CustomerRefNo,13);
            if(!$additionCheck){
                $this->getElement('additionCheck13')->setRequired(true)->addErrorMessage('You must add a additional Information for declaration question 10');
            }
        }
        */
        
        // Call original isValid()
        return parent::isValid($formData);
    }
}
