<?php

class TenantsInsuranceQuoteB_Form_Subforms_HowHear extends Zend_Form_SubForm
{
    /**
     * Create how hear subform
     *
     * @return void
     */
    public function init()
    {
        // Add campaign code element
        $this->addElement('text', 'campaign_code', array(
            'label'      => 'Campaign code',
            'required'   => false,
            'filters'    => array('StringTrim')
        ));
        
        // Add where did you hear about us element
        $this->addElement('select', 'how_hear', array(
            'label'     => 'Where did you hear about us?',
            'required'  => false,
            'filters'   => array('StringTrim'),
            'multiOptions' => array(
                '' => '--- please select ---',
                'Letting Agent' => 'Letting Agent',
                'Personal Recommendation' => 'Friend',
                'SMS' => 'SMS',
                'Email' => 'Email',
                'Letter' => 'Letter',
                'Internet' => 'Internet',
                'Publication' => 'Publication',
                'Other' => 'Other'
            )
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/how-hear.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
   }
   
    /**
     * Overridden isValid() method for pre-validation code
     *
     * @param array $formData data typically from a POST or GET request
     *
     * @return bool
     */
    public function isValid($formData = array()) {
        
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');
        
        // Populate $formData with data from model, if available
        if (isset($pageSession->CustomerRefNo)) {
            $marketQuestionDS = new Datasource_Core_ManagementInformation_MarketingAnswers();
            $customerData['how_hear'] = $marketQuestionDS->getAnswer($pageSession->PolicyNumber);
            
            // Pipe into $formData, with any existing $formData content taking precedence
            $formData = array_merge($customerData, $formData);
        }

        // Call original isValid()
        return parent::isValid($formData);
        
    }
   
}