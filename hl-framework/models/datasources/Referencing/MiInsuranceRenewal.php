<?php

/**
* Model definition for the Enquiry datasource. The Enquiry links together all aspects of the
* referencing process. The Enquiry identifier can be used to identify all related data,
* not just that in the Enquiry datasource.
*/
class Datasource_Referencing_MiInsuranceRenewal extends Zend_Db_Table_Multidb 
{
    /**#@+
     * Mandatory attributes
     */
    protected $_multidb = 'db_referencing';
    protected $_name = 'mi_insurance_renewals';
    protected $_primary = 'customer_id';
    /**#@-*/

    /**
     * Insert data for a new customer registration for their 
     * next insurance renewal.
     *
     * @param int $customerid Customer ID
     * @param \Zend\Date $renewaldate Renewal date
     */
    public function insertMiData($customerid, $renewaldate)
    {
        $data = array(
            'customer_id' => $customerid,
            'renewal_date' => $renewaldate->get(Zend_Date::YEAR . '-' . Zend_Date::MONTH . '-' . Zend_Date::DAY)
        );

        $this->insert($data);
    }
}

