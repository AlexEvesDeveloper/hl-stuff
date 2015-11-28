<?php

class Form_PortfolioInsuranceQuote_Step2 extends Zend_Form_Multilevel {
    /**
     * Pull in the sub forms that comprise Portfolio Step 2
     *
     * @return void
     */
    public function init()
    {
         $this->addElement('hidden', 'propNumb', array(
            'label'      => '',
            'required'   => true,
            'filters'    => array('Digits'),
            'validators' => array(
                array(
                    'GreaterThan', true, array(
                        'min' => 1,
                        'messages' => 'A minimum of two properties is required'
                    )
                )
            )
        ));
    }
}
?>