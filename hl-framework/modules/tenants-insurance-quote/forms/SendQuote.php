<?php

class TenantsInsuranceQuote_Form_SendQuote extends Zend_Form
{
    /**
     * Create Sendquote subform
     *
     * @return void
     */
    public function init()
    {
        $this->addElement('radio', 'how_send', array(
            'label'     => 'How would you like your quote sending?',
            'required'  => true,
            'multiOptions' => array(
                'email' => 'Email',
                'post' => 'Post',
                'both' => 'Email & Post'
            ),
            'separator' => '',
            'label_placement' => 'append',
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Please select a method to receive'
                        )
                    )
                )
            )
        ));
        
        $this->addElement('submit', 'send_quote', array(
            'label' => 'Send',
            'onclick' => 'sendQuote(); return false;',
            'attribs' => array(
                'class' => 'btn btn-tertiary',
            )
        ));
        
        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
        
        $this->send_quote->setDecorators(array(
            'ViewHelper'
        ));
    }
}