<?php
/**
 * Action Helper for populating flash messaging
 *
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Connect_Controller_Action_Helper_Flashmessages extends Zend_Controller_Action_Helper_Abstract {

    /**
     * Creates and adds messages to a flash messenger helper object
     *
     * @param string $message Message to flash
     * @return void
     */
    public function addMessage($message) {

        $flashmessenger = $this->getActionController()->getHelper('FlashMessenger');

        if (is_string($message)) {

            $flashmessenger->addMessage($message);

        } elseif (is_array($message)) {

            foreach($message as $textLine) {
                $this->addMessage($textLine);
            }

        }
    }

    public function getCurrentMessages() {

        $flashmessenger = $this->getActionController()->getHelper('FlashMessenger');

        if (isset($flashmessenger)) {

            return $flashmessenger->getCurrentMessages();
        }
    }
}