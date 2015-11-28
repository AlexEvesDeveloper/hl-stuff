<?php
    class Manager_Insurance_Portfolio_AdditionalInformation {

        /**
        * TODO: Document this
        * @param
        * @return
        * @author John Burrin
        * @since
        */
        public function save($data){
            $property = new Datasource_Insurance_Portfolio_AdditionalInformation();
            return ($property->save($data));
        }

        /**
        * TODO: Document this
        * @param
        * @return
        * @author John Burrin
        * @since
        */

        public function fetchAllByRefNo($refNo,$qid){
            $property = new Datasource_Insurance_Portfolio_AdditionalInformation();
            return ($property->fetchAllAdditionalByrefNo($refNo,$qid));
        }

        public function removeAdditional($id){
           $pageSession = new Zend_Session_Namespace('portfolio_insurance_quote');
           $refNo = $pageSession->CustomerRefNo;
           $dataSource = new Datasource_Insurance_Portfolio_AdditionalInformation();
           return $dataSource->deleteWithRefno($refNo,$id);
       }

       /**
        *
        *
        *@return bool
        *
        **/
       public function hasAdditions($refNo, $quid){
            $property = new Datasource_Insurance_Portfolio_AdditionalInformation();
            return ($property->hasAdditions($refNo, $quid));
       }


    }
?>
