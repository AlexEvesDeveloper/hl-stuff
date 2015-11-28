<?php

class Connect_Form_ReferencingSearch extends Zend_Form {

    /**
     * Create reference search form.
     *
     * @return void
     */
    public function init() {

        // TODO: This is a stand-in until there's a product manager to give the
        //   names of all active products
        $productList  = array('' => 'Select Product');
        $productManager = new Manager_Referencing_Product();
        $productArray = $productManager->getAll(true);
        foreach($productArray as $product) {
            $productList[$product->name] = $product->name;
        }

        $this->setMethod('get');

        // Add reference number element
        $this->addElement('text', 'refno', array(
            'label'      => 'Reference Number',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^\d+\.?\d+\/?\d+$/',
                        'messages' => 'Reference Number must contain digits and an optional full stop and/or forward slash'
                    )
                )
            )
        ));

        // Add applicant first name element
        $this->addElement('text', 'firstname', array(
            'label'      => 'Applicant First Name',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']+$/i',
                        'messages' => 'Applicant First Name must contain alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                    )
                )
            )
        ));

        // Add applicant last name element
        $this->addElement('text', 'lastname', array(
            'label'      => 'Applicant Last Name',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \']+$/i',
                        'messages' => 'Applicant Last Name must contain alphabetic characters and only basic punctuation (hyphen, space and single quote)'
                    )
                )
            )
        ));

        // Add property address element
        $this->addElement('text', 'address', array(
            'label'      => 'Property Address',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z0-9\-\ \'\,\.]+$/i',
                        'messages' => 'Property Address must contain alphanumeric characters and only basic punctuation (hyphen, space, single quote, comma and full stop)'
                    )
                )
            )
        ));

        // Add property town element
        $this->addElement('text', 'town', array(
            'label'      => 'Property Town',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z\-\ \'\,\.]+$/i',
                        'messages' => 'Property Town must contain alphabetic characters and only basic punctuation (hyphen, space, single quote, comma and full stop)'
                    )
                )
            )
        ));

        // Add postcode element
        $this->addElement('text', 'postcode', array(
            'label'      => 'Property Post Code',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'regex', true, array(
                        'pattern' => '/^[a-z0-9\ ]+$/i',
                        'messages' => 'Property Post Code must only contain alphanumeric characters and spaces'
                    )
                )
            )
        ));

        // Add state element
        $this->addElement('select', 'state', array(
            'label'     => 'State',
            'required'  => false,
            'multiOptions' => array(
                'Incomplete'    => 'Incomplete',
                'Complete'      => 'Complete',
                'Unknown'       => 'Search All'
            )
        ));

        // Add results per page element
        $this->addElement('select', 'rows', array(
            'label'     => 'Results per Page ',
            'required'  => false,
            'multiOptions' => array(
        		'' 		=> 'All',
                '10'    => '10',
                '25'    => '25',
        		'50'    => '50',
                '100'   => '100'
            )
        ));

        // Add page element
        $this->addElement('hidden', 'page', array(
            'label'     => '',
        	'value'		=> '1'
        
        ));  
        
        // Add type element
        $this->addElement('select', 'type', array(
            'label'     => 'Product',
            'required'  => false,
            'multiOptions' => $productList
        ));

        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            array('HtmlTag', array('tag' => 'div')),
        ));

        // Add search button
        $this->addElement('submit', 'search', array('label' => 'Search'));
    }
}