<?php
/**
* Class definition for the form elements in the subform Personal Details
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_InterestedProducts extends Zend_Form_SubForm
{
	public function init(){
    	$this->addElement('multiCheckbox', 'interested_products', array(
            'label'     => 'What products are you interested in hearing about?',
            'required'  => false,
            'multiOptions' => array(
                'Rent Guarantee' => 'Rent Guarantee',
                'Landlords Insurance' => 'Landlords Insurance',
                'Professional Indemnity Insurance' => 'Professional Indemnity Insurance',
                'Office Insurance' => 'Office Insurance',
                'Tenants Insurance' => 'Tenants Insurance',
                'Tenant Referencing' => 'Tenant Referencing',
                'Other' => 'Other'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'What products are you interested in',
                            'notEmptyInvalid' => 'Invalid selection for interested products'
                        )
                    )
                )
            )
        ));        
             

       
	    // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/interested-products.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}