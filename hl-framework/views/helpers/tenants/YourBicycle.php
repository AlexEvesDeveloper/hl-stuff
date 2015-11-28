<?php

class Cms_View_Helper_YourBicycle extends Zend_View_Helper_Abstract {

    /**
     * Helper function for generating bike summary HTML fragment
     *
     * @return string
     */
    public function yourBicycle() {
        $pageSession = new Zend_Session_Namespace('tenants_insurance_quote');

        $bike = new Datasource_Insurance_Policy_Cycles($pageSession->CustomerRefNo, $pageSession->PolicyNumber);
        $bicycleData = $bike->listBikes();
        
        if (count($bicycleData)>0) {
            return $this->view->partial('partials/ajax-bicycle-list.phtml', array('bicycleData' => $bicycleData));
        } else {
            return;
        }
    }

}