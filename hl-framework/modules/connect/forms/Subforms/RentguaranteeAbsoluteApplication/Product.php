<?php

class Connect_Form_Subforms_RentguaranteeAbsoluteApplication_Product extends Zend_Form_SubForm {

    protected $_absoluteType;

    public function __construct($absoluteType = Model_Core_Agent_AbsoluteType::ABSOLUTE)
    {
        $this->_absoluteType = $absoluteType;
        return parent::__construct();
    }

    /**
     * Create product subform
     *
     * @return void
     */
    public function init()
    {
        // Add product selection element
        $this->addElement('radio', 'product', array(
            'label'     => 'Product Type',
            'required'  => true,
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
        $product = $this->getElement('product');
        switch ($this->_absoluteType) {
            case Model_Core_Agent_AbsoluteType::PROMISE:
                $product->setMultiOptions(array(
                    'promise' => 'Promise',
                    'promiseplus' => 'Promise Plus'
                ));
                break;
            case Model_Core_Agent_AbsoluteType::ESSENTIAL:
                $product->setMultiOptions(array(
                    'essential' => 'Essential',
                    'essentialzero' => 'Essential Zero Excess'
                ));
                break;
            case Model_Core_Agent_AbsoluteType::ABSOLUTE:
            default:
                $product->setMultiOptions(array(
                    'absolute' => 'Absolute',
                    'absolutezero' => 'Absolute Zero'
                ));
                break;
        }

        // Add policy term selection element
        $this->addElement('radio', 'term', array(
            'label'     => 'Policy Term',
            'required'  => true,
            'multiOptions' => array(
                '6' =>  '6',
                '12' => '12'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a policy term',
                            'notEmptyInvalid' => 'Please select a policy term'
                        )
                    )
                )
            )
        ));

        // Add reference type selection element
        $this->addElement('radio', 'type', array(
            'label'     => 'Reference Type',
            'required'  => true,
            'multiOptions' => array(
                'insight' => 'Insight',
                'enhance' => 'Enhance',
                'optimum' => 'Optimum',
                'combination' => 'Combination'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a reference type',
                            'notEmptyInvalid' => 'Please select a reference type'
                        )
                    )
                )
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'rentguarantee/subforms/absolute-application-product.phtml'))
        ));

		$this->setElementFilters(array('StripTags'));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
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

        // Insight can only be selected for product type "absolute", "promise" or "essential"
        if (isset($formData['type']) && trim($formData['type']) == 'insight') {
            if (
                isset($formData['product']) &&
                (
                    trim($formData['product']) == 'absolute' ||
                    trim($formData['product']) == 'promise' ||
                    trim($formData['product']) == 'essential'
                )
            ) {
                // All OK, do nothing
            } else {
                // Force an error
                $this->addError('Reference type invalid');
            }
        }

        // Call original isValid()
        return parent::isValid($formData);
    }
}