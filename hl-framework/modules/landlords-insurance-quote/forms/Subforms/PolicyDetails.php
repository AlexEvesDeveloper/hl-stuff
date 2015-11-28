<?php

class LandlordsInsuranceQuote_Form_Subforms_PolicyDetails extends Zend_Form_SubForm
{
    /**
     * Create policy details subform
     *
     * @return void
     */
    public function init()
    {
        // Add policy start date element
        $this->addElement('text', 'policy_start', array(
            'label'     => 'Policy start date',
            'required'  => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a policy start date',
                        )
                    )
                )
            )
        ));
        $policy_start = $this->getElement('policy_start');
        $validator = new Zend_Validate_DateCompare();
        $validator->minimum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $validator->maximum = new Zend_Date(mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 60 * 60 * 24 * 45);
        $validator->setMessages(array(
            'msgMinimum' => 'Policy start date cannot be in the past',
            'msgMaximum' => 'Policy start date cannot be more than 45 days in the future'
        ));
        $policy_start->addValidator($validator, true);

        // Add policy end date element - used only for display purposes
        $this->addElement('text', 'policy_end', array(
            'label'     => 'Policy end date',
            'required'  => false,
            'filters'    => array('StringTrim')
        ));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/policy-details.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));

        // Grab view and add the date picker JavaScript files into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headLink()->appendStylesheet(
            '/assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css',
            'screen'
        );

        $view->headScript()->appendFile(
            '/assets/vendor/jquery-date/js/date.js',
            'text/javascript'
        )->appendFile(
            '/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
            'text/javascript'
        )->appendFile(
            '/assets/landlords-insurance-quote/js/policyStartDatePicker.js',
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

        $pageSession = new Zend_Session_Namespace('landlords_insurance_quote');
        $session = new Zend_Session_Namespace('homelet_global');
        $agentSchemeNumber = $session->referrer;

        // Populate $formData with data from model, if available
        if (isset($pageSession->quoteID)) {
            $customerData = array();

            // Fetch previously stored start date
            $quoteManager = new Manager_Insurance_LandlordsPlus_Quote($pageSession->quoteID); 
            $startDate = $quoteManager->getStartDate();

            if ($startDate != '' && $startDate != '0000-00-00') {
                $customerData['policy_start'] = substr($startDate, 8, 2) . '/' . substr($startDate, 5, 2) . '/' . substr($startDate, 0, 4);
            }

            // Pipe into $formData, with any existing $formData content taking precedence
            $formData = array_merge($customerData, $formData);
        }

        // Call original isValid()
        return parent::isValid($formData);

    }

}
