<?php
/**
* TODO: Document this
* @param
* @return
* @author John Burrin
* @since
*/
class Manager_Insurance_Portfolio_BankInterest {
	/**
	* TODO: Document this
	* @param
	* @return
	* @author John Burrin
	* @since
	*/
    public function save($data){
        $dataSource = new Datasource_Insurance_Portfolio_BankInterest();
        return ($dataSource->save($data));
    }
	
	/**
	* TODO: Document this
	* @param
	* @return
	* @author John Burrin
	* @since
	*/
	public function fetchAllInterests($refNo){
        $dataSource = new Datasource_Insurance_Portfolio_BankInterest();
        return $dataSource->fetchAllInterestsByrefNo($refNo);
    }
	
	public function removeInterest($id){
        $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
		$refNo = $pageSession->CustomerRefNo;
        $dataSource = new Datasource_Insurance_Portfolio_BankInterest();
        return $dataSource->deleteWithRefno($refNo,$id);
    }
}

?>