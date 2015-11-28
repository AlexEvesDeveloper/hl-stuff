<?php
/**
 * A collection of wrapper functions to interact with the Policy
 * datasource. 
 *
 */
class Manager_Core_Policy
{
    /**
     * Wrapper function to get the policy identifier from the given policy
     * type and name
     * 
     * @param str $type Policy type
     * @param str $name Policy name
     * @return object Model_Core_Policy_Options
     */
    public function fetchPolicyOptionIdByName($type, $name)
    {
        $source = new Datasource_Insurance_Policy_Options($type);
        $optionsObj = $source->fetchOptionsByName($name);
        return $optionsObj->getPolicyOptionId();
    }
    
    /**
     * Gets policy minimum insured from the given policy type and name
     * 
     * @param str $type Policy type
     * @param str $name Policy Name
     */
    public function fetchPolicyOptionMinimumSumInsuredByName($type, $name)
    {
        $source = new Datasource_Insurance_Policy_Options($type);
        $optionsObj = $source->fetchOptionsByName($name);
        return $optionsObj->getMinimumSumInsured();
    }
}