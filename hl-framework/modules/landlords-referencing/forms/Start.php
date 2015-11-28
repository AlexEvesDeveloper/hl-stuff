<?php
class LandlordsReferencing_Form_Start extends Zend_Form {

    public function init() {
    	
    	// Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'landlords-referencing/start.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}
?>