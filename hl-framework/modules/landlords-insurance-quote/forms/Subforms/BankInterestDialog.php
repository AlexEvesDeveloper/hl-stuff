<?php

/**
* Class definition for the form elements in the subform BankInterest
*/
class LandlordsInsuranceQuote_Form_Subforms_BankInterestDialog extends Zend_Form_SubForm
{
    const PLEASE_SELECT = '--- please select ---';

    public function init()
    {
        $this->addElement('text', 'bank_name', array(
            'label'      => 'Bank Name',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the bank name'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'class' => 'form-control',
            )
        ));

        $this->addElement('text', 'account_number', array(
            'label'      => 'Account Number',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the bank account number'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-ctfilter' => 'yes',
                'data-required' => 'required',
                'data-validate' => 'validate',
                'class' => 'form-control',
            )
        ));

        $this->addElement('text', 'address_line1', array(
            'label'      => 'Address Line 1',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the bank address line 1'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'class' => 'form-control',
            )
        ));

        $this->addElement('text', 'address_line2', array(
            'label'      => 'Address Line 2',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'attribs' => array(
                'class' => 'form-control',
            )
        ));

        $this->addElement('text', 'town', array(
            'label'      => 'Town',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the bank town'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'class' => 'form-control',
            )
        ));

        $this->addElement('text', 'postcode', array(
            'label'      => 'Postcode',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please enter the bank postcode'
                        )
                    )
                )
            ),
            'attribs' => array(
                'data-required' => 'required',
                'data-validate' => 'validate',
                'data-type' => 'postcode',
                'class' => 'form-control',
            )
        ));

        // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/bank-interest-dialog.phtml'))
        ));

        // Clear the default dt and dd element decorators
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}