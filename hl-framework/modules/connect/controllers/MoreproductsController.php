<?php

require_once('ConnectAbstractController.php');
class Connect_MoreproductsController extends ConnectAbstractController {

    public function init() {

        // Include the premier CSS
        $this->view->headLink()->appendStylesheet('/assets/connect/css/premier.css');

        parent::init();
    }

    /*public function benefitsAction() {
        switch($this->_agentObj->premierStatus) {

            case Model_Core_Agent_PremierStatus::STANDARD:
                $this->_helper->viewRenderer('benefits-standard');
                break;

            case Model_Core_Agent_PremierStatus::PREMIER:
                $this->_helper->viewRenderer('benefits-premier');
                break;

            case Model_Core_Agent_PremierStatus::PREMIER_PLUS:
                $this->_helper->viewRenderer('benefits-premierplus');
                break;

            default:
                $this->_helper->viewRenderer('benefits-standard');
                break;
        }
    }

    public function advantageAction() {
        switch($this->_agentObj->premierStatus) 
	{
	    default:
            case Model_Core_Agent_PremierStatus::STANDARD:
		$this->view->product = 'standard';
                break;

            case Model_Core_Agent_PremierStatus::PREMIER:
		$this->view->product = 'premier';
                break;

            case Model_Core_Agent_PremierStatus::PREMIER_PLUS:
		$this->view->product = 'premplus';
                break;
        }
    }

    public function businessCareAction() {
        switch($this->_agentObj->premierStatus) 
	{
	    default:
            case Model_Core_Agent_PremierStatus::STANDARD:
		$this->view->product = 'standard';
                break;

            case Model_Core_Agent_PremierStatus::PREMIER:
		$this->view->product = 'premier';
                break;

            case Model_Core_Agent_PremierStatus::PREMIER_PLUS:
		$this->view->product = 'premplus';
                break;
        }
    }

    public function vizzihomeAction() {
        switch($this->_agentObj->premierStatus) 
	{
	    default:
            case Model_Core_Agent_PremierStatus::STANDARD:
		$this->view->product = 'standard';
                break;

            case Model_Core_Agent_PremierStatus::PREMIER:
		$this->view->product = 'premier';
                break;

            case Model_Core_Agent_PremierStatus::PREMIER_PLUS:
		$this->view->product = 'premplus';
                break;
        }
    }*/
}
