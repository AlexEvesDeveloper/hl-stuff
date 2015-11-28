<?php

class Account_Form_Subforms_LoggedIn extends Zend_Form_SubForm {

    /**
     * @var Zend_Auth
     */
    private $auth;

    public function __construct(Zend_Auth $auth)
    {
        $this->auth = $auth;
        return parent::__construct();
    }

    public function init()
    {
        $customer = $this->auth->getStorage()->read();

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/logged-in.phtml', 'customer' => $customer))
        ));
    }
}
