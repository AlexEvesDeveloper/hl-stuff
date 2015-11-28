<?php
    class Manager_Insurance_Portfolio_UnderwritingAnswers {
        
        /**
        * Save the underwrighting answer
        * @param array of data to save
        * @return bool
        * @author John Burrin
        * @since
        */
        public function save($data){
            $property = new Datasource_Insurance_Portfolio_UnderwritingAnswers();
            return ($property->save($data));
        }
        
        /**
        * Fetch unwriting anwsers by refno
        * @param string $refNo reference number of property
        * @return bool
        * @author John Burrin
        * @since
        */   
        public function fetchByRefNo($refNo){
            $property = new Datasource_Insurance_Portfolio_UnderwritingAnswers();
            return ($property->fetchByRefNo($refNo));
        }
        
    }
?>