<?php

class LettingAgents_Form_TermsAndConditions extends Zend_Form{
    public function init()
    {
		$this->addElement('text', 'homelet_admin_code', array(
            'label'     => 'If you work for HomeLet, tell us your unique code.',
            'required'  => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'If you work for HomeLet, tell us your unique code',
                            'notEmptyInvalid' => 'Invalid HomeLet Code'
                        )
                    )
                )
            )
        ));
    	
		$this->addElement('text', 'acceptance_name', array(
            'label'     => 'Enter your Forename and Surname.',
            'required'  => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your full name',
                            'notEmptyInvalid' => 'Invalid Name'
                        )
                    )
                )
            )
        ));

		$this->addElement('text', 'date_of_acceptance', array(
            'label'     => 'Today’s date (DD/MM/YYYY)',
            'required'  => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Date of acceptance',
                            'notEmptyInvalid' => 'Invalis date for date of acceptance'
                        )
                    )
                )
            )
        ));        
        

        $this->addElement('checkbox', 'confirm', array(
            'label'         => 'I confirm I’ve read the terms and conditions',
            'required'      => true,
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'You must agree to the terms and conditions'
                        )
                    )
                )
            )
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'terms-and-conditions.phtml'))
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
}