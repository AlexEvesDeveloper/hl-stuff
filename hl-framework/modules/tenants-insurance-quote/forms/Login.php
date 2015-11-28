<?php
class TenantsInsuranceQuote_Form_Login extends Zend_Form {

    public function init()
    {
        $this->setMethod('post');
        
        // Email entry
        $this->addElement('text', 'email', array(
            'label'      => 'Email address',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your email address'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        
        // Modify email error messages & add validator
        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Invalid email address"
            )
        );
        $this->getElement('email')->addValidator($emailValidator);
        
        
        // Password entry
        $this->addElement('password', 'password', array(
            'required'  => true,
            'label'     => 'Password',
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter your password'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes'
            )
        ));
        
        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            'Label',
            'Errors',
            array('HtmlTag', array('tag' => 'div')),
        ));
                
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'    => true,
            'label'     => 'Login',
            'class'     => 'button noalt'
        ));

        // Add a resend validation link button
        $this->addElement('submit', 'resendValidation', array(
            'ignore'    => true,
            'label'     => 'Resend Account Validation',
            'class'     => 'button noalt'
        ));

         // Add a forgotten password button
        $this->addElement('submit', 'forgottenPassword', array(
            'ignore'    => true,
            'label'     => 'Resend Password',
            'class'     => 'button noalt'
        ));
        
        // Remove the label from the submit button
        $element = $this->getElement('submit');
        $element->removeDecorator('label');
        
        $element = $this->getElement('forgottenPassword');
        $element->removeDecorator('label');
        
        // Set up the decorator on the form and add in decorators which are removed
        $this->addDecorator('FormElements')
            ->addDecorator(
                'HtmlTag', 
                array('tag' => 'div', 'class' => 'form_section one-col')
                )
            ->addDecorator('Form');
    }

    public function isValid($data)
    {
        // add email to request data as
        // isValid seems to remove any data currently loaded into the form
        $email = $this->getElement('email')->getValue();
        $data['email'] = $email;

        $validationResult = parent::isValid($data);

        // Perform login validation
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('homelet_customer'));

        if ($data['password'] != '') {
            // Process login
            $customerManager = new Manager_Core_Customer();

            $adapter = $customerManager->getAuthAdapter(array('email' => $data['email'], 'password' => $data['password']));
            $result = $auth->authenticate($adapter);

            if ($result->isValid()) {
                $email = $this->getElement('email');
                $newCustomer = $customerManager->getCustomerByEmailAddress($email->getValue());
                if ($newCustomer->getEmailValidated() !== true) {
                    $auth->clearIdentity();
                    $this->setDescription("Unfortunately you haven't validated your email address yet. We've sent you an email which includes a link to validate your My HomeLet account. You'll need to validate your account to continue. If you've not received your validation email or if you're unable to access your account, please call us on 0845 117 6000.");

                    return false;
                }

                $storage = $auth->getStorage();
                $storage->write($adapter->getResultRowObject(array(
                    'title',
                    'first_name',
                    'last_name',
                    'email_address',
                    'id'
                )));
            }
            else {
                $this->setDescription('Your account details are incorrect, please try again');
                return false;
            }
        }

        // All valid above, return parents validation result
        return $validationResult;
    }
}
