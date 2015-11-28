<?php
/**
* Class definition for the form elements in the subform Professional Indemnity
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_ProfessionalIndemnity extends Zend_Form_SubForm
{
	public function init(){
       
        // current_insurer Element
        $this->addElement('text', 'current_insurer', array(
            'label'      => 'Who is your current insurer?',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Enter your the name of you current insurer',
                            'notEmptyInvalid' => 'Current insurer name is invalid'
                        )
                    )
                )
            )
        )); 

        // policy_number Element
        $this->addElement('text', 'policy_number', array(
            'label'      => 'What is your policy number?',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Enter your policy number',
                            'notEmptyInvalid' => 'Policy number is invalid'
                        )
                    )
                )
            )
        ));        
 
        // retroactive_date Element
        $this->addElement('text', 'retroactive_date', array(
            'label'      => 'When did your policy start? (DD/MM/YYYY)',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Enter your policy start date',
                            'notEmptyInvalid' => 'Invaid policy start date'
                        )
                    )
                )
            )
        ));
 
        // next_renewal_date Element
        $this->addElement('text', 'next_renewal_date', array(
            'label'      => 'When is your policy up for renewal? (DD/MM/YYYY)',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Enter your policy renewal date',
                            'notEmptyInvalid' => 'Invaid policy renewal date'
                        )
                    )
                )
            )
        ));  
 
        // placing_broker Element
        $this->addElement('text', 'placing_broker', array(
            'label'      => 'Your broker or intermediary’s name (if you have one)',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Enter your brokers name',
                            'notEmptyInvalid' => 'Invaid broker name'
                        )
                    )
                )
            )
        ));  

        // indemnity Element
        $this->addElement('text', 'indemnity', array(
            'label'      => 'What is your current indemnity limit?',
            'required'   => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Enter current limit of indemnity',
                            'notEmptyInvalid' => 'Invalid value for current limit of indemnity'
                        )
                    )
                )
            )
        ));        

        // excesses Element
        $this->addElement('text', 'excesses', array(
            'label'      => 'What’s your current excess?',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Enter current value of excess',
                            'notEmptyInvalid' => 'Invalid value for current excesses'
                        )
                    )
                )
            )
        ));      
           
	    // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/professional-indemnity.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
    
   public function isValid($formData = array()) {
		//Zend_Debug::dump($formData);
		if(isset($formData['pi_cert']) && $formData['pi_cert'] != 'yes'){
			// un require some fields
			$this->getElement('current_insurer')->setRequired(false);
			$this->getElement('policy_number')->setRequired(false);
			$this->getElement('indemnity')->setRequired(false);
		}else{
			// Turn em back on
			$this->getElement('current_insurer')->setRequired(true);
			$this->getElement('policy_number')->setRequired(true);
			$this->getElement('indemnity')->setRequired(true);
		}
        // Call original isValid()
        return parent::isValid($formData);

    }    
}