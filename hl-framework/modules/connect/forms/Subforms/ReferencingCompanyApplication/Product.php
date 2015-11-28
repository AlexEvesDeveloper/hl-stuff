<?php

class Connect_Form_Subforms_ReferencingCompanyApplication_Product extends Zend_Form_SubForm {
    /**
     * Create product subform
     *
     * @return void
     */
    public function init() {
  		
		// Add product selection element
        $this->addElement('select', 'product', array(
            'label'     => 'Product',
            'required'  => true,
            'multiOptions' => array(
                ''              => '--- Please select ---'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a product',
                            'notEmptyInvalid' => 'Please select a product'
                        )
                    )
                )
            )
        ));

		// Add previously_selected_product hidden element
        $this->addElement('hidden', 'previously_selected_product', array(
            'required'  => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('Digits', true, array(
						'messages' => array(
							'notDigits' => 'Invalid Product ID'
                        )
                    )
                ),
            )
        ));

        $this->getElement('product')->setRegisterInArrayValidator(false);
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'referencing/subforms/company-application-product.phtml'))
        ));
		
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }

}