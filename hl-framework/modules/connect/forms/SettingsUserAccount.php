<?php

class Connect_Form_SettingsUserAccount extends Zend_Form_Multilevel {

    protected $_role;

    public function __construct($role = Model_Core_Agent_UserRole::BASIC) {

        $this->_role = $role;
        return parent::__construct();
    }

    /**
     * Create user details form (single user).
     *
     * @return void
     */
    public function init() {

        $this->setMethod('post');
        $this->addSubForm(new Connect_Form_Subforms_Settings_UserAccount($this->_role), 'subform_useraccount');

        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            'Label',
            'Errors',
            array('HtmlTag', array('tag' => 'div')),
        ));
    }
}