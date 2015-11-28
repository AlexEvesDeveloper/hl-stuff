<?php
class LandlordsReferencing_Form_DownloadApplication extends Zend_Form {

    public function init() {
    	
        $this->setMethod('post');
        
        // Email entry
        $this->addElement('select', 'application_select', array(
            'required'  => true,
            'multiOptions' => array(
            	'' => 'Please Select',
        		'1' => 'Individual applicant form',
		        '2' => 'Student applicant form',
        		'3' => 'Unemployed applicant form',
        		'4' => 'Company form',
        		'5' => 'Guarantor form',
        	),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select an application form',
                            'notEmptyInvalid' => 'Please select an application form'
                        )
                    )
                )
            )
        ));
        
        
        // Set up the element decorators
        $this->setElementDecorators(array (
            'ViewHelper',
            'Label',
            'Errors',
            array('HtmlTag', array('tag' => 'div')),
        ));       
    }
    
    public function isValid($data) {
    	
    	//Ensure a value is selected in the drop-down.
    	if($data['application_select'] == '') {
    		
    		return false;
    	}
    	
    	if(!is_numeric($data['application_select'])) {
    		
    		return false;
    	}
    	
    	// Call original isValid()
        return parent::isValid($data);
    }
}
?>