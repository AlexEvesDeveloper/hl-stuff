<?php

use Iris\Authentication\AgentAuthorizationToken;

class Connect_View_Helper_IrisApiCredentials extends Zend_View_Helper_Abstract
{

    public function irisApiCredentials() {

        $this->_auth = Zend_Auth::getInstance();
        $this->_auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));

        $this->_hasAuth = $this->_auth->hasIdentity();

        if (
            ! $this->_hasAuth ||
            ! $this->_auth->getStorage()->read()->isInIris
        ) {
            return;
        }

        $agentAuthorizationToken = new AgentAuthorizationToken();

        return $this->view->partial(
            'partials/iris-api-credentials.phtml',
            array(
                'agentRealName' => $this->_auth->getStorage()->read()->realname,
                'agentKey' => $agentAuthorizationToken->getConsumerKey(),
                'agentSecret' => $agentAuthorizationToken->getConsumerSecret(),
                'agentBranch' => $agentAuthorizationToken->getAgentBranchUuid()
            )
        );
    }
}