<?php

class Connect_View_Helper_ConnectMenu extends Zend_View_Helper_Abstract {

    public function connectMenu() {

        // This could be quite yucky - I'm just trying to work out how to get the menu structure to work
        $url = trim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');

        // If user is viewing 'News' tab, fake it as being on 'Home'
        $url = (strpos($url, '/news/') !== false) ? 'connect' : $url;

        // Check if user is logged in
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('hl_connect'));

        $isInIris = isset($auth->getStorage()->read()->isInIris) ? $auth->getStorage()->read()->isInIris : false;

        $menuData = array(
            'url'           => $url,
            'loggedIn'      => $auth->hasIdentity(),
            'userresources' => $this->view->userresources,
            'fsastatusabbr' => $this->view->fsastatusabbr,
            'isInIris'      => $isInIris,
        );

        $mainMenu =
            $subHeader = '';

        if ($auth->hasIdentity() && $this->view->layout()->getLayout() != 'login') {

            // Extra agent user info for constructing MyConnect link
            $menuData['agentSchemeNumber']  = $auth->getStorage()->read()->agentschemeno;
            $menuData['agentId']            = $auth->getStorage()->read()->agentid;
            $menuData['agentUsername']      = $auth->getStorage()->read()->username;

            $mainMenu = $this->view->partial('partials/main-menu.phtml', $menuData);

        } else {

            $mainMenu = $this->view->partial('partials/main-menu-prelogin.phtml', $menuData);

        }

        $subHeader = $this->view->partial('partials/sub-header.phtml', $menuData);

        echo $mainMenu;
        echo $subHeader;
    }

}