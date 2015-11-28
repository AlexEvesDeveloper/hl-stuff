<?php

/**
* Model definition for the legacy Product datasource.
*/
class Datasource_ReferencingLegacy_Product extends Zend_Db_Table_Multidb {

    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_legacy_referencing';
    protected $_name = 'Product';
    protected $_primary = 'ID';
    /**#@-*/

    public function getProductById($productId) {

        if(empty($productId)) {
            
            return null;
        }
        
        $select = $this->select();
        $select->where('ID = ?', $productId);
        $productRow = $this->fetchRow($select);

        if(empty($productRow)) {

            $returnVal = null;
        }
        else {

            $returnVal = $this->_createDomainObject($productRow);
        }

        return $returnVal;
    }

    /**
    * Returns a specific product located by its name.
    *
    * @param string $productName
    * @return Model_Referencing_Product
    * @throws Zend_Exception
    */
    public function getProductByName($productName) {

        $select = $this->select();
        $select->where('Name = ?', $productName);
        $productRow = $this->fetchRow($select);

        if (empty($productRow)) {
            throw new Zend_Exception(get_class() . '::' . __FUNCTION__ . ':Unable to find product.');
        }
        $returnVal = $this->_createDomainObject($productRow);

        return $returnVal;
    }

    /**
     * Creates a Product domain object from a Zend_Db_Table_Row_Abstract.
     *
     * @param Zend_Db_Table_Row_Abstract $productRow
     * Row from the legacy datasource containing the details to populate into the
     * domain object.
     *
     * @return Model_Referencing_Product
     * The domain object encapsulating the legacy datasource row.
     */
    protected function _createDomainObject(Zend_Db_Table_Row_Abstract $productRow) {

        $product = new Model_Referencing_Product();
        $product->key = $productRow->ID;
        $product->name = $productRow->Name;
        $product->length = $productRow->fixedPolicyLength;

        //Assign the applicable product variables.
        $productVariables = array();
        if($productRow->RentGuarantee == 1) {

            $productVariables[Model_Referencing_ProductVariables::RENT_GUARANTEE] = 1;
        }
        else {

            $productVariables[Model_Referencing_ProductVariables::NON_RENT_GUARANTEE] = 1;
        }

        if($productRow->LEGALSERVICES == 1) {

            $productVariables[Model_Referencing_ProductVariables::LEGAL_EXPENSES] = 1;
        }
        else {

            $productVariables[Model_Referencing_ProductVariables::NON_LEGAL_EXPENSES] = 1;
        }

        if($productRow->checkfield == 'creditreference') {

            $productVariables[Model_Referencing_ProductVariables::CREDIT_REFERENCE] = 1;
        }
        else {

            $productVariables[Model_Referencing_ProductVariables::FULL_REFERENCE] = 1;
        }

        if($productRow->hasvariablelength == 1) {

            $productVariables[Model_Referencing_ProductVariables::VARIABLE_DURATION] = 1;
        }
        else {

            $productVariables[Model_Referencing_ProductVariables::FIXED_DURATION] = 1;
        }

        if(preg_match("/international/i", $productRow->Name)) {

            $productVariables[Model_Referencing_ProductVariables::INTERNATIONAL] = 1;
        }
        else {

            $productVariables[Model_Referencing_ProductVariables::NATIONAL] = 1;
        }

        if(preg_match("/advantage/i", $productRow->Name)) {

            $productVariables[Model_Referencing_ProductVariables::NO_EXCESS] = 1;
        }
        else {

            if(isset($productVariables[Model_Referencing_ProductVariables::RENT_GUARANTEE])) {

                //Product is a rent guarantee, so excess applies.
                $productVariables[Model_Referencing_ProductVariables::EXCESS_APPLIES] = 1;
            }
            else {

                //Product is not a rent guaratee, so excess is not applicable.
                $productVariables[Model_Referencing_ProductVariables::EXCESS_NOT_APPLICABLE] = 1;
            }
        }

        $product->variables = $productVariables;

        if($productRow->Active == 1) {

            $product->isActive = true;
        }
        else {

            $product->isActive = false;
        }
        return $product;
    }

    /**
     * Not yet implemented.
     */
    public function getMatchingProducts(array $productVariables) {

        //If null, write to the Core_Logger.
        throw new Zend_Exception(get_class() . __FUNCTION__ . ': not implemented in this release.');
    }
}
