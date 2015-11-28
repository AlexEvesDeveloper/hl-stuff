<?php

class TenantsInsuranceQuote_Form_Subforms_Bicycle extends Zend_Form_SubForm
{
    /**
     * Create bicycle subform
     *
     * @return void
     */
    public function init()
    {
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        $bike = new Datasource_Insurance_Policy_Cycles($pageSession->CustomerRefNo, $pageSession->PolicyNumber);

        $maximumReached = ($bike->countBikes() == $bike->maxBicycles);

        // Grab view and add the bicycle JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');

        // JavaScript that shows or hides promo panels, depending on values in subform
        $view->headScript()->appendFile('/assets/tenants-insurance-quote/js/bicycle.js', 'text/javascript');

        // Add own a bicycle element
        $this->addElement('radio', 'bicycle', array(
            'label'     => 'Would you like to cover your bicycle?',
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
                            'isEmpty' => 'Please select whether you own a bicycle',
                            'notEmptyInvalid' => 'Please select whether own a bicycle'
                        )
                    )
                )
            )
        ));

        // Add fields for all new bicycle values (the visible fields associated with the "Add bike" button)
        $this->addElement('text', "bicycle_make", array(
            'label'      => "Make of bicycle",
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a bicycle make',
                            'notEmptyInvalid' => 'Please enter a bicycle make'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z\ \-\,\(\)\.\']{3,}$/i',
                        'messages' => 'Bicycle make must contain at least three alphanumeric characters and only basic punctuation (space, hyphen, comma, round brackets, full stop and single quote)'
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'name',
                'class' => 'form-control',
            )
        ));

        $this->addElement('text', "bicycle_model", array(
            'label'      => "Model of bicycle",
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a bicycle model',
                            'notEmptyInvalid' => 'Please enter a bicycle model'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z\ \-\,\(\)\.\']{3,}$/i',
                        'messages' => 'Bicycle model must contain at least three alphanumeric characters and only basic punctuation (space, hyphen, comma, round brackets, full stop and single quote)'
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'name',
                'class' => 'form-control',
            )
        ));
        $this->addElement('text', "bicycle_serial", array(
            'label'      => "Serial number of bicycle",
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter a bicycle serial',
                            'notEmptyInvalid' => 'Please enter a bicycle serial'
                        )
                    )
                ),
                array(
                    'regex', true, array(
                        'pattern' => '/^[0-9a-z\ \-\,\(\)\.\']{5,}$/i',
                        'messages' => 'Bicycle serial must contain at least five alphanumeric characters and only basic punctuation (space, hyphen, comma, round brackets, full stop and single quote)'
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'name',
                'class' => 'form-control',
            )
        ));
        $this->addElement('text', "bicycle_value", array(
            'required'   => false,
            'attribs' 	=> array(
                'class'=>'currency'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the bicycle value',
                            'notEmptyInvalid' => 'Please enter the bicycle value'
                        )
                    )
                ),
                array(
                    'GreaterThan', true, array(
                        'min' => 200,
                        'messages' => 'Bicycle value must be above &pound;200'
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
        $bicycleValue = $this->getElement('bicycle_value');
        $bicycleValue->addFilter('callback', function($v) {
            return preg_replace('/[^\d\.]/', '', $v);
        });

        if ($maximumReached) {
            // Disable the bike entry forms
            $this->bicycle_make->setAttrib('disabled', 'disabled');
            $this->bicycle_model->setAttrib('disabled', 'disabled');
            $this->bicycle_serial->setAttrib('disabled', 'disabled');
            $this->bicycle_value->setAttrib('disabled', 'disabled');
        }

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/bicycle.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
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

        if (isset($formData['bicycle']) && $formData['bicycle'] == 'yes')
        {
            if (trim($formData['bicycle_make']) != '' || trim($formData['bicycle_model']) != '' || trim($formData['bicycle_serial']) != '' || trim($formData['bicycle_value']) != '')
            {
                $this->getElement('bicycle_make')->setRequired(true);
                $this->getElement('bicycle_model')->setRequired(true);
                $this->getElement('bicycle_value')->setRequired(true);
            }
        }

        // Call original isValid()
        return parent::isValid($formData);

    }
}