<?php

class Connect_Form_SettingsEmailAddresses extends Zend_Form_Multilevel {

    public function __construct() {

        parent::__construct();

        $this->clearDecorators();
        $this->setDecorators(array(
            'FormElements'
        ));
        $this->setElementDecorators(array(
            'ViewHelper',
            'Label',
            'Errors',
            array(
                'data' => 'HtmlTag',
                array(
                    'tag' => 'div'
                )
            )
        ));

    }

    /**
     * Create e-mail addresses form.
     *
     * @return void
     */
    public function init() {

        $this->setMethod('post');
        $this->addSubForm(new Connect_Form_Subforms_Settings_EmailAddresses(), 'subform_emailaddresses');

        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            'Label',
            'Errors',
            array('HtmlTag', array('tag' => 'div')),
        ));

    }

    /**
     * Overridden isValid() method for pre-validation code.
     *
     * @param array $formData data typically from a POST or GET request.
     *
     * @return bool
     */
    public function isValid($formData = array()) {

        // Call original isValid()
        return parent::isValid($formData);
    }
}