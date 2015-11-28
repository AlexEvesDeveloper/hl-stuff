<?php

class LettingAgents_Form_Signup extends Zend_Form {
   /**
     * 
     *
     * @return void
     */
    public function init()
    {
         // Strip all tags to prevent XSS errors
        $this->setElementFilters(array('StripTags'));

        // Set custom subform decorator
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'signup.phtml'))
        ));

        $this->setElementDecorators(array(
            array('ViewHelper', array('escape' => false)),
            array('Label', array('escape' => false))
        ));
    }

    /**
     * Returns errors flattened into a 2d array
     *
     * @return array
     */
    public function getMessagesFlattened() {
		return $this->getMessages();
    }
}
?>