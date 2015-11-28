<?php
/**
* TODO: Document this
* @param
* @return
* @author John Burrin
* @since
*/
class Datasource_Insurance_Portfolio_Rates extends Zend_Db_Table_Multidb {
    protected $_name = 'portfoliostat';
    protected $_primary = 'ID';
    protected $_multidb = 'db_homelet_insurance_com';
    
    /*
    select = $db->select()
             ->from(array('p' => 'products'),
                    array('product_id', 'product_name'))
             ->join(array('l' => 'line_items'),
                    'p.product_id = l.product_id');
    */
    public function fetch(){
       $select = $this->select()
            ->from(array('p' => 'postcode_merge'))
            ->join(array())
            ->where('ID = ?', $id);
    }


}

?>