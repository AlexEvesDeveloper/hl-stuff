<?php
class Form_PortfolioInsuranceQuote_Step3 extends Zend_Form {
    /**
     * Pull in the sub forms that comprise Portfolio Step 3
     *
     * @return void
     */
    public function init()
    {
         // Existing Insurer
        $this->addElement('text', 'target_premium', array(
            'label'     => '',
            'required'  => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please let us know how much your renewal premium is',
                            'notEmptyInvalid' => 'Please let us know how much your renewal premium is'
                        )
                    )
                )
            )
        ));
        
        // Existing Insurer
        $this->addElement('text', 'existing_insurer', array(
            'label'     => '',
            'required'  => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please let us know the name of you existng insurer',
                            'notEmptyInvalid' => 'Please let us know the name of you existng insurer'
                        )
                    )
                )
            )
        ));

        // Add next renewal date element
        $this->addElement('text', 'next_renwal_date', array(
                'label'     => '',
                'required'  => true,
                'filters'    => array('StringTrim'),
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your renewal date',
                            'notEmptyInvalid' => 'Please enter a valid policy start date'
                        )
                    )
                )
            )
        ));
        $next_renwal_date = $this->getElement('next_renwal_date');
        $validator = new Zend_Validate_DateCompare();
        $validator->minimum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $validator->setMessages(array(
            'msgMinimum' => 'Policy start date cannot be in the past',
        ));
        $next_renwal_date->addValidator($validator, true);

        // Append additional styles for the datepicker
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $view->headLink()->appendStylesheet(
            '/assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css',
            'screen'
        );

        // Append required JS files for the datepicker to function
        $view->headScript()->appendFile(
            '/assets/vendor/jquery-date/js/date.js',
            'text/javascript'
        )->appendFile(
            '/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
            'text/javascript'
        )->appendFile(
            '/assets/portfolio-insurance-quote/js/renewalDatePicker.js',
            'text/javascript'
        );

        // Add 'how did you hear about us?' element
        $this->addElement('select', 'how_hear', array(
            'label'     => '',
            'required'  => false,
            // TODO: Remove the hard coded options
            'multiOptions' => array(
                '' => 'Please Select',
                'Letting Agent' => 'Letting Agent',
                'Personal Recommendation' => 'Personal Recommendation',
                'SMS' => 'SMS',
                'Email' => 'Email',
                'Letter' => 'Letter',
                'Internet Location' => 'Internet Location',
                'Publication' => 'Publication',
                'Other' => 'Other'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select How did you heard about us',
                            'notEmptyInvalid' => 'Please select How did you heard about us'
                        )
                    )
                )
            )
        ));
        
        // Other Information
        $this->addElement('text', 'other', array(
            'label'     => '',
            'required'  => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Other Information'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));

        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'portfolio-insurance-quote/step3.phtml'))
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
    
    /**
     * Returns errors flattened into a 2d array
     *
     * @return array
     */
    public function getMessagesFlattened() {
        return $this->getMessages();
    }
    
    public function isValid($postData) {
        if(isset($postData['how_hear']) && $postData['how_hear'] == "Other"){
             $this->getElement('other')->setRequired(true);
        }
        return parent::isValid($postData);
    }
}
?>
