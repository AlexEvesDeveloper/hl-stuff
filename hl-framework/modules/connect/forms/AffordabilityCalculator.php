<?php

class Connect_Form_AffordabilityCalculator extends Zend_Form {

    /**
     * Create Affordability Calculator form.
     *
     * @return void
     */
    public function init() {

    	
        $this->addElement('text', 'fb_rent', array(
            'label'      => 'Monthly Rent',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your username',
                            'notEmptyInvalid' => 'Please enter your username'
                        )
                    )
                )
            )
        ));

        $this->addElement('text', 'fb_tenant_income', array(
            'label'      => 'Tenant Income/Year',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your username',
                            'notEmptyInvalid' => 'Please enter your username'
                        )
                    )
                )
            )
        ));

        $this->addElement('text', 'fb_guarantor_income', array(
            'label'      => 'Guarantor Income/Year',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your username',
                            'notEmptyInvalid' => 'Please enter your username'
                        )
                    )
                )
            )
        ));        
         

        $this->addElement('button','calculate', array(
        	'label'		=> 'Calculate'
        ));
  
        // Remove the label from the submit button
        $element = $this->getElement('calculate');
        $element->setAttrib('onclick', 'Calculate();');
        $element->removeDecorator('label');
                
    	// Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            'Label',
            'Errors',
            array('HtmlTag', array('tag' => 'div')),
        ));
    }
} 