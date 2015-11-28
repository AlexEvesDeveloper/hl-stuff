<?php
/**
* Manager model for Postcodes.
*
*/
class Manager_Core_Postcode {

    /**
     * Fetch an individual address by the passed ID
     *
     * @param id Id of the address
     * @param boolean $includeRiskAreaDetail
     * True if call code requires the details of the risk area associated with the
     * postcode passed in, false otherwise. True is the default, however, systems
     * such as referencing and products other than LI+ may not need this feature,
     * and so can pass in 'false' to bypass it.
     *
     * @return array
     */
    public function getPropertyByID($id, $includeRiskAreaDetail = true) {

        $postcodesDataSource = new Datasource_Core_Postcodes();
        $address = $postcodesDataSource->getPropertyByID($id);

        // Map the address to useful field names
        $returnArray = array(
            'id'            =>  $address->id,
            'houseNumber'   =>  $address->NUM,
            'buildingName'  =>  $address->SBN!=''?$address->SBN . ' ' . $address->BNA:$address->BNA,
            'address1'      =>  $address->address1,
            'address2'      =>  $address->address2,
            'address3'      =>  $address->address3,
            'address4'      =>  $address->address4,
            'address5'      =>  $address->address5,
            'postcode'      =>  $address->postcode,
            'organisation'  =>  $address->ORG,
            'department'    =>  $address->ORD,
            'county'        =>  $address->CTP
        );

        if($includeRiskAreaDetail) {

            $landlordsRiskAreas = new Datasource_Insurance_LandlordsPlus_RiskAreas();
            $returnArray['landlordsRiskAreas'] = $landlordsRiskAreas->getByPostcode($address->postcode);
        }

        return $returnArray;
    }

    /**
     * Fetch a list of addresses by the postcode
     *
     * @param postcode Postcode for the search
     * @param houseNumber Optional house number for the search
     * @return array
     */
    public function getPropertiesByPostcode($postcode, $houseNumber = null)
    {
        // First we need to check that the postcode is valid, but only run this
        // check if the global.ini parameter test.filterPostcode is set to true
        $params = Zend_Registry::get('params');
        if (is_object($params->test) && $params->test->filterPostcode) {
            $postcodeUtils = new Application_Core_Postcode();
            $postcode = $postcodeUtils->validate($postcode);
        }

        if ($postcode != '') {
            $postcodesDataSource = new Datasource_Core_Postcodes();
            $addresses = $postcodesDataSource->getPropertiesByPostcode($postcode, $houseNumber);

            $returnArray = array();
            foreach ($addresses as $address) {
                array_push($returnArray, array(
                    'id'            =>  $address->id,
                    'houseNumber'   =>  $address->NUM,
                    'buildingName'  =>  $address->SBN!=''?$address->SBN . ' ' . $address->BNA:$address->BNA,
                    'address1'      =>  $address->address1,
                    'address2'      =>  $address->address2,
                    'address3'      =>  $address->address3,
                    'address4'      =>  $address->address4,
                    'address5'      =>  $address->address5,
                    'postcode'      =>  $address->postcode,
                    'organisation'  =>  $address->ORG,
                    'department'    =>  $address->ORD,
                    'county'        =>  $address->CTP
                ));
            }
        } else {
            // Not a valid postcode - return an error
            return array(
                'error'         => true,
                'errorMessage'  => 'Not a valid postcode');
        }

        // Before we return the raw data - loop through and build some nice single line addresses for form selection
        // WARNING: This may have duplicates due to dropping departments etc.. do an array cleanup before using!
        foreach ($returnArray as &$address) {
            $singleLine = '';
            if ($address['organisation']) { $singleLine .= ucwords(strtolower($address['organisation'])) . ', '; }
            if ($address['buildingName']) { $singleLine .= ucwords(strtolower($address['buildingName'])) . ', '; }
            if ($address['houseNumber']) { $singleLine .= ucwords(strtolower($address['houseNumber'])) . ', '; }
            if ($address['address1']) { $singleLine .= ucwords(strtolower($address['address1'])) . ', '; }
            if ($address['address2']) { $singleLine .= ucwords(strtolower($address['address2'])) . ', '; }
            if ($address['address3']) { $singleLine .= ucwords(strtolower($address['address3'])) . ', '; }
            if ($address['address4']) { $singleLine .= ucwords(strtolower($address['address4'])) . ', '; }
            if ($address['address5']) { $singleLine .= ucwords(strtolower($address['address5'])) . ', '; }
            if ($address['county']) { $singleLine .= ucwords(strtolower($address['county'])) . ', '; }
            $singleLineWithoutPostcode = $singleLine;
            if ($address['postcode']) { $singleLine .= strtoupper($address['postcode']) . ', '; }

            $singleLine = trim ($singleLine,', ');
            $singleLineWithoutPostcode = trim ($singleLineWithoutPostcode,', ');

            $address['singleLine'] = $singleLine;
            $address['singleLineWithoutPostcode'] = $singleLineWithoutPostcode;
        }

        return $returnArray;
    }
}
?>