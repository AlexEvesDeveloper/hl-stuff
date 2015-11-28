<?php

/**
* Class definition for the form elements in the subform Claims
*/
class LandlordsInsuranceQuote_Form_Subforms_ClaimsDialog extends Zend_Form_SubForm
{
    const PLEASE_SELECT = '--- please select ---';

    public function init()
    {
        //Claim type element.
        $this->addElement('select', 'claim_type', array(
            'label' => 'Type of claim:',
            'required' => true,
            'multiOptions' => array('' => self::PLEASE_SELECT),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your claim type',
                            'notEmptyInvalid' => 'Please select your claim type'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        $claimsManager = new Manager_Insurance_PreviousClaims();
        $claimTypeObjects = $claimsManager->getPreviousClaimTypes(Model_Insurance_ProductNames::LANDLORDSPLUS);

        $claimTypeList = array();
        $claimTypeList[''] =  self::PLEASE_SELECT;
        foreach ($claimTypeObjects as $claimType) {
            $id = $claimType->getClaimTypeID();
            $text = $claimType->getClaimTypeText();
            $claimTypeList[$id] = $text;
        }

        //Add the claim types.
        $claimTypesSelect = $this->getElement('claim_type');
        $claimTypesSelect->setMultiOptions($claimTypeList);

        //Claim month element.
        $this->addElement('select', 'claim_month', array(
            'label' => 'Month of claim:',
            'required' => true,
            'multiOptions' => array(
                '' =>  self::PLEASE_SELECT,
                '01' => 'Jan',
                '02' => 'Feb',
                '03' => 'Mar',
                '04' => 'Apr',
                '05' => 'May',
                '06' => 'Jun',
                '07' => 'Jul',
                '08' => 'Aug',
                '09' => 'Sep',
                '10' => 'Oct',
                '11' => 'Nov',
                '12' => 'Dec'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your claim month',
                            'notEmptyInvalid' => 'Please select your claim month'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));


        $claimYears = array('' =>  self::PLEASE_SELECT);
        $nowYear = date('Y');
        for ($i = $nowYear; $i >= $nowYear - 5; $i--) {
            $claimYears[$i] = $i;
        }
        $this->addElement('select', 'claim_year', array(
            'label' => 'Year of claim:',
            'required' => true,
            'multiOptions' => $claimYears,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select your claim year',
                            'notEmptyInvalid' => 'Please select your claim year'
                        )
                    )
                )
            ),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        $this->addElement('text', 'claim_value', array(
            'label' => 'Value of claim:',
            'required' => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the claim value',
                            'notEmptyInvalid' => 'Please enter the claim value'
                        )
                    )
                ),
                array(
                    'GreaterThan', true, array(
                        'min' => 0,
                        'messages' => 'Claim value must be above zero'
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'currency',
                'class' => 'currency form-control',
            )
        ));

        // Add a filter suitable for currency input - this strips anything non-digit and non-decimal point such as pound
        //   symbols and commas
        $claimValue = $this->getElement('claim_value');
        $claimValue->addFilter('callback', function($v) {
            return preg_replace('/[^\d\.]/', '', $v);
        });

        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/claims-dialog.phtml'))
        ));

        // Clear the default dt and dd element decorators
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
    public function isValid($formData = array())
    {
        // Filter for currency elements
        $currencyFilterElements = array(
            'claim_value'
        );

        foreach ($currencyFilterElements as $filterElement) {
            if (isset($formData[$filterElement])) {
                $formData[$filterElement] = preg_replace(
                    array('/[^\d\.]/'),
                    array(''),
                    $formData[$filterElement]
                );
            }
        }

        // Call original isValid()
        return parent::isValid($formData);
    }
}