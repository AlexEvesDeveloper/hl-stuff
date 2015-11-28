<?php

class Connect_Form_Rentguarantee_DeclineRenewal extends Zend_Form
{
    /**
     * Initialise the form
     *
     * @return void
     */
    public function init()
    {
        $this->setMethod('post');
        
        
        // Add non renewal reason drop down
        $this->addElement('select', 'nonrenewal_reason', array(
            'label'     => 'Select Reason',
            'required'  => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a valid reason',
                        )
                    )
                )
            ),
            'decorators' => array
            (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            )
        ));
        
        // Other product chosen
        $this->addElement('textarea', 'other_product', array
        (
            'label'     => 'Other product',
            'required'  => false,
            'decorators' => array
            (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            )
        ));
        
        // Other reason chosen
        $this->addElement('textarea', 'other_reason', array
        (
            'label'     => 'Other reason',
            'required'  => false,
            'decorators' => array
            (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            )
        ));
        
        // Continue and Back
        $this->addElement('submit', 'formsubmit_back', array('name' => 'formsubmit_back', 'label' => 'Back'));
        $this->addElement('submit', 'formsubmit_continue', array('name' => 'formsubmit_continue', 'label' => 'Continue'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'rentguarantee/subforms/rentguarantee-decline-renewal.phtml'))
        ));
    }
    
    /**
     * Apply the reason codes to the reason drop down, depends on agent fsa status abbr code.
     * Always clears any existing options
     *
     * @param string $status FSA status abbr of agent
     * @return void
     */
    public function applyReasonCodes($status)
    {
        $nonrenewal_reason = $this->getElement('nonrenewal_reason');
        
        if ($status != "IO" && $status != "IAR")
        {
            $arrRenewalReasons = array
            (
                "tenant_arrears"	        => "Tenant in arrears",
                "tenant_moved_out"	        => "Tenant moved out",
                "landlord_accepting_risk"	=> "Landlord happy to accept the risk",
                "other_product"		        => "Other Product",
                "other"				        => "Other"
            );
        }
        else
        {
            $arrRenewalReasons = array
            (
                "tenant_arrears"	        => "Tenant in arrears",
                "tenant_moved_out"	        => "Tenant moved out",
                "other_product"		        => "Other Product",
                "other"				        => "Other"
            );
        }
        
        $nonrenewal_reason->clearMultiOptions();
        foreach ($arrRenewalReasons as $reason_name => $reason_value)
            $nonrenewal_reason->addMultiOption($reason_name, $reason_value);
    }
}