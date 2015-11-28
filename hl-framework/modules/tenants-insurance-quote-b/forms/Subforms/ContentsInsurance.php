<?php

class TenantsInsuranceQuoteB_Form_Subforms_ContentsInsurance extends Zend_Form_SubForm
{
    /**
     * Create contents subform
     *
     * @return void
     */
    public function init()
    {
        // Add amount of cover (select) element
        $this->addElement('select', 'contents_cover_a', array(
            'label'     => 'Please select the amount of cover required',
            'required'  => true,
            //'filters'    => array('StripTags'),
            'multiOptions' => array(
                ''          => '--- please select ---',
                '5000'      => html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8'). '5000',
                '7500'      => html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8'). '7500',
                '10000'     => html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8'). '10000',
                '15000'     => html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8'). '15000',
                '15000+'    => html_entity_decode('&pound;', ENT_COMPAT, 'UTF-8'). '15000+',
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select the amount of cover required',
                            'notEmptyInvalid' => 'Please select the amount of cover required'
                        )
                    )
                )
            )
        ));
        

        // Add amount of cover element (text) element, for when value of contents_cover_a is "15000+"
        $this->addElement('text', 'contents_cover_b', array(
            'label'      => 'Please enter amount of cover required',
            'required'   => false,
            'attribs' 	=> array(
                'class'=>'currency'
            ),
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the amount of cover required',
                            'notEmptyInvalid' => 'Please enter the amount of cover required'
                        )
                    )
                ),
                array(
                    'GreaterThan', true, array(
                        'min' => 15000,
                        'messages' => 'Amount of cover must be above &pound;15,000'
                    )
                )
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/contents-insurance.phtml'))
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        // Grab view and add the contents JavaScript into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headScript()->appendFile(
            '/assets/tenants-insurance-quote-b/js/contents.js',
            'text/javascript'
        );
    }
    
    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     *
     * @return bool
     */
    public function isValid($formData = array()) {
        
        $session = new Zend_Session_Namespace('homelet_global');
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        
        // Check if contents_cover_a is "15000+", if so make contents_cover_b validation mandatory
        if (isset($formData['contents_cover_a']) && $formData['contents_cover_a'] == '15000+') {
            $this->getElement('contents_cover_b')->setRequired(true);
        }
        
        // Call original isValid()
        return parent::isValid($formData);
    }

}