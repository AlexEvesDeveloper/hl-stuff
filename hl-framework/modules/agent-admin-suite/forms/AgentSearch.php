<?php

class AgentAdminSuite_Form_AgentSearch extends Zend_Form {

	public function init() {
		$this->setAction('/admin-suite/agentsearch')
             ->setMethod('post');
        
        $agentNumber = $this->createElement('text', 'agentnumber', array(
            'label' =>  'Agent Number'
        ));
        $this->addElement($agentNumber);
        
        $hNumber = $this->createElement('text', 'hnumber', array(
            'label' =>  'H Number'
        ));
        $this->addElement($hNumber);
        
        $submit = $this->createElement('submit', 'search', array(
            'class' => 'submit'
        ));
        $submit->removeDecorator('Label');
        $this->addElement($submit);
	}

}

?>