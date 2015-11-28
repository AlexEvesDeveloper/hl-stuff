<?php

class LandlordsReferencing_Form_Subforms_PropertyMisc extends Zend_Form_SubForm
{
    public function init()
    {
        $this->addElement('select', 'property_let_type', array(
            'label'     => 'Property Let Type',
            'required'  => false,
            'multiOptions' => array(
                '' => 'Please select',
                '1' => 'Let Only',
                '2' => 'Managed',
                '3' => 'Rent Collect'),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        $this->addElement('select', 'property_bedrooms', array(
            'label'     => 'How many bedrooms does this property have?',
            'required'  => false,
            'multiOptions' => array(
                '' => 'Please select',
                '0' => 0,
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4,
                '5' => 5,
                '6' => 6,
                '7' => '7+'),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        $this->addElement('select', 'property_type', array(
            'label'     => 'Property type',
            'required'  => false,
            'multiOptions' => array(
                '' => 'Please select',
                '1' => 'Detached',
                '2' => 'Semi Detached',
                '3' => 'Flat',
                '4' => 'Terraced',
                '5' => 'Bungalow'),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        $this->addElement('select', 'property_birth', array(
            'label'     => 'When was the property built?',
            'required'  => false,
            'multiOptions' => array(
                '' => 'Please select',
                '1' => 'Pre 1850',
                '2' => '1850 - 1899',
                '3' => '1900 - 1919',
                '4' => '1920 - 1945',
                '5' => '1946 - 1979',
                '6' => '1980 - 1990',
                '7' => '1991 - 2000',
                '8' => '2001 - 2010',
                '9' => '2011+'),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));


        // Add total rent element
        $this->addElement('text', 'total_rent', array(
            'label'     => 'Total rent per month',
            'required'  => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the total rent per month'
                        )
                    )
                ),
                array('Digits', true, array(
                        'messages' => array(
                            'notDigits' => 'Please enter a valid rental amount'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'currency',
                'class'=>'currency form-control',
            )
        ));


        //Tenancy term element
        $this->addElement('select', 'tenancy_term', array(
            'label'     => 'Tenancy Term (months)',
            'required'  => true,
            'multiOptions' => array(
                '6' => 6,
                '12' => 12,
                '18' => 18,
                '24' => 24),
            'validators' => array(
                array('Digits', true, array(
                        'messages' => array(
                            'notDigits' => 'Please select the tenancy term'
                        )
                    )
                ),
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select the tenancy term',
                            'notEmptyInvalid' => 'Please select a valid tenancy term'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class'=>'form-control',
            )
        ));


        //Number of tenants element
        $this->addElement('select', 'no_of_tenants', array(
            'label'     => 'Number of tenants',
            'required'  => true,
            'multiOptions' => array(
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4,
                '5' => 5,
                '6' => 6,
                '7' => 7,
                '8' => 8,
                '9' => 9,
                '10' => 10),
            'validators' => array(
                array('Digits', true, array(
                        'messages' => array(
                            'notDigits' => 'Please select the number of tenants'
                        )
                    )
                ),
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select the number of tenants',
                            'notEmptyInvalid' => 'Please select a valid number of tenants'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class'=>'form-control',
            )
        ));


        //The tenancy start date element
        $this->addElement('text', 'tenancy_start_date', array(
            'label'     => 'Tenancy start date (dd/mm/yyyy)',
            'required'  => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the tenancy start date.'
                        )
                    )
                ),
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'date',
                'class'=>'form-control',
            )
        ));
        
        $tenancyStartDate = $this->getElement('tenancy_start_date');
        $validator = new Zend_Validate_DateCompare();
        $validator->minimum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 60 * 60 * 24 * 30);
        $validator->setMessages(array(
            'msgMinimum' => 'Tenancy start date cannot be in the past',
            'msgMaximum' => 'Tenancy start date cannot be more than 30 days in the future'
        ));
        $tenancyStartDate->addValidator($validator, true);

        
        //Grab view and add the date picker JavaScript files into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headLink()->appendStylesheet(
            '/assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css',
            'screen'
        );
        
        $view->headScript()->appendFile(
            '/assets/vendor/jquery-date/js/date.js',
            'text/javascript'
            )->appendFile(
                '/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
                'text/javascript'
            )->appendFile(
                '/assets/landlords-referencing/js/referencingDatePicker.js',
                'text/javascript'
        );


        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/property-misc.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}