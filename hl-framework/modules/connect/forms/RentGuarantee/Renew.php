<?php

class Connect_Form_Rentguarantee_Renew extends Zend_Form
{
    /**
     * Initialise the form
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');
        
        $this->addElement('hidden', 'landlordconsent');
        $this->addElement('hidden', 'policynumber');
        $this->addElement('hidden', 'pollength');
        $this->addElement('hidden', 'title');
        $this->addElement('hidden', 'firstname');
        $this->addElement('hidden', 'lastname');
        $this->addElement('hidden', 'riskaddress');
        $this->addElement('hidden', 'risktown');
        $this->addElement('hidden', 'riskpc');
        $this->addElement('hidden', 'date');
        $this->addElement('hidden', 'signature');
        $this->addElement('hidden', 'fsastatus');
        
        // term option
        $term = $this->addElement('radio', 'term', array('required'  => true,))->getElement('term');
        $term->addMultiOptions(array('6' => '6', '12' => '12'));
        
        // tenancytype option
        $tenancytype = $this->addElement('radio', 'tenancytype', array('required'  => true,))->getElement('tenancytype');
        $tenancytype->addMultiOptions(array('1' => '1', '2' => '2','3'=>'3'));
        
        // rgoffer option
        $rgoffer = $this->addElement('radio', 'rgoffer', array('required'  => true,))->getElement('rgoffer');
        $rgoffer->addMultiOptions(array('1' => '2', '2' => '2', '3'=>'3'));
        // Rent of property
        $this->addElement('text', 'rent', array('required'  => false,));
        
        // Rent share of tenant
        $this->addElement('text', 'rentshare', array('required'  => true,));
        
        // Continue and Back
        $this->addElement('submit', 'formsubmit_back', array('name' => 'formsubmit', 'label' => 'Back'));
        $this->addElement('submit', 'formsubmit_continue', array('name' => 'formsubmit', 'label' => 'Continue'));
    }
}
