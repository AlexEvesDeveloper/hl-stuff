<?php

class LandlordsInsuranceQuote_Form_Subforms_PrestigeRentGuarantee extends Zend_Form_SubForm
{
    /**
     * Create prestige rent guarantee subform
     *
     * @return void
     */
    
    public function init() {
        // Add need contents insurance element
        $this->addElement('radio', 'need_prestige_rent_guarantee', array(
            'label'     => 'Do you need prestige rent guarantee?',
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
                            'isEmpty' => 'Please select if you need rent guarantee cover or not',
                            'notEmptyInvalid' => 'Please select if you need rent guarantee cover or not'
                        )
                    )
                )
            )
        ));
        
        // Add rent amount element
        $this->addElement('text', 'rent_amount', array(
            'label' => 'How much is the monthly rent?',
            'required' => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the monthly rent amount'
                        )
                    )
                ),
                array(
                    'GreaterThan', true, array(
                        'min' => 349,
                        'messages' => array(
                            'notGreaterThan' => 'Rent amount must be at least &pound;350'
                        )
                    )
                ),
                array(
                    'LessThan', true, array(
                        'max' => 3001,
                        'messages' => array(
                            'notLessThan' => 'Rent amount must be no more than &pound;3000'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'currency',
                'class'=>'currency form-control'
            )
        ));

        // Add a filter suitable for currency input - this strips anything after a decimal point and then anything
        //   non-digit such as pound symbols and commas
        $rentAmount = $this->getElement('rent_amount');
        $rentAmount->addFilter('callback', function($v) {
            return preg_replace(array('/\..+$/', '/\D/'), array('', ''), $v);
        });

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/prestige-rent-guarantee.phtml'))
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Strip all tags to prevent XSS errors - done iteratively so not to overwrite any existing filters
        foreach($this->getElements() as $element) {
            $element->addFilter('StripTags');
        }

        // Grab view and add the agent lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $view->headScript()->appendFile(
            '/assets/landlords-insurance-quote/js/rentGuarantee.js',
            'text/javascript'
        );
        
        // Grab view and add the agent lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
    }
    
    public function isValid($formData) {
        if (isset($formData['need_prestige_rent_guarantee']) && $formData['need_prestige_rent_guarantee'] == 'yes') {
            $this->getElement('rent_amount')->setRequired(true);
        }
        else {
            $this->getElement('rent_amount')->setRequired(false);
        }

        return parent::isValid($formData);
    }
}