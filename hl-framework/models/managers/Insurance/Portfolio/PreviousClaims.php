<?php

/**
 * Business rules class which provides underwriting previous claims services.
 *
 * I did not want to put this here but I need to keep this apart from the new managers for previous claims
 */
class Manager_Insurance_Portfolio_PreviousClaims {
	/**
	* TODO: Document this
	* @param
	* @return
	* @author John Burrin
	* @since
	*/
    public function save($data){
        $dataSource = new Datasource_Insurance_Portfolio_PreviousClaims();
        return ($dataSource->save($data));
    }

	/**
	* TODO: Document this
	* @param
	* @return
	* @author John Burrin
	* @since
	*/
	public function fetchAllClaims($refNo){
        $dataSource = new Datasource_Insurance_Portfolio_PreviousClaims();
        return $dataSource->fetchWithClaimTypes($refNo);
    }

    public function removeClaim($id){
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
		$refNo = $pageSession->CustomerRefNo;
        $dataSource = new Datasource_Insurance_Portfolio_PreviousClaims();
        return $dataSource->deleteWithRefno($refNo,$id);
    }

    /**
    * TODO: Document this
    * @param
    * @return
    * @author John Burrin
    * @since
    */

    public function getClaimsTotal($refNo){
        $dataSource = new Datasource_Insurance_Portfolio_PreviousClaims();
        return $dataSource->getClaimsTotal($refNo);
    }

    public function fetchWithClaimTypes($refNo){
        $dataSource = new Datasource_Insurance_Portfolio_PreviousClaims();
        return $dataSource->fetchWithClaimTypes($refNo);
    }
}