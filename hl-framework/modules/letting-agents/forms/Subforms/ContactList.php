<?php
/**
* Class definition for the form elements in the subform Campaign
* @author John Burrin
* @since 1.5
*/
class LettingAgents_Form_Subforms_ContactList extends Zend_Form_SubForm
{
   /**
     * 
     *
     * @return void
     */
    public function init()
    {
        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'subforms/contact-list.phtml'))
        ));
    }
}
?>