<?php
    /**
    * Manager class responsible for Portfolio Quote logic
    * @author John Burrin
    * @since 1.3 (arcane coyote)                
    */
    class Manager_Insurance_Portfolio_LegacyPortfolio {
        
    /**
    * Public function to retrieve by policynumber
    * @param string $policyNumber The Policy number of the policy
    *
    * @return Model_Insurance_Portfolio_Portfolio
    */
    public function getQuoteByPolicyNumber($policyNumber){
        $quoteDataSource = new Datasource_Insurance_Portfolio_LegacyPortfolio();
        return $quoteDataSource->getRowById($policyNumber);
    }
      
    
    /**
    * Public function to save
    *
    * @param Model_Insurance_Portfolio_Portfolio $data The data to save 
    */
    public function save($data){
        $quoteDataSource = new Datasource_Insurance_Portfolio_LegacyPortfolio();
        return $quoteDataSource->save($data);

    }
    

    /**
    * gets a quote from the portfolio quote table portfoliostat by its auto id
    * @param int $id The id of the record to be retrieved
    * 
    * @return Model_Insurance_Portfolio_Portfolio The row as a Model_Insurance_Portfolio_Portfolio object
    * 
    * @author John Burrin
    */
    public function getQuoteById($id){
        $quoteDataSource = new Datasource_Insurance_Portfolio_LegacyPortfolio();
        return $quoteDataSource->getRowById($id);
    }
  
    /**
    * gets a quote from the portfolio quote table portfoliostat by its refno (UWP number)
    * @param string $rfNo The referance number (UWP Number) of the record to be retrieved
    * 
    * @return Model_Insurance_Portfolio_Portfolio The row as a Model_Insurance_Portfolio_Portfolio object
    * 
    * @author John Burrin
    */
    public function getQuoteByRefNo($refNo){
        $quoteDataSource = new Datasource_Insurance_Portfolio_LegacyPortfolio();
        return $quoteDataSource->getRowByRefNo($refNo);
    }

    /**
    * gets a quote from the portfolio quote table portfoliostat by its Cunstomer refno
    * @param string $rfNo The referance number (UWP Number) of the record to be retrieved
    * 
    * @return Model_Insurance_Portfolio_Portfolio The row as a Model_Insurance_Portfolio_Portfolio object
    * 
    * @author John Burrin
    */
    public function getQuoteByCustomerRefNo($refNo){
        $quoteDataSource = new Datasource_Insurance_Portfolio_LegacyPortfolio();
        return $quoteDataSource->getRowByCustomerRefNo($refNo);
    }
    
    /*
    * Delete a qiven quote by its IDs
    * @param int $id Indox of record to be deleted
    * @return void
    * @author John Burrin
    */
    public function deleteById($id){
        $quoteDataSource = new Datasource_Insurance_Portfolio_LegacyPortfolio();
        $quoteDataSource->deleteById($id);
    }
    
    /**
    * TODO: Document this
    * @param string refNo Referance numbers to be deleted
    * @return void
    * @author John Burrin
    * @since 1.3
    */
    public function deleteByRefNo($refNo){
        $quoteDataSource = new Datasource_Insurance_Portfolio_LegacyPortfolio();
        $quoteDataSource->deleteByRefNo($refNo);
        
    }
    
}

?>