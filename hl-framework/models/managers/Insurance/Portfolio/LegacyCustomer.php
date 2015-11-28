<?php
    /**
    * TODO: Document this
    * @param
    * @return
    * @author John Burrin
    * @since
    */
    class Manager_Insurance_Portfolio_LegacyCustomer {
        
        /**
        * TODO: Document this
        * @param
        * @return
        * @author John Burrin
        * @since
        */
        public function save($data){
            $property = new Datasource_Insurance_Portfolio_LegacyCustomer();
            return ($property->save($data));
        }
        
        /**
        * TODO: Document this
        * @param
        * @return
        * @author John Burrin
        * @since
        */
        
        public function fetchByRefNo($refNo){
            $property = new Datasource_Insurance_Portfolio_LegacyCustomer();
            return ($property->fetchByRefNo($refNo));
        }
    }
?>