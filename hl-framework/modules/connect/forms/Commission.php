<?php

class Connect_Form_Commission extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        
        // Add report month element
        $startmonthselect = $this->addElement('select', 'commission_startmonth', array(
            'label'     => 'Start month',
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
        ))->getElement('commission_startmonth');
        
        $c = 1;
        foreach (array('January',' February', 'March', 'April', 'May', 'June', 'July', 'August',
                       'September', 'October', 'November', 'December') as $month)
        {
            $startmonthselect->addMultiOption(sprintf('%02d', $c), $month);
            $c++;
        }
        
        // Add report month element
        $endmonthselect = $this->addElement('select', 'commission_endmonth', array(
            'label'     => 'Start month',
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
        ))->getElement('commission_endmonth');
        
        $c = 1;
        foreach (array('January',' February', 'March', 'April', 'May', 'June', 'July', 'August',
                       'September', 'October', 'November', 'December') as $month)
        {
            $endmonthselect->addMultiOption(sprintf('%02d', $c), $month);
            $c++;
        }
        
        
        // Add report year element
        $startyearselect = $this->addElement('select', 'commission_startyear', array(
            'label'     => 'Start year',
            'required'  => true,
            'decorators' => array
            (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            )
        ))->getElement('commission_startyear');
        
        $endyear = date("Y");
        $startyear = $endyear - 2;
        
        for ($year = $endyear; $year >= $startyear; $year--)
            $startyearselect->addMultiOption($year, $year);
        
        $endyearselect = clone($startyearselect);
        $endyearselect->setName('commission_endyear');
        $endyearselect->setLabel('End year');
        $this->addElement($endyearselect);
        
        $this->addElement('submit', 'commission_producereport', array('label' => 'View Commission'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/commission.phtml'))
        ));
        
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        // Don't show labels for the submit buttons
        $this->getElement('commission_producereport')->setDecorators(array(
            array('ViewHelper', array('escape' => false))
        ));
    }
}