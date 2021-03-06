<?php

class Connect_Form_LandlordsInsuranceSalesReport extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        
        // Add policy start date element
        $this->addElement('text', 'landlordsinsurancesalesreport_start', array(
            'label'     => 'Report start date',
            'required'  => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a report start date',
                        )
                    )
                )
            ),
            'decorators' => array
            (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            )
        ));
        
        // Add policy end date element - used only for display purposes
        $this->addElement('text', 'landlordsinsurancesalesreport_end', array(
            'label'     => 'Report end date',
            'required'  => false,
            'filters'    => array('StringTrim'),
            'decorators' => array
            (
                array('ViewHelper', array('escape' => false)),
                array('Label', array('escape' => false))
            )
        ));
        
        $this->addElement('submit', 'landlordsinsurancesalesreport_producereport', array('label' => 'Produce Report'));
        $this->addElement('submit', 'landlordsinsurancesalesreport_exporttoexcel', array('label' => 'Export to Excel'));
        
        
        
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/landlordsinsurancesalesreport.phtml'))
        ));
        
        $this->setElementFilters(array('StripTags'));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        // Don't show labels for the submit buttons
        $this->getElement('landlordsinsurancesalesreport_producereport')->setDecorators(array(
            array('ViewHelper', array('escape' => false))
        ));
        
        $this->getElement('landlordsinsurancesalesreport_exporttoexcel')->setDecorators(array(
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
        );
    }
}