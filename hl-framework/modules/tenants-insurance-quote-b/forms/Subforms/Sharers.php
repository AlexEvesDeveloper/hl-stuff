<?php

class TenantsInsuranceQuoteB_Form_Subforms_Sharers extends Zend_Form_SubForm
{
    public $sharersAllowed = 0;

    public $maxSharers = 2;

    /**
     * Create sharers subform
     *
     * @return void
     */
    public function init()
    {
        // Invoke the sharers manager
        $sharersManager = new Manager_Insurance_TenantsContentsPlus_Sharers();

        // Create array of possible sharer occupations
        $sharerOccupations = array('' => '--- please select ---');
        $sharerOccupationsObj = $sharersManager->getOccupations();
        foreach($sharerOccupationsObj as $sharerOccupationObj) {
            $sharerOccupations[$sharerOccupationObj->getType()] = $sharerOccupationObj->getType();
        }

        // Add number of sharers element
        $multiOptions = array(
            '' => '--- please select ---'
        );
        for ($i = 0; $i <= $this->maxSharers; $i++) {
            $multiOptions["{$i}"] = "{$i}";
        }
        $this->addElement('select', 'policy_sharers', array(
            'label'     => 'How many sharers would you like to include in this policy?',
            'required'  => false,
            'multiOptions' => $multiOptions,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a number of sharers',
                            'notEmptyInvalid' => 'Please select a number of sharers'
                        )
                    )
                )
            )
        ));

        // Add sharer 1 upward's occupation - this is only required for validation if the above element value says is equal to or above $i
        if ($this->maxSharers > 0) {
            for ($i = 1; $i <= $this->maxSharers; $i++) {
            $this->addElement('select', "policy_sharer{$i}_occupation", array(
                'label'     => "Sharer {$i} occupation",
                'required'  => false,
                'multiOptions' => $sharerOccupations,
                'validators' => array(
                    array(
                        'NotEmpty', true, array(
                            'messages' => array(
                                'isEmpty' => "Please select an occupation for sharer {$i}",
                                'notEmptyInvalid' => "Please select an occupation for sharer {$i}"
                            )
                        )
                    )
                )
            ));
            }
        }

        // Set custom subform decorator
         $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/sharers.phtml'))
        ));
        
        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));
        
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headScript()->appendFile(
            '/assets/tenants-insurance-quote-b/js/sharers.js',
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

        // Check that contents SI is above certain values to allow for sharers
        $session = new Zend_Session_Namespace('homelet_global');
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');

        // Invoke the sharers manager
        $sharersManager = new Manager_Insurance_TenantsContentsPlus_Sharers();

        // Note what the contents SI is from the freshly POSTed data, or from the DB model if there
        $contentsSI = $contents_cover_a = $contents_cover_b = 0;
        if (isset($formData['contents_cover_a'])) {
            $contents_cover_a = $formData['contents_cover_a'];
            $contents_cover_b = $formData['contents_cover_b'];
        
            if ($contents_cover_a == '15000+') {
                if ($contents_cover_b != '' && (int)$contents_cover_b > 15000) {
                    $contentsSI = (int)$contents_cover_b;
                }
            } else {
                $contentsSI = (int)$contents_cover_a;
            }
        }
        
        // Decide max number of sharers allowed based on contents insured value
        $contentsAmount = new Zend_Currency(
            array(
                'value' => $contentsSI,
                'precision' => 0
            )
        );
        $sharersAllowed = $sharersManager->getNoOfSharersAllowed($contentsAmount);
        
        // Force user to select a number of sharers if number allowed > 0
        if ($sharersAllowed > 0) {
            $this->getElement('policy_sharers')->setRequired(true);
        }
        
        // Check if sharers selected are above 0, if so make their occupation validation mandatory
        $sharerCount = min($sharersAllowed, (isset($formData['policy_sharers']) ? $formData['policy_sharers'] : 0));
        if ($sharerCount > 0) {
            for ($i = 1; $i <= $sharerCount; $i++) {
                $this->getElement("policy_sharer{$i}_occupation")->setRequired(true);
            }
        }
        
        // Call original isValid()
        return parent::isValid($formData);
    }
}