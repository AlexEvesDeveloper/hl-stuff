<?php
class Manager_Insurance_TenantsContentsPlus_Policy_Options {
    
    public function getOptions() {
        $type = 'T'; // We just want Tenants options from the datasource
        $optionsDatasource = new Datasource_Insurance_Policy_Options($type);
        $options = $optionsDatasource->fetchOptions();
        
        $returnArray = array();
        foreach ($options as $option) {
            // Filter for just the options which are relevant to tenants contents PLUS product
            if(substr($option->policyOption, -1) == "p") {
                $returnArray[] =  $option->policyOption;
                //$returnArray[]['minimumSumInsured'] =  $option->minimumSumInsured;
            }
        }
        return $returnArray;
    }
}

?>