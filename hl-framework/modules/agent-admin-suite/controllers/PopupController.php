<?php
class AgentAdminSuite_PopupController extends Zend_Controller_Action {
	
	/**
	 * Popup box for agent search
	 */
	public function agentSearchAction() {
        $this->_helper->layout->disableLayout();
        if ($this->getRequest()->isPost()) {
        	// Perform search and show results
        	
        	$postData = $this->getRequest()->getPost();
        	
        	$agentSearchResults = array();
        	
        	$agents = new Manager_Core_Agent();
        	
        	if (isset($postData['agentnumber'])) {
        		// We have an agent scheme number so this should be easy
        		try {
        			$agent = $agents->getAgent($postData['agentnumber']);
        		} catch (Exception $e) {
        			// TODO: Handle - Failed to find a match
        			
        		}
        		$agentSearchResults[] = array(
        			'schemeNumber'	=>	$agent->getSchemeNumber(),
        			'hNumber'		=>  $agent->getHNumber(),
        			'name'			=>  $agent->getName()
    			);
        	}
        	
        	$this->view->agents = $agentSearchResults;
        	/*array(
	    		array('schemeNumber'	=>	 '1501062', 'hNumber'	=>	'7292',	'name'	=>	'Ash Property Management Ltd'),
	    		array('schemeNumber'	=>	 '1403587', 'hNumber'	=>	'5437',	'name'	=>	'Belvoir Property Management'),
	    		array('schemeNumber'	=>	 '1403454', 'hNumber'	=>	'5128',	'name'	=>	'Chance Option Developments Ltd'),
	    		array('schemeNumber'	=>	 '1322742', 'hNumber'	=>	'5101',	'name'	=>	'Hodgson Elkington LLP')
    		);*/
    		
    		$this->render('agent-search-results');
        } else {
        	// Show the search form
	        $searchForm = new AgentAdminSuite_Form_AgentSearch();
	 		
	        if ($this->getRequest()->isPost()) {
	            $searchForm->isValid($_POST);
	        }
	        
	        $this->view->form = $searchForm;
	    }
	}
}
?>