<?php

class LandlordsReferencing_Form_Residence extends Zend_Form
{
    public function init()
    {
        //Identify which residence this form is being provided for (current, first previous or
        //second previous).
        $session = new Zend_Session_Namespace('referencing_global');
        switch ($session->currentFlowItem) {
            case Model_Referencing_DataEntry_FlowItems::FIRST_RESIDENCE:
                $session->residentialChronology = Model_Referencing_ResidenceChronology::CURRENT;
                break;

            case Model_Referencing_DataEntry_FlowItems::SECOND_RESIDENCE:
                $session->residentialChronology = Model_Referencing_ResidenceChronology::FIRST_PREVIOUS;
                break;

            case Model_Referencing_DataEntry_FlowItems::THIRD_RESIDENCE:
                $session->residentialChronology = Model_Referencing_ResidenceChronology::SECOND_PREVIOUS;
                break;
        }

        // Add house number/name element
        $this->addElement('hidden', 'property_number_name', array(
            'required' => false,
        ));

        $this->addElement('text', 'property_postcode', array(
            'label' => 'Postcode',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a postcode',
                            'notEmptyInvalid' => 'Please enter a valid postcode'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z]{2,}\ ?[0-9a-z]{2,}$/i', // TODO: temporary regex, needs to use postcode validator once available
                        'messages' => 'Postcode must be in postcode format'
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'postcode',
                'class' => 'form-control',
            )
        ));

        $this->addElement('select', 'property_address', array(
            'label' => 'Please select your address',
            'required' => true,
            'multiOptions' => array(
                '' => '--- please select ---'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select the property address',
                            'notEmptyInvalid' => 'Please select the property address'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));        

        // Grab view and add the address lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headScript()->appendFile(
            '/assets/common/js/addressLookup.js',
            'text/javascript'
        );

        $this->addElement('radio', 'is_foreign_address', array(
            'label' => 'Is address overseas?',
            'required' => true,
            'multiOptions' => array(
                'Yes' => 'Yes',
                'No' => 'No'
            ),
            'separator' => '',
            'label_placement' => 'prepend',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select if address is overseas',
                            'notEmptyInvalid' => 'Please select if address is overseas'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));
        
        $this->addElement('text', 'duration_at_address', array(
            'label' => 'Period at Address: ',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter duration at address in months'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
                'style' => 'display: none;',
            )
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        //Finally, remove the default decorators from the hidden elements.
        $this->property_number_name->removeDecorator('HtmlTag');
    }

    public function isValid($formData = array())
    {
        if ((isset($formData['property_postcode']) && '' != trim($formData['property_postcode']))) {
            $postcode = trim($formData['property_postcode']);
            $postcodeLookup = new Manager_Core_Postcode();
            $addresses = $postcodeLookup->getPropertiesByPostcode(preg_replace('/[^\w\ ]/', '', $postcode));

            $addressList = array('' => '--- please select ---');
            foreach ($addresses as $address) {
                $addressList[$address['id']] = $address['singleLineWithoutPostcode'];
            }

            $ins_address = $this->getElement('property_address');
            $ins_address->setMultiOptions($addressList);
            $validator = new Zend_Validate_InArray(array(
                'haystack' => array_keys($addressList)
            ));
            $validator->setMessages(array(
                Zend_Validate_InArray::NOT_IN_ARRAY => 'Insured address does not match with postcode'
            ));
            $ins_address->addValidator($validator, true);
        }

        // If a value for an address lookup is present, the house name or number is not required
        if (isset($formData['is_foreign_address']) && 'Yes' == $formData['is_foreign_address']) {
            $this->getElement('property_postcode')->setRequired(false);
            $this->getElement('property_address')->setRequired(false);
        }

        if (isset($formData['property_postcode'])) {
            $this->getElement('property_number_name')->setRequired(false);
        }

        return parent::isValid($formData);
    }

    public function saveData()
    {
        $session = new Zend_Session_Namespace('referencing_global');
        $data = $this->getValues();

        $referenceManager = new Manager_Referencing_Reference();
        $reference = $referenceManager->getReference($session->referenceId);

        //Derive the residence chronology from the current flow item, so that we can locate
        //the relevant residence to update.
        switch ($session->currentFlowItem) {
            case Model_Referencing_DataEntry_FlowItems::FIRST_RESIDENCE:
                $chronology = Model_Referencing_ResidenceChronology::CURRENT;
                break;
            case Model_Referencing_DataEntry_FlowItems::SECOND_RESIDENCE:
                $chronology = Model_Referencing_ResidenceChronology::FIRST_PREVIOUS;
                break;
            case Model_Referencing_DataEntry_FlowItems::THIRD_RESIDENCE:
                $chronology = Model_Referencing_ResidenceChronology::SECOND_PREVIOUS;
                break;
        }


        //Attept to locate the relevant residence.
        $residenceManager = new Manager_Referencing_Residence();
        $thisResidence = $residenceManager->findSpecificResidence($reference->referenceSubject->residences, $chronology);
        if (empty($thisResidence)) {
            //The residence to process does not exist, so create it first.
            $thisResidence = $residenceManager->insertPlaceholder($session->referenceId, $chronology);

            if(empty($reference->referenceSubject->residences)) {
                $reference->referenceSubject->residences = array();
            }
            $reference->referenceSubject->residences[] = $thisResidence;
        }


        //Update thisResidence to reflect the user inputs.
        if (isset($data['is_foreign_address']) && 'Yes' == ($data['is_foreign_address'])) {
            // Needed by the HRT system to recognise foreign addresses
            $thisResidence->address->addressLine1 = 'Abroad';
            $thisResidence->address->town = 'Abroad';
            $thisResidence->address->postCode = '1001';

            $thisResidence->address->isOverseasAddress = true;
        }
        else {
            //Format the property details.
            $postcodeManager = new Manager_Core_Postcode();
            $propertyAddress = $postcodeManager->getPropertyByID($data['property_address'], false);
            $addressLine1 =
                (($propertyAddress['organisation'] != '') ? "{$propertyAddress['organisation']}, " : '')
                . (($propertyAddress['houseNumber'] != '') ? "{$propertyAddress['houseNumber']} " : '')
                . (($propertyAddress['buildingName'] != '') ? "{$propertyAddress['buildingName']}, " : '')
                . $propertyAddress['address2'];

            $addressLine2 = $propertyAddress['address4'];
            $town = $propertyAddress['address5'];
            $postCode = $data['property_postcode'];

            if (empty($thisResidence->address)) {
                $addressManager = new Manager_Core_Address();
                $thisResidence->address = $addressManager->createAddress();
            }

            $thisResidence->address->addressLine1 = $addressLine1;
            $thisResidence->address->addressLine2 = $addressLine2;
            $thisResidence->address->town = $town;
            $thisResidence->address->postCode = $postCode;
            $thisResidence->address->isOverseasAddress = false;
        }

        $thisResidence->durationAtAddress = $data['duration_at_address']; //months

        //Finally, identify if the ReferenceSubject should be classed as a foreign national,
        //which is when they have spent the 6 months or more abroad.
        if ($this->_isOverseas($reference)) {
            $reference->referenceSubject->isForeignNational = true;
        }
        else {
            $reference->referenceSubject->isForeignNational = false;
        }

        //Update the datasources.
        $referenceManager->updateReference($reference);
    }

    /**
     * Returns a Model_Core_Address built from the form data.
     *
     * Note that the address object will not be reflected in the datasource,
     * and so will not contain an ID. The purpose of this method is to provide
     * a simple means of address extraction so that the address given can be easily
     * examined and tested.
     *
     * @return Model_Core_Address
     * An address built from th details provided on the form.
     */
    public function getAddressFromForm()
    {
        $session = new Zend_Session_Namespace('referencing_global');
        $data = $this->getValues();

        $address = new Model_Core_Address();
        if (isset($data['is_foreign_address']) && 'Yes' == ($data['is_foreign_address'])) {
            $address->isOverseasAddress = true;
        }
        else {
            $postcodeManager = new Manager_Core_Postcode();
            $propertyAddress = $postcodeManager->getPropertyByID($data['property_address'], false);
            $addressLine1 =
                (($propertyAddress['organisation'] != '') ? "{$propertyAddress['organisation']}, " : '')
                . (($propertyAddress['houseNumber'] != '') ? "{$propertyAddress['houseNumber']} " : '')
                . (($propertyAddress['buildingName'] != '') ? "{$propertyAddress['buildingName']}, " : '')
                . $propertyAddress['address2'];

            $address->addressLine1 = $addressLine1;
            $address->addressLine2 = $propertyAddress['address4'];
            $address->town = $propertyAddress['address5'];
            $address->postCode = $data['property_postcode'];
            $address->isOverseasAddress = false;
        }
        return $address;
    }

    /**
     * Determines if the reference subject is a foreign national.
     *
     * @param Model_Referencing_Reference $reference
     * Encapsulates the reference subject and residences.
     *
     * @return boolean
     * Returns true if the applicant is a foreign national, false otherwise.
     */
    protected function _isOverseas($reference)
    {
        $isOverseas = false;

        $currentResidence = null;
        $firstPreviousResidence = null;
        $secondPreviousResidence = null;

        foreach ($reference->referenceSubject->residences as $residence) {
            switch ($residence->chronology) {
                case Model_Referencing_ResidenceChronology::CURRENT:
                    $currentResidence = $residence;
                    break;
                case Model_Referencing_ResidenceChronology::FIRST_PREVIOUS:
                    $firstPreviousResidence = $residence;
                    break;
                case Model_Referencing_ResidenceChronology::SECOND_PREVIOUS:
                    $secondPreviousResidence = $residence;
                    break;
            }
        }

        $overseasDuration = 0;
        if ($currentResidence->address->isOverseasAddress) {
            $isOverseas = true;
        }

        if (!$isOverseas) {
            if (!empty($firstPreviousResidence)) {
                if ($firstPreviousResidence->address->isOverseasAddress) {
                    if ($currentResidence->durationAtAddress < 6) {
                        $isOverseas = true;
                    }
                }
            }
        }


        if (!$isOverseas) {
            if (!empty($secondPreviousResidence)) {
                if ($secondPreviousResidence->address->isOverseasAddress) {
                    $totalDurationsSoFar = $currentResidence->durationAtAddress + $firstPreviousResidence->durationAtAddress;

                    if ($totalDurationsSoFar < 6) {
                        $isOverseas = true;
                    }
                }
            }
        }

        return $isOverseas;
    }

    public function getMessagesFlattened()
    {
        return $this->getMessages();
    }
}