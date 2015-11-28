<?php
/**
* Class definition for the form elements in the subform Campaign
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_Campaign extends Zend_Form_SubForm
{
   /**
     * 
     *
     * @return void
     */
    public function init()
    {
    	$this->addElement('select', 'is_previous_client', array(
            'label'     => 'Have you used HomeLet before?',
            'required'  => true,
            'multiOptions' => array(
                '' => '--- Please select ---',
                '1' => 'Yes',
                '0' => 'No'
            ),
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Have you used HomeLet before?',
                            'notEmptyInvalid' => 'Have you used HomeLet before?'
                        )
                    )
                )
            )
        ));
        
        // Add campaign code element
        $this->addElement('text', 'campaign_code', array(
            'label'      => 'HomeLet campaign code (if you’ve got one)',
            'required'   => false,
            'validators' => array(
                array(
                    'NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'Do you have a HomeLet campaign code?',
                            'notEmptyInvalid' => 'You have entered an invalid HomeLet campaign code'
                        )
                    )
                )
            )
        ));    	
    	
         // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/campaign.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }
}
?>