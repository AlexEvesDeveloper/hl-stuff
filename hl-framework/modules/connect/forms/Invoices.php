<?php

class Connect_Form_Invoices extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        
        // Add report month element
        $monthselect = $this->addElement('select', 'invoice_month', array(
            'label'     => 'Month',
            'required'  => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select an invoice month',
                        )
                    )
                )
            ),
            'decorators' => array
            (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            )
        ))->getElement('invoice_month');
        
        $c = 1;
        foreach (array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
                       'September', 'October', 'November', 'December') as $month)
        {
            $monthselect->addMultiOption(sprintf('%02d', $c), $month);
            $c++;
        }
        
        // Add report year element
        $yearselect = $this->addElement('select', 'invoice_year', array(
            'label'     => 'Year',
            'required'  => true,
            'decorators' => array
            (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            )
        ))->getElement('invoice_year');
        
        $endyear = date("Y");
        $startyear = $endyear - 2;
        
        for ($year = $endyear; $year >= $startyear; $year--)
            $yearselect->addMultiOption($year, $year);
        
        $this->addElement('submit', 'invoice_producereport', array('label' => 'View Invoice'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/invoices.phtml'))
        ));
        
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        // Don't show labels for the submit buttons
        $this->getElement('invoice_producereport')->setDecorators(array(
            array('ViewHelper', array('escape' => false))
        ));

    }
}