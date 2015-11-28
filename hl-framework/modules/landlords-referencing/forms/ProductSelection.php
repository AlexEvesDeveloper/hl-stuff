<?php

class LandlordsReferencing_Form_ProductSelection extends Zend_Form
{
    public function init()
    {
        //Reference subject title element
        $this->addElement('select', 'personal_title', array(
            'label'     => 'Tenant Title',
            'required'  => true,
            'multiOptions' => array(
                '' => 'Not Known',
                'Mr' => 'Mr',
                'Ms' => 'Ms',
                'Mrs' => 'Mrs',
                'Miss' => 'Miss',
                'Dr' => 'Dr',
                'Prof' => 'Professor',
                'Sir' => 'Sir'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select the tenant title',
                            'notEmptyInvalid' => 'Please select a valid tenant title'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));
    
        //First name entry
        $this->addElement('text', 'first_name', array(
            'label'      => 'Tenant First Name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the tenant\'s first name'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'name',
                'class' => 'form-control',
            )
        ));
        
        //Last name entry
        $this->addElement('text', 'last_name', array(
            'label'      => 'Tenant Last Name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the tenant\'s last name'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'name',
                'class' => 'form-control',
            )
        ));

        // Email entry
        $this->addElement('text', 'email', array(
            'label'      => 'Tenant Email Address',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the tenant\'s email address'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'class' => 'form-control',
            )
        ));

        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID_HOSTNAME    => "Domain name invalid in email address",
                Zend_Validate_EmailAddress::INVALID_FORMAT      => "Invalid email address"
            )
        );
        $this->getElement('email')->addValidator($emailValidator);
        
        // Add share of rent element
        $this->addElement('text', 'share_of_rent', array(
            'label' => 'Share of rent per month (&pound;)',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the share of rent per month'
                        )
                    ),
                    'Digits', true, array(
                        'messages' => array(
                            'notDigits' => 'Please enter the share of rent per month',
                            'digitsStringEmpty' => 'Please enter the Share of rent per month'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'currency',
                'class' => 'currency form-control',
            )
        ));
        
        // List the products based on the user choice.
        $productManager = new Manager_Referencing_Product();
        $session = new Zend_Session_Namespace('referencing_global');
        $productList = array();
        
        if ($session->displayRentGuaranteeProducts) {
            $productVariable = Model_Referencing_ProductVariables::RENT_GUARANTEE;
            $products = $productManager->getByVariable($productVariable);
            
            foreach($products as $product) {
                if (!preg_match("/international/i", $product->name)) {
                    $productList[$product->key] = strtoupper($product->name);
                }
            }
        }
        else {
            $productVariable = Model_Referencing_ProductVariables::NON_RENT_GUARANTEE;
            $products = $productManager->getByVariable($productVariable);
            
            $productSelection = new Model_Referencing_ProductSelection();
            $productSelection->referenceId = 0;
            $productSelection->duration = 0;
            
            foreach ($products as $product) {
                if (!preg_match("/international/i", $product->name)) {
                    $productSelection->product = $product;
                    $price = $productManager->getPrice($productSelection);
                    $productList[$product->key] = strtoupper($product->name) . " (" . $price . " + VAT)";
                }
            }
        }
        
        $this->addElement('radio', 'product_choice', array(
            'required'  => true,
            'multiOptions' => $productList,
            'separator' => '',
            'label_placement' => 'prepend',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your product choice',
                            'notEmptyInvalid' => 'Please select a valid product choice'
                        )
                    )
                )
            )
        ));
        
        //Identify if we need to indicate duration.
        if($session->displayRentGuaranteeProducts) {

            //Determine the allowable durations... Needs to be done in ajax

            //Display duration box.
            $this->addElement('select', 'product_duration', array(
                'label'     => 'Product Duration (months)',
                'required'  => true,
                'multiOptions' => array(
                    6 => '6',
                    12 => '12'),
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                            'messages' => array(
                                'isEmpty' => 'Please select the product duration',
                                'notEmptyInvalid' => 'Please select a valid product duration'
                            )
                        )
                    )
                ),
                'attribs' => array(
                    'class' => 'form-control',
                )
            ));
        }
        
        //Completion method element
        $this->addElement('select', 'completion_method', array(
            'label'     => 'Completion Method',
            'required'  => true,
            'multiOptions' => array(
                Model_Referencing_ReferenceCompletionMethods::ONE_STEP => 'Complete Information Now',
                Model_Referencing_ReferenceCompletionMethods::TWO_STEP => 'Email to Tenant'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select the completion method',
                            'notEmptyInvalid' => 'Please select a valid completion method'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'landlords-referencing/product-selection.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
    
    
    public function isValid($formData = array())
    {
        if (isset($formData['completion_method']) && $formData['completion_method'] == "2") {
             $this->getElement('email')->setRequired(true);
        }
        else {
            $this->getElement('email')->setRequired(false);
        }

        return parent::isValid($formData);
    }
    
    public function saveData()
    {
        $session = new Zend_Session_Namespace('referencing_global');
        $data = $this->getValues();

        $referenceManager = new Manager_Referencing_Reference();
        $reference = $referenceManager->getReference($session->referenceId);

        // Update the completion method (complete now, or email link to tenant).
        $reference->completionMethod = $data['completion_method'];
        
        // Update the reference subject.
        if (empty($reference->referenceSubject)) {

            $referenceSubjectManager = new Manager_Referencing_ReferenceSubject();
            $reference->referenceSubject = $referenceSubjectManager->insertPlaceholder($session->referenceId);
        }
        
        // Reference subject name details
        if (empty($reference->referenceSubject->name)) {
            $nameManager = new Manager_Core_Name();
            $reference->referenceSubject->name = $nameManager->createName();
        }

        $reference->referenceSubject->name->title = $data['personal_title'];
        $reference->referenceSubject->name->firstName = $data['first_name'];
        $reference->referenceSubject->name->lastName = $data['last_name'];

        //Reference subject contact details
        if (empty($reference->referenceSubject->contactDetails)) {
            $contactDetailsManager = new Manager_Core_ContactDetails();
            $reference->referenceSubject->contactDetails = $contactDetailsManager->createContactDetails();
        }

        $reference->referenceSubject->contactDetails->email1 = $data['email'];

        //Misc details.
        $reference->referenceSubject->type = Model_Referencing_ReferenceSubjectTypes::TENANT;
        $reference->referenceSubject->shareOfRent = new Zend_Currency(
            array(
                'value' => $data['share_of_rent'],
                'precision' => 0
            )
        );

        //Product details.
        if(empty($reference->productSelection)) {
            $productSelectionManager = new Manager_Referencing_ProductSelection();
            $reference->productSelection = $productSelectionManager->insertPlaceholder($session->referenceId);
        }

        $productDatasource = new Datasource_Referencing_Product();
        $reference->productSelection->product = $productDatasource->getById($data['product_choice']);

        if (empty($data['product_duration'])) {
            $reference->productSelection->duration = 0;
        }
        else {
            $reference->productSelection->duration = $data['product_duration'];
        }

        // And update...
        $referenceManager->updateReference($reference);

        // Ensure the product selection by the user is updated in the session, ensuring correct
        // navigations...
        $session->productName = $reference->productSelection->product->key;
    }

    public function getMessagesFlattened()
    {
        return $this->getMessages();
    }
}
