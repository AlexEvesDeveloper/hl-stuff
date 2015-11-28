<?php
/**
* Class definition for the form elements in the subform Additional Information
* @author John Burrin
* @since 1.3
*/
class Form_PortfolioInsuranceQuote_additionalDialog extends Zend_Form_SubForm
{

    public function init()
    {

        $this->addElement('select', 'property', array(
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

        $this->addElement('hidden', 'questionId', array(
            'label'     => '',
            'required'  => true

        ));


        $this->addElement('textarea', 'information', array(
            'required'  => true
        ));

        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'portfolio-insurance-quote/additional-details-form.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
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
                        ($property['houseNumber']) ." ".
                        ($property['building'])  ." ".
                        ($property['address1'])  ." ".
                        ($property['address2'])  ." ".
                        ($property['address3'])  ." ".
                        ($property['address4'])  ." ".
                        ($property['address5'])  ." ".
                        ($property['postcode']);
            }

        // Get the form element for property address
        $propertyAddressSelect = $this->getElement('property');
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

        return parent::isValid($postData);
    }
}
?>
