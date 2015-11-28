<?php

class Connect_Form_Subforms_ReferencingCompanyApplication_Additional extends Zend_Form_SubForm {
    /**
     * Create additional information subform
     *
     * @return void
     */
    public function init() {

        // Add profit and loss element
        $this->addElement('file', 'profit_and_loss', array(
            'label'      => 'Profit and loss accounts file',
            'required'   => false,
            'filters'    => array('StringTrim')
        ));

        // Add additional information element
        $this->addElement('textarea', 'additional_info', array(
            'label'      => 'Additional information',
            'required'   => false,
            'filters'    => array('StringTrim')
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'referencing/subforms/company-application-additional.phtml'))
        ));

        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        $file = $this->getElement('profit_and_loss');
        
        $file->setDecorators(array(
        	array('File', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }

}