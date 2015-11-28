<?php

class LandlordsInsuranceQuote_Form_Subforms_UnderwritingQuestions extends Zend_Form_SubForm
{
    public function init()
    {
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
        
        // Add declaration question 2b element
        $this->addElement('radio', 'declaration2b', array(
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
        
        // Add declaration question 2c element
        $this->addElement('radio', 'declaration2c', array(
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
        
        // Add declaration question 2d element
        $this->addElement('radio', 'declaration2d', array(
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

        // Add declaration question 6 element
        $this->addElement('radio', 'declaration6', array(
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
        
        // Add declaration question 7 element
        $this->addElement('radio', 'declaration7', array(
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
        
        // Add declaration question 8 element
        $this->addElement('radio', 'declaration8', array(
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
        
        // Add declaration question 9 element
        $this->addElement('radio', 'declaration9', array(
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
        
        
        //Determine if declaration question 10 should be displayed, and if yes, add the element.
        $session = new Zend_Session_Namespace('landlords_insurance_quote');
        $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);
        if($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::RENT_GUARANTEE)) {

            $displayDeclaration10 = true;
        }
        else if($quoteManager->hasProduct(Manager_Insurance_LandlordsPlus_Quote::LEGAL_EXPENSES)) {

            $displayDeclaration10 = true;
        }
        else {

            $displayDeclaration10 = false;
        }

        if($displayDeclaration10) {

            // Add declaration question 10 element
            $this->addElement('radio', 'declaration10', array(
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
        }
        
        // Add declaration question 11 element
        $this->addElement('radio', 'declaration11', array(
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
                            'isEmpty' => 'Please select an answer for declaration question 11',
                            'notEmptyInvalid' => 'Please select an answer for declaration question 11'
                        )
                    )
                )
            )
        ));
        
        
        //Additional information.        
        $this->addElement('textarea', 'additional_information', array(
            'label'     => 'Additional information',
            'required'  => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter additional information',
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'class' => 'declarationAnswer form-control'
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
            array('ViewScript', array('viewScript' => 'subforms/underwriting-questions.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Strip all tags to prevent XSS errors - done iteratively so not to overwrite any existing filters
        foreach($this->getElements() as $element) {
            $element->addFilter('StripTags');
        }

        // Grab view and add the page-specific JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headScript()->appendFile(
            '/assets/landlords-insurance-quote/js/underwritingQuestions.js',
            'text/javascript'
        );
    }

    public function isValid($formData)
    {
        //We only need additional information if some questions have been answered wrongly.
        $additionalInfoIsRequired = false;
        if(!empty($formData['declaration2']) && $formData['declaration2'] == 'no') { $additionalInfoIsRequired = true; }
        else if(!empty($formData['declaration2b']) && $formData['declaration2b'] == 'no') { $additionalInfoIsRequired = true; }
        else if(!empty($formData['declaration2c']) && $formData['declaration2c'] == 'yes') { $additionalInfoIsRequired = true; }
        else if(!empty($formData['declaration2d']) && $formData['declaration2d'] == 'yes') { $additionalInfoIsRequired = true; }
        else if(!empty($formData['declaration3']) && $formData['declaration3'] == 'no') { $additionalInfoIsRequired = true; }
        else if(!empty($formData['declaration4']) && $formData['declaration4'] == 'no') { $additionalInfoIsRequired = true; }
        else if(!empty($formData['declaration6']) && $formData['declaration6'] == 'yes') { $additionalInfoIsRequired = true; }
        else if(!empty($formData['declaration8']) && $formData['declaration8'] == 'yes') { $additionalInfoIsRequired = true; }
        else if(!empty($formData['declaration9']) && $formData['declaration9'] == 'yes') { $additionalInfoIsRequired = true; }
        else if(!empty($formData['declaration10']) && $formData['declaration10'] == 'no') { $additionalInfoIsRequired = true; }

        if($additionalInfoIsRequired) {

            $this->getElement('additional_information')->setRequired(true);
        }
        else {

            $this->getElement('additional_information')->setRequired(false);
        }


        //Superclass validations
        $returnVal = parent::isValid($formData);


        //Some compound processing. If the user has advised they have previous claims, then they must provide
        //details of these. Ensure this is the case.
        $session = new Zend_Session_Namespace('landlords_insurance_quote');
        $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($session->quoteID);

        $customerReferenceNumber = $quoteManager->getLegacyCustomerReference();
        $policyNumber = $quoteManager->getLegacyID();

        if(!empty($formData['declaration7'])) {

            if($formData['declaration7'] == 'yes') {

                //One or more previous claims must exist.
                $claimsManager = new Manager_Insurance_PreviousClaims();
                $previousClaims = $claimsManager->getPreviousClaims($customerReferenceNumber);
                if(empty($previousClaims)) {

                    //Record error.
                    $this->declaration7->addError('Please provide details of previous claims');
                    $returnVal = false;
                }
            }
        }

        //If the user advised they have bank interest, then they must provide details of these.
        //Ensure this is the case.
        if(!empty($formData['declaration11'])) {

            if($formData['declaration11'] == 'yes') {

                //One or more bank interests.
                $bankInterestManager = new Manager_Insurance_LegacyBankInterest();
                $bankInterestArray = $bankInterestManager->getAllInterests($policyNumber, $customerReferenceNumber);

                if(empty($bankInterestArray)) {

                    //Record error.
                    $this->declaration11->addError('Please provide details of bank interest');
                    $returnVal = false;
                }
            }
        }

        return $returnVal;
    }
}
