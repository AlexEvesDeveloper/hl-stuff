<?php

class Connect_Form_ReferencingServiceLevelReport extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        
        // Add report month element
        $monthselect = $this->addElement('select', 'slareport_month', array(
            'label'     => 'Month',
            'required'  => true,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a report month',
                        )
                    )
                )
            ),
            'decorators' => array
            (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            )
        ))->getElement('slareport_month');
        
        $c = 1;
        foreach (array('January',' February', 'March', 'April', 'May', 'June', 'July', 'August',
                       'September', 'October', 'November', 'December') as $month)
        {
            $monthselect->addMultiOption(sprintf('%02d', $c), $month);
            $c++;
        }
        
        
        
        // Add report year element
        $yearselect = $this->addElement('select', 'slareport_year', array(
            'label'     => 'Year',
            'required'  => true,
            'decorators' => array
            (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            )
        ))->getElement('slareport_year');
        
        $endyear = date("Y");
        $startyear = $endyear - 2;
        
        for ($year = $endyear; $year >= $startyear; $year--)
            $yearselect->addMultiOption($year, $year);
        
        $this->addElement('submit', 'slareport_producereport', array('label' => 'Produce Report'));
        $this->addElement('submit', 'slareport_exporttoexcel', array('label' => 'Export to Excel'));
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/referencingservicelevelreport.phtml'))
        ));
        
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        // Don't show labels for the submit buttons
        $this->getElement('slareport_producereport')->setDecorators(array(
            array('ViewHelper', array('escape' => false))
        ));
        
        $this->getElement('slareport_exporttoexcel')->setDecorators(array(
            array('ViewHelper', array('escape' => false))
        ));
        
        // Grab view and add the date picker JavaScript files into the page head
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view'); 
        $view->headLink()->appendStylesheet(
            '/assets/vendor/jquery-datepicker/css/datePicker.css',
            'screen'
        );
        $view->headScript()->appendFile(
            '/assets/vendor/jquery-date/js/date.js',
            'text/javascript'
        )->appendFile(
            '/assets/vendor/jquery-datepicker/js/jquery.datePicker.js',
            'text/javascript'
        );
    }
}