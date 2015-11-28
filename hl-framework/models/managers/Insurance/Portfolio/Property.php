<?php
    /**
    * Manager class responsible for Portfolio Property logic
    */
    class Manager_Insurance_Portfolio_Property {
	
	/**
	 * Ensures a space exists in the postcode, and that it is comprised
	 * of upper case letters only.
	 */
	public function formatPostcode($postCode) {
        
		$formattedPostCode = trim(strip_tags($postCode));
		if(substr($formattedPostCode, -3) != " ") {
		
			function stringrpl($x,$r,$str) {
			
				$out = "";
				$temp = substr($str,$x);
				$out = substr_replace($str,"$r",$x);
				$out .= $temp;
				return $out;
			}
		
			$formattedPostCode = stringrpl(-3," ", $formattedPostCode);
		}
		return strtoupper($formattedPostCode);
	}

    /**
    * Public function to save
    *
    * @param Model_Insurance_Portfolio_Property $data The Data to be saved
    * return int The last insert id
    */
    public function save($data){
    #	Zend_Debug::dump($data);die();
        $property = new Datasource_Insurance_Portfolio_Property();
        $dsBuildingsRiskArea = new Datasource_Insurance_RiskAreas_Buildings();
        $dsContentsRiskArea = new Datasource_Insurance_RiskAreas_LandlordsContents();
        $data->buildingsRiskArea = $dsBuildingsRiskArea->getCurrentRate($data->postcode);
        $data->contentsRiskArea = $dsContentsRiskArea->getCurrentRate($data->postcode);
        return ($property->save($data));
    }

    /**
    * gets a Property from the portfolio quote table by its auto id
    * @param int $id The id of the record to be retrieved
    * @return Model_Insurance_Portfolio_Portfolio The row as a Model_Insurance_Portfolio_Portfolio object
    *
    */
    public function getPropertyById($id){
        $propertyDataSource = new Datasource_Insurance_Portfolio_Property();
        return $propertyDataSource->getRowById($id);
    }

    /**
    * Delete a qiven Property by its ID
    */
    public function deleteById($id){
        $quoteDataSource = new Datasource_Insurance_Portfolio_Property();
        $quoteDataSource->deleteById($id);
    }

    /**
    * fetchAllProperties, fetches all the properties in a portfolio by there refno number
    * @param String $refNo  Customer reference number of the properties
    * return array An array of object of type Model_Insurance_Portfolio_Property
    *
    */
    public function fetchAllProperties($refNo){
        $quoteDataSource = new Datasource_Insurance_Portfolio_Property();
        return $quoteDataSource->fetchPropertiesByrefNo($refNo);
    }

    /**
    * TODO: Document this
    * @param string refNo Referance numbers to be deleted
    * @return void
    * @author John Burrin
    * @since 1.3
    */
    public function deleteByRefNo($refNo){
        $quoteDataSource = new Datasource_Insurance_Portfolio_Portfolio();
        $quoteDataSource->deleteByRefNo($refNo);

    }

    /**
    * TODO: Document this
    * @param Model_Insurance_Portfolio_Portfolio $data
    * @return
    * @author John Burrin
    * @since 1.3
    */

    public function update($data){
        $quoteDataSource = new Datasource_Insurance_Portfolio_Portfolio();
        $quoteDataSource->save($data);
    }
}

?>
