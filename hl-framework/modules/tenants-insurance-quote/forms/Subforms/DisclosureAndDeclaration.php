<?php

class TenantsInsuranceQuote_Form_Subforms_DisclosureAndDeclaration extends Zend_Form_SubForm
{
    /**
     * Create disclosure and declaration agreement subform
     *
     * @return void
     */
    public function init()
    {
        // Add declaration statement agree element
        $this->addElement('checkbox', 'declaration_statement', array(
            'required'      => true,
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'You must agree to both declaration statements to continue'
                        )
                    )
                )
            )
        ));

        // Add declaration statement agree element
        $this->addElement('checkbox', 'declaration_notadvised', array(
            'required'      => true,
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'You must agree to both declaration statements to continue'
                        )
                    )
                )
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/disclosure-and-declaration.phtml'))
        ));

        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }

    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     *
     * @return bool
     */
    public function isValid($formData = array())
    {
        // Don't show both error messages, one is enough
        if ($formData['declaration_statement'] != 1 && $formData['declaration_notadvised'] != 1) {
            // Modify the mandatory-ness to give just one error
            $this->getElement('declaration_statement')->setRequired(true);
            $this->getElement('declaration_notadvised')->setRequired(false);
        }

        // Call original isValid()
        return parent::isValid($formData);
    }
}