<?php

class LandlordsInsuranceQuote_Form_Subforms_CorrespondenceDetails extends Zend_Form_SubForm
{
    /**
     * Create correspondence details subform
     *
     * @return void
     */
    public function init()
    {
        // Add foreign address toggle
        $this->addElement('checkbox', 'cor_foreign_address', array(
            'checkedValue'  => '1',
        ));

        // Add house number/name element
        $this->addElement('hidden', 'cor_house_number_name', array(
                'label'     => '',
                'required'  => false,
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a house number or name',
                            'notEmptyInvalid' => 'Please enter a house number or name'
                        )
                    )
                    ),
                    array(
                        'regex', true, array(
                        'pattern' => '/^[0-9a-z\ \-\/]{1,}$/i',
                        'messages' => 'House number or name must contain at least one alphanumeric character and only basic punctuation (space, hyphen and forward slash)'
                    )
                    )
                ),
                'attribs' => array(
                    'data-ctfilter' => 'yes'
                )
            )
        );

        // Add postcode element
        $this->addElement('text', 'cor_postcode', array(
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a correspondence address postcode',
                            'notEmptyInvalid' => 'Please enter a correspondence address postcode'
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

        // Add address select element
        $this->addElement('select', 'cor_address', array(
            'label'     => 'Please select your address',
            'required'  => true,
            'multiOptions' => array(
                '' => '--- please select ---'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your correspondence address',
                            'notEmptyInvalid' => 'Please select your correspondence address'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));

        // Add address text boxes
        $this->addElement('text', 'cor_address_line1', array(
            'label'      => '',
            'required'   => false,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please provide the first line of your address',
                            'notEmptyInvalid' => 'Please provide the first line of your address'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        $this->addElement('text', 'cor_address_line2', array(
            'label'      => '',
            'required'   => false,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => 'Please provide the second line of your address',
                        'notEmptyInvalid' => 'Please provide the second line of your address'
                    )
                )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        $this->addElement('text', 'cor_address_line3', array(
            'label'      => '',
            'required'   => false,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        $this->addElement('text', 'cor_address_postcode', array(
            'label'      => '',
            'required'   => true,
            'readonly'	 => 'readonly',
            'class'		 => 'readonly',
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a correspondence address postcode',
                            'notEmptyInvalid' => 'Please enter a correspondence address postcode'
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
                'data-ctfilter' => 'yes'
            )
        ));

        // todo: add countries
        $this->addElement('select', 'country', array(
            'label' => 'Select country',
            'required' => false,
            'multiOptions' => array(
                '' => '--- please select ---',
                'Afghanistan' => 'Afghanistan',
                'Albania' => 'Albania',
                'Algeria' => 'Algeria',
                'American Samoa' => 'American Samoa',
                'Andorra' => 'Andorra',
                'Angola' => 'Angola',
                'Anguilla' => 'Anguilla',
                'Antigua and Barbuda' => 'Antigua and Barbuda',
                'Argentina' => 'Argentina',
                'Armenia' => 'Armenia',
                'Aruba' => 'Aruba',
                'Australia' => 'Australia',
                'Austria' => 'Austria',
                'Azerbaijan' => 'Azerbaijan',
                'Bahamas, The' => 'Bahamas, The',
                'Bahrain' => 'Bahrain',
                'Bangladesh' => 'Bangladesh',
                'Barbados' => 'Barbados',
                'Belarus' => 'Belarus',
                'Belgium' => 'Belgium',
                'Belize' => 'Belize',
                'Benin' => 'Benin',
                'Bermuda' => 'Bermuda',
                'Bhutan' => 'Bhutan',
                'Bolivia' => 'Bolivia',
                'Bosnia and Herzegovina' => 'Bosnia and Herzegovina',
                'Botswana' => 'Botswana',
                'Brazil' => 'Brazil',
                'British Virgin Is.' => 'British Virgin Is.',
                'Brunei' => 'Brunei',
                'Bulgaria' => 'Bulgaria',
                'Burkina Faso' => 'Burkina Faso',
                'Burma' => 'Burma',
                'Burundi' => 'Burundi',
                'Cambodia' => 'Cambodia',
                'Cameroon' => 'Cameroon',
                'Canada' => 'Canada',
                'Cape Verde' => 'Cape Verde',
                'Cayman Islands' => 'Cayman Islands',
                'Central African Rep.' => 'Central African Rep.',
                'Chad' => 'Chad',
                'Chile' => 'Chile',
                'China' => 'China',
                'Colombia' => 'Colombia',
                'Comoros' => 'Comoros',
                'Congo, Dem. Rep.' => 'Congo, Dem. Rep.',
                'Congo, Repub. of the' => 'Congo, Repub. of the',
                'Cook Islands' => 'Cook Islands',
                'Costa Rica' => 'Costa Rica',
                'Cote d\'Ivoire' => 'Cote d\'Ivoire',
                'Croatia' => 'Croatia',
                'Cuba' => 'Cuba',
                'Cyprus' => 'Cyprus',
                'Czech Republic' => 'Czech Republic',
                'Denmark' => 'Denmark',
                'Djibouti' => 'Djibouti',
                'Dominica' => 'Dominica',
                'Dominican Republic' => 'Dominican Republic',
                'East Timor' => 'East Timor',
                'Ecuador' => 'Ecuador',
                'Egypt' => 'Egypt',
                'El Salvador' => 'El Salvador',
                'Equatorial Guinea' => 'Equatorial Guinea',
                'Eritrea' => 'Eritrea',
                'Estonia' => 'Estonia',
                'Ethiopia' => 'Ethiopia',
                'Faroe Islands' => 'Faroe Islands',
                'Fiji' => 'Fiji',
                'Finland' => 'Finland',
                'France' => 'France',
                'French Guiana' => 'French Guiana',
                'French Polynesia' => 'French Polynesia',
                'Gabon' => 'Gabon',
                'Gambia, The' => 'Gambia, The',
                'Gaza Strip' => 'Gaza Strip',
                'Georgia' => 'Georgia',
                'Germany' => 'Germany',
                'Ghana' => 'Ghana',
                'Gibraltar' => 'Gibraltar',
                'Greece' => 'Greece',
                'Greenland' => 'Greenland',
                'Grenada' => 'Grenada',
                'Guadeloupe' => 'Guadeloupe',
                'Guam' => 'Guam',
                'Guatemala' => 'Guatemala',
                'Guernsey' => 'Guernsey',
                'Guinea' => 'Guinea',
                'Guinea-Bissau' => 'Guinea-Bissau',
                'Guyana' => 'Guyana',
                'Haiti' => 'Haiti',
                'Honduras' => 'Honduras',
                'Hong Kong' => 'Hong Kong',
                'Hungary' => 'Hungary',
                'Iceland' => 'Iceland',
                'India' => 'India',
                'Indonesia' => 'Indonesia',
                'Iran' => 'Iran',
                'Iraq' => 'Iraq',
                'Ireland' => 'Ireland',
                'Isle of Man' => 'Isle of Man',
                'Israel' => 'Israel',
                'Italy' => 'Italy',
                'Jamaica' => 'Jamaica',
                'Japan' => 'Japan',
                'Jersey' => 'Jersey',
                'Jordan' => 'Jordan',
                'Kazakhstan' => 'Kazakhstan',
                'Kenya' => 'Kenya',
                'Kiribati' => 'Kiribati',
                'Korea, North' => 'Korea, North',
                'Korea, South' => 'Korea, South',
                'Kuwait' => 'Kuwait',
                'Kyrgyzstan' => 'Kyrgyzstan',
                'Laos' => 'Laos',
                'Latvia' => 'Latvia',
                'Lebanon' => 'Lebanon',
                'Lesotho' => 'Lesotho',
                'Liberia' => 'Liberia',
                'Libya' => 'Libya',
                'Liechtenstein' => 'Liechtenstein',
                'Lithuania' => 'Lithuania',
                'Luxembourg' => 'Luxembourg',
                'Macau' => 'Macau',
                'Macedonia' => 'Macedonia',
                'Madagascar' => 'Madagascar',
                'Malawi' => 'Malawi',
                'Malaysia' => 'Malaysia',
                'Maldives' => 'Maldives',
                'Mali' => 'Mali',
                'Malta' => 'Malta',
                'Marshall Islands' => 'Marshall Islands',
                'Martinique' => 'Martinique',
                'Mauritania' => 'Mauritania',
                'Mauritius' => 'Mauritius',
                'Mayotte' => 'Mayotte',
                'Mexico' => 'Mexico',
                'Micronesia, Fed. St.' => 'Micronesia, Fed. St.',
                'Moldova' => 'Moldova',
                'Monaco' => 'Monaco',
                'Mongolia' => 'Mongolia',
                'Montserrat' => 'Montserrat',
                'Morocco' => 'Morocco',
                'Mozambique' => 'Mozambique',
                'Namibia' => 'Namibia',
                'Nauru' => 'Nauru',
                'Nepal' => 'Nepal',
                'Netherlands' => 'Netherlands',
                'Netherlands Antilles' => 'Netherlands Antilles',
                'New Caledonia' => 'New Caledonia',
                'New Zealand' => 'New Zealand',
                'Nicaragua' => 'Nicaragua',
                'Niger' => 'Niger',
                'Nigeria' => 'Nigeria',
                'N. Mariana Islands' => 'N. Mariana Islands',
                'Norway' => 'Norway',
                'Oman' => 'Oman',
                'Pakistan' => 'Pakistan',
                'Palau' => 'Palau',
                'Panama' => 'Panama',
                'Papua New Guinea' => 'Papua New Guinea',
                'Paraguay' => 'Paraguay',
                'Peru' => 'Peru',
                'Philippines' => 'Philippines',
                'Poland' => 'Poland',
                'Portugal' => 'Portugal',
                'Puerto Rico' => 'Puerto Rico',
                'Qatar' => 'Qatar',
                'Reunion' => 'Reunion',
                'Romania' => 'Romania',
                'Russia' => 'Russia',
                'Rwanda' => 'Rwanda',
                'Saint Helena' => 'Saint Helena',
                'Saint Kitts and Nevis' => 'Saint Kitts and Nevis',
                'Saint Lucia' => 'Saint Lucia',
                'St Pierre and Miquelon' => 'St Pierre and Miquelon',
                'Saint Vincent and the Grenadines' => 'Saint Vincent and the Grenadines',
                'Samoa' => 'Samoa',
                'San Marino' => 'San Marino',
                'Sao Tome and Principe' => 'Sao Tome and Principe',
                'Saudi Arabia' => 'Saudi Arabia',
                'Senegal' => 'Senegal',
                'Serbia' => 'Serbia',
                'Seychelles' => 'Seychelles',
                'Sierra Leone' => 'Sierra Leone',
                'Singapore' => 'Singapore',
                'Slovakia' => 'Slovakia',
                'Slovenia' => 'Slovenia',
                'Solomon Islands' => 'Solomon Islands',
                'Somalia' => 'Somalia',
                'South Africa' => 'South Africa',
                'Spain' => 'Spain',
                'Sri Lanka' => 'Sri Lanka',
                'Sudan' => 'Sudan',
                'Suriname' => 'Suriname',
                'Swaziland' => 'Swaziland',
                'Sweden' => 'Sweden',
                'Switzerland' => 'Switzerland',
                'Syria' => 'Syria',
                'Taiwan' => 'Taiwan',
                'Tajikistan' => 'Tajikistan',
                'Tanzania' => 'Tanzania',
                'Thailand' => 'Thailand',
                'Togo' => 'Togo',
                'Tonga' => 'Tonga',
                'Trinidad and Tobago' => 'Trinidad and Tobago',
                'Tunisia' => 'Tunisia',
                'Turkey' => 'Turkey',
                'Turkmenistan' => 'Turkmenistan',
                'Turks and Caicos Is' => 'Turks and Caicos Is',
                'Tuvalu' => 'Tuvalu',
                'Uganda' => 'Uganda',
                'Ukraine' => 'Ukraine',
                'United Arab Emirates' => 'United Arab Emirates',
                'United States' => 'United States',
                'Uruguay' => 'Uruguay',
                'Uzbekistan' => 'Uzbekistan',
                'Vanuatu' => 'Vanuatu',
                'Venezuela' => 'Venezuela',
                'Vietnam' => 'Vietnam',
                'Virgin Islands' => 'Virgin Islands',
                'Wallis and Futuna' => 'Wallis and Futuna',
                'West Bank' => 'West Bank',
                'Western Sahara' => 'Western Sahara',
                'Yemen' => 'Yemen',
                'Zambia' => 'Zambia',
                'Zimbabwe' => 'Zimbabwe'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your country',
                            'notEmptyInvalid' => 'Please select your country'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/correspondence-details.phtml'))
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        

        // Grab view and add the address lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headScript()->appendFile(
            '/assets/common/js/addressLookup.js',
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
        
        $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');

        // If a postcode is present, look it up and populate the allowed values of the associated dropdown
        if ((isset($formData['cor_postcode']) && trim($formData['cor_postcode']) != '')) {
            $postcode = trim($formData['cor_postcode']);
            $postcodeLookup = new Manager_Core_Postcode();
            $addresses = $postcodeLookup->getPropertiesByPostcode(preg_replace('/[^\w\ ]/', '', $postcode));
            $addressList = array('' => '--- please select ---');
            foreach($addresses as $address) {
                $addressList[$address['id']] = $address['singleLineWithoutPostcode'];
            }

            $cor_address = $this->getElement('cor_address');
            $cor_address->setMultiOptions($addressList);
            $validator = new Zend_Validate_InArray(array(
                    'haystack' => array_keys($addressList)
                ));
            $validator->setMessages(array(
                    Zend_Validate_InArray::NOT_IN_ARRAY => 'Correspondence address does not match with postcode'
                ));
            $cor_address->addValidator($validator, true);
        }

        // If a value for an address lookup is present, the house name or number
        // is not required
        if (isset($formData['cor_postcode'])) {
            $this->getElement('cor_house_number_name')->setRequired(false);
        }

        // todo: sort out server-side validation for foreign address stuff
        // If the address is overseas, toggle  the required status for the
        // relevant fields.
        if (isset($formData['cor_foreign_address']) && 1 == $formData['cor_foreign_address']) {
            $this->getElement('cor_address')->setRequired(false);
            $this->getElement('cor_address_postcode')->setRequired(false);
            $this->getElement('cor_address_line1')->setRequired(true);
            $this->getElement('cor_address_line2')->setRequired(true);
            $this->getElement('country')->setRequired(true);

            // Also change the validators for the postcode field
            $this->getElement('cor_postcode')->setValidators(
                array(
                    array(
                        'NotEmpty', true, array(
                            'messages' => array(
                                'isEmpty' => 'Please enter zip/postal code for your correspondence address',
                                'notEmptyInvalid' => 'Please enter a valid zip/postal code for your correspondence address'
                            )
                        )
                    )
                )
            );
            $this->getElement('cor_address_postcode')->setValidators(array());
        }

        // Call original isValid()
        return parent::isValid($formData);
        
    }
}