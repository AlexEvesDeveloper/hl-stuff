<?php

class Datasource_Referencing_Product extends Zend_Db_Table_Multidb {

    protected $_multidb = 'db_referencing';
    protected $_name = 'product';
    protected $_primary = 'id';

    /**
     * Retrieves the product recorded against the Reference identifier.
     *
     * @param integer $referenceId
     * The unique Reference identifier.
     *
     * @return mixed
     * The Product details, encapsulated in a Model_Referencing_Product object,
     * or null if the Product cannot be found.
     */
    public function getByReferenceId($referenceId) {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('ps' => 'product_selection'));
        $select->where('ps.reference_id = ?', $referenceId);
        $productSelectionRow = $this->fetchRow($select);

        if(!empty($productSelectionRow)) {

            $returnVal = $this->getById($productSelectionRow->product_id);
        }
        else {

            //Create empty object.
            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ': Unable to find product.');
            $returnVal = null;
        }

        return $returnVal;
    }

    /**
    * Returns a specific product located by its identifier.
    *
    * @param integer $id
    * The product identifier
    *
    * @return mixed
    * Returns a Model_Referencing_Product encapsulating the product details,
    * or null if no matching product found.
    */
    public function getById($id) {

        $returnVal = null;
        if(is_numeric($id)) {

            $select = $this->select();
            $select->where('id = ?', $id);
            $productRow = $this->fetchRow($select);

            if(!empty($productRow)) {

                $returnVal = $this->_createDomainObject($productRow);
            }
        }

        return $returnVal;
    }

    /**
    * Returns a specific product located by its name.
    *
    * @param string $name
    * The product's name. Case insensitive.
    *
    * @return mixed
    * Returns a Model_Referencing_Product encapsulating the product details,
    * or null if no matching product found.
    */
    public function getByName($name) {

        $select = $this->select()->from;
        $select->where('name = ?', $name);
        $productRow = $this->fetchRow($select);

        if(empty($productRow)) {

            Application_Core_Logger::log(get_class() . '::' . __FUNCTION__ . ':Unable to find product.');
            $returnVal = null;
        }
        else {

            $returnVal = $this->_createDomainObject($productRow);
        }

        return $returnVal;
    }

    public function getByVariable($productVariable) {
		
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(
				array('p' => $this->_name),
				array('id','name','is_active','display_order')
			)
			->join(array('m' => 'product_variables_map'), 'p.id = m.product_id')
			->where('m.product_variable_id = ?', $productVariable)
			->where('p.is_active = 1')
			->group('p.id')
			->order('p.display_order');

		$results = $this->fetchAll($select);

        $productArray = array();
        foreach($results as $currentRow) {

            $productArray[] = $this->_createDomainObject($currentRow);
        }

		//Returns products matching the variables passed in.
        return $productArray;
    }

    public function getAll($orderProducts = false) {

        // Returns all products
        $select = $this->select();

        if ($orderProducts) {

            if (!is_string($orderProducts)) {

                $orderProducts = 'display_order';
            }

            $select->order($orderProducts);
        }

        $results = $this->fetchAll($select);

        $productArray = array();
        foreach($results as $currentRow) {

            $productArray[] = $this->_createDomainObject($currentRow);
        }

        return $productArray;
    }

    /**
     * Creates a Product domain object from a Zend_Db_Table_Row_Abstract.
     *
     * @param Zend_Db_Table_Row_Abstract $productRow
     * Row from the datasource containing the details to populate into the
     * domain object.
     *
     * @return Model_Referencing_Product
     * The domain object encapsulating the datasource row.
     */
    protected function _createDomainObject($productRow) {

        $product = new Model_Referencing_Product();
        $product->key = $productRow->id;
        $product->name = $productRow->name;
        $product->isActive = ($productRow->is_active == 1) ? true : false;
        $product->displayOrder = $productRow->display_order;
        return $product;
    }
}

?>