<?php
/**
* Manager class responsible for Portfolio Property logic
*/ 
    class Manager_Insurance_Portfolio_LegacyProperty {
    
    /**
    * Public function to save
    *
    * @param Model_Insurance_Portfolio_Property $data The Data to be saved
    * return int The last insert id
    */
    public function save($data){
        $property = new Datasource_Insurance_Portfolio_LegacyProperty();
        return ($property->save($data));
    }
  
}

?>