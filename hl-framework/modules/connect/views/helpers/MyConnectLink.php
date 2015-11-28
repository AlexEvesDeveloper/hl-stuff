<?php

class Connect_View_Helper_MyConnectLink extends Zend_View_Helper_Abstract
{

    public function myConnectLink($asn, $agentUsername, $agentUserId) {

        // Get parameters
        $params = Zend_Registry::get('params');

        // Get IP address
        $ipAddress = $this->_getMyConnectIp();

        // Put data into array
        $data = array($ipAddress, $asn, $agentUsername);

        // Generate MyConnect token - does NOT use proper HMAC :-/
        $token = time() . '||' . $this->_getMyConnectIp() . "||{$asn}||{$agentUsername}||" . rand(100000, 9999999);
		$hash = substr(md5($token . $params->connect->myConnect->securityString), 0, 8);
		$token = base64_encode($token . '||' . $hash);
        $link['url'] = 'http://' . $params->connect->myConnect->host . '/Login.php?Loginbtn=Login&token=' . $token;

        return $this->view->partial('partials/myconnect.phtml', $link);
    }

    private function _getMyConnectIp() {

        // Determine real IP address of end user irrespective of proxy forwarding etc
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        }
        elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        }
        elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}