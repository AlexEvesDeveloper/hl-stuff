<?php
/**
* TODO: Document this
* @param
* @return
* @author John Burrin
* @since
*/
class Datasource_Insurance_Portfolio_PortfolioRates extends Zend_Db_Table_Multidb {
    protected $_name = 'product';
    protected $_primary = 'productID';
    protected $_multidb = 'db_legacy_homelet';
    

    /**
    * This fetch function, fetches the portfolio product rates 
    * @param Integer $productID This is the product id of the rates to retrieve, it is defaulted to 1 for
    * Protfolio as I don't belive we will use this rates model any where else
    * @return array, and array of array containg the rates information as rows
    * @author John Burrin
    * @since 1.3
    */
    public function fetchRates($productId = 1){
       $select = $this->select()
            ->from(array('p' => $this->_name))
            ->setIntegrityCheck(false)
            ->join(array('o' => 'productOptions'),'p.productID = o.productID')
            ->join(array('s' => 'policyOptions'),'o.policyOptionID = s.policyOptionID')
            ->join(array('r' => 'productRates'),'o.productOptionsID = r.productOptionsID')
            ->where('p.productID = ?', $productId);
            
            $rows = $this->fetchAll($select);
            $options = array();
            if(!empty($rows)){              
                foreach($rows as $row){
               		$optionName = $row["policyOption"];

                    switch ($optionName) {
                        case 'buildings':
                             $options['buildingNet'][$row["riskarea"]-1] = $row["netRate"];
                            break;
                        case 'contentsl':
                            $options['contentsNet'][$row["riskarea"]-1] = $row["netRate"];
                            break;
                        case 'buildingsAccidentalDamage':
                            $options['buildingsAD_multiplyer'] = $row["netRate"];
                            break;
                        case 'buildingsNoExcess':
                            $options['buildingsNE_multiplyer'] = $row["netRate"];
                            break;
                        case 'contentslAccidentalDamage':
                            $options['contentsAD_multiplyer'] = $row["netRate"];
                            break;
                        case 'contentslNoExcess':
                            $options['contentsNE_multiplyer'] = $row["netRate"];
                            break;
                        case 'limitedcontents':
                            $options['limited_contents_price'] = $row["netRate"];
                            break;
                    }
                }
                return $options;
            }else{
                return false;
            }
    }

        /**
     * Fetch options from the database
     *
     * @param none
     * @return array
     */
    public function fetchOptionsByProduct($productName) {
       $select = $this->select()
            ->from(array('p' => $this->_name))
            ->setIntegrityCheck(false)
            ->join(array('o' => 'productOptions'),'p.productID = o.productID')
            ->join(array('s' => 'policyOptions'),'o.policyOptionID = s.policyOptionID')
            ->join(array('r' => 'productRates'),'o.productOptionsID = r.productOptionsID')
            ->where('p.productID = ?', 1);
            
            $rows = $this->fetchAll($select);
            if(!empty($rows)){   
            return $rows;
        } else {
            // Can't find policy options for this type - log a warning
            Application_Core_Logger::log("Policy options not found in database (productName = $productName)", 'warning');
            return false;
        }
    }

    
    /**
    * TODO: Document this
    * @param
    * @return
    * @author John Burrin
    * @since
    */
    public function fetchFactors($dateBoundary,$productId = 1){

        $select = $this->select()
            ->from(array('p' => $this->_name))
            ->setIntegrityCheck(false)
            
            ->join(array('pf' => 'productFactors'),'p.productID = pf.productID')
            ->join(array('f' => 'factors'),'f.factorID = pf.factorID')
            
            ->where('p.prodEndDate = ?', '0000-00-00')
            ->where('pf.factorStartDate <= ?', $dateBoundary)
            ->where('pf.factorEndDate >= ? or pf.factorEndDate = 0', $dateBoundary)
            ->where('p.productID = ?', $productId);
            
        $rows = $this->fetchAll($select);

        if(!empty($rows)){
            $factors = array();
            $factors['professionalRate'] = array();
            $factors['sumInsuredDiscRate'] = array();
            $factors['netRate_multiplyer'] = array();
            $factors['ipt_multiplyer'] = array();
            $factors['gross_premium_multiplyer'] = array();
            $factors['netnetPremium_multiplyer'] = array();
            $factors['commission'] = array();
            $factors['claimRate'] = array();
            
            
            $profRate = array();
            $claimRate = array();
        	$sumInsuredDiscRate = array();
            foreach($rows as $row){
        
            $factor = $row["factorName"];
    
            switch ($factor) {
                case 'professionalRate':
                     $factors['professionalRate'][$row["lowerLimit"].'-'.$row["upperLimit"]]  = $row["factorValue"];
                    break;
                case 'netRate':
                    $factors['netRate_multiplyer'] = $row["factorValue"];                     
                    break;
                case 'ipt':
                    $factors['ipt_multiplyer'] = $row["factorValue"];
                    break;
                case 'grossPremium':
                    $factors['gross_premium_multiplyer'] = $row["factorValue"];
                    break;
                case 'netnetPremium':
                    $factors['netnetPremium_multiplyer'] = $row["factorValue"];
                    break;
                case 'commission':
                     $factors['commission'] = $row["factorValue"];
                    break;
                case 'serviceCharge':
                    $factors['serviceCharge'] = $row["factorValue"];
                    break;
                case 'claimRate':
                    $factors['claimRate'][$row["lowerLimit"].'-'.$row["upperLimit"]] = $row["factorValue"];
                    break;
                case 'sumInsuredDiscRate':
                    $factors['sumInsuredDiscRate'][$row["lowerLimit"].'-'.$row["upperLimit"]] = $row["factorValue"];
                    break;
                }
             
            }
        }
        return $factors;
    }
    /*
     SELECT * FROM product p, productFactors pf, factors f  WHERE
		p.productID=1 AND
		p.prodEndDate = '0000-00-00' AND
		p.productID = pf.productID AND
		pf.factorStartDate <= $dateBoundary AND
		(pf.factorEndDate >= $dateBoundary OR pf.factorEndDate = 0) AND
		f.factorID = pf.factorID
    */
}
