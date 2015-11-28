<?php
/**
* Tenant occupation subform 
* @author John Burrin
* @since 1.4
*/
class Form_PortfolioInsuranceQuote_Subforms_TenantOccupation extends Zend_Form_SubForm
{
    public function init()
    {
        // Add Employment status element
        $this->addElement('select', 'employment_status', array(
            'label'     => 'Tenant status',
            'required'  => true,
            // TODO: Remove the hard coded options
            'multiOptions' => array(
                '' => 'Please Select',
                'Employed' => 'Employed',
                'Unemployed' => 'Unemployed',
                'DSS with AST' => 'DSS with AST',
                'DSS' => 'DSS without AST',
                'Student' => 'Student',
                'Self Employed' => 'Self Employed',
                'Retired' => 'Retired',
                'Unknown' => 'Unknown'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select tenants occupation status',
                            'notEmptyInvalid' => 'Please select tenants occupation status'
                        )
                    )
                )
            )
        ));
        
        $this->employment_status->setAttrib('onchange','employmentChange();');
        
       #
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'portfolio-insurance-quote/subforms/tenant-occupation.phtml'))
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Grab view and add the address lookup JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
    }
}
?>