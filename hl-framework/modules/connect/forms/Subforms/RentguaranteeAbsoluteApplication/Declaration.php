<?php

class Connect_Form_Subforms_RentguaranteeAbsoluteApplication_Declaration extends Zend_Form_SubForm {
    /**
     * Create disclosure and declaration subform
     *
     * @return void
     */
    public function init() {

        $this->addElement('checkbox', 'confirmation', array(
            'label'         => 'I confirm that the statements above are true to the best of my knowledge',
            'required'      => true,
            'checkedValue'  => '1',
            'uncheckedValue' => null, // Must be used to override default of '0' and force an error when left unchecked
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'You must agree to the statements to continue'
                        )
                    )
                )
            )
        ));


        // Add submit button
        $this->addElement('submit', 'complete', array(
            'label' => 'Complete'
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'rentguarantee/subforms/absolute-application-declaration.phtml'))
        ));

		$this->setElementFilters(array('StripTags'));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Remove submit button's label
        $element = $this->getElement('complete');
        $element->removeDecorator('label');
    }

}