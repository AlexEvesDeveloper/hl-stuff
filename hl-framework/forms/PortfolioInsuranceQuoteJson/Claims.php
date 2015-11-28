<?php
/**
* Class definition for the form elements in the subform Claims
* @author John Burrin
* @since 1.3
*/
class Form_PortfolioInsuranceQuoteJson_Claims extends Zend_Form_SubForm
{

    public function init()
    {
        $this->addElement('select', 'claim_property', array(
            'label'     => 'Property Claim on',
            'required'  => true,
            'multiOptions'  => array('' => '--- please select ---'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your property',
                            'notEmptyInvalid' => 'Please select your property'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        
        $this->addElement('select', 'claim_type', array(
            'label'     => 'Type of claim',
            'required'  => true,
            'multiOptions'  => array('' => '--- please select ---'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your claim type',
                            'notEmptyInvalid' => 'Please select your claim type'
                        )
                    )
                )
            )
        ));
        
        $this->addElement('select', 'claim_month', array(
            'label'     => 'Month of claim',
            'required'  => true,
            'multiOptions' => array(
                '' => '--- please select ---',
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
            )
        ));
        
        $claimYears = array('' => '--- please select ---');
        $nowYear = date('Y');
        for($i = $nowYear; $i >= $nowYear - 5; $i--) {
            $claimYears[$i] = $i;
        }
        
        $this->addElement('select', 'claim_year', array(
            'label'     => 'Year of claim',
            'required'  => true,
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
            )
        ));
        
        $this->addElement('text', 'claim_value', array(
            'label'      => 'Value of claim',
            'required'   => true,
            'attribs'     => array(
                'class'=>'currency'
            ),
            'filters'    => array('Digits'),
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
                    'regex', true, array(
                        'pattern' => '/^\d{1,}$/',
                        'messages' => 'Amount of claim value must contain at least one digit'
                    )
                ),
                array(
                    'GreaterThan', true, array(
                        'min' => 0,
                        'messages' => 'Claim value must be above zero'
                    )
                )
            )
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'portfolio-insurance-quote/subforms/claims-details-form.phtml'))
        ));
    }
    
    public function isValid($postData) {
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
        $customerReferenceNumber = $pageSession->CustomerRefNo;
        $propertyManager = new Manager_Insurance_Portfolio_Property();
        $propertyObjects = $propertyManager->fetchAllProperties($customerReferenceNumber);
        $propertyArray = $propertyObjects->toArray();
        $optionList = array('' => '--- please select ---');
        
        foreach($propertyArray as $property){
            $optionList[$property['id']] =
                     #   ($property['houseNumber']) ." ".
                      #  ($property['building'])  ." ".
                        ($property['address1'])  ." ".
                        ($property['address2'])  ." ".
                        ($property['address3'])  ." ".
                      #  ($property['address4'])  ." ".
                      #  ($property['address5'])  ." ".
                        ($property['postcode']);
            }
        
        // Get the subfoem element for property address that the bank may have interest in
        $propertyAddressSelect = $this->getElement('claim_property');
        $propertyAddressSelect->setMultiOptions($optionList);
        
        $validator = new Zend_Validate_InArray(array(
            'haystack' => array_keys($optionList)
        ));
        $validator->setMessages(array(
            Zend_Validate_InArray::NOT_IN_ARRAY => 'Not in list'
        ));
        $propertyAddressSelect->addValidator($validator, true);                    
        // Set the selected to 0
        $propertyAddressSelect->setValue('0');
        
        $claimTypeList = array('' => '--- please select ---');
        $claimTypesSelect = $this->getElement('claim_type');
        $claimTypes = new Datasource_Insurance_PreviousClaimTypes();
        $claimTypeObjects = $claimTypes->getPreviousClaimTypes(Model_Insurance_ProductNames::LANDLORDSPLUS);
        
        foreach($claimTypeObjects as $ClaimType){
            $claimTypeList[$ClaimType->getClaimTypeID()] = $ClaimType->getClaimTypeText();
        }
        $claimTypesSelect->setMultiOptions($claimTypeList);
        return parent::isValid($postData);
    }
}
?>