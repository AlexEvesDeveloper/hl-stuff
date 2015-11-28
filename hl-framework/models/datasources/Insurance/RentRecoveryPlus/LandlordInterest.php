<?php

/**
 * Class Datasource_Insurance_RentRecoveryPlus_LandlordInterest
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Datasource_Insurance_RentRecoveryPlus_LandlordInterest extends Zend_Db_Table_Multidb
{
    /**
     * @var string table name
     */
    protected $_name = 'landlord_interest';

    /**
     * @var string primary key
     */
    protected $_primary = 'policynumber';

    /**
     * @var string database
     */
    protected $_multidb = 'db_legacy_homelet';

    /**
     * Saves the data to the landlord_interest table
     *
     * @param Model_Insurance_RentRecoveryPlus_LandlordInterest $llInterest
     * @return bool
     */
    public function save(Model_Insurance_RentRecoveryPlus_LandlordInterest $llInterest)
    {
        $wasSuccessful = true;

        // Firstly we need to see if this already exists
        $select = $this->select();
        $select->where('policynumber = ?', $llInterest->getPolicyNumber());
        $row = $this->fetchRow($select);

        $data = array(
            'policynumber'       => $llInterest->getPolicyNumber(),
            'title'              => $llInterest->getTitle(),
            'firstname'          => $llInterest->getFirstName(),
            'lastname'           => $llInterest->getLastName(),
            'email'              => $llInterest->getEmailAddress(),
            'phone'              => $llInterest->getPhoneNumber(),
            'address1'           => $llInterest->getAddress1(),
            'address2'           => $llInterest->getAddress2(),
            'address3'           => $llInterest->getAddress3(),
            'postcode'           => $llInterest->getPostcode(),
            'country'            => $llInterest->getCountry(),
            'is_foreign_address' => $llInterest->getIsForeignAddress(),
        );

        if (count($row) > 0) {

            // Already exists so we are doing an update
            $where = $this->_db->quoteInto('policynumber = ?', $llInterest->getPolicyNumber());
            $this->update($data, $where);
        }
        else {
            // New quote so just insert
            if (!$this->insert($data)) {
                // Failed insertion
                Application_Core_Logger::log("Can't insert quote in table landlord_interest", 'error');
                $wasSuccessful = false;
            }
        }
        return $wasSuccessful;
    }

    /**
     * When the quote is accepted this changes the quote number to the policy number
     *
     * @param string $quoteNumber
     * @param string $policyNumber
     * @return bool
     */
    public function accept($quoteNumber, $policyNumber)
    {
        $isSuccessful = false;

        $data = array(
            'policynumber' => $policyNumber,
        );

        // Firstly we need to see if this already exists
        $select=$this->select();
        $select->where('policynumber = ?', $quoteNumber);
        $row = $this->fetchRow($select);

        // Check that a single row exists, if duplicates exists something bad happened!
        if (count($row) == 1) {

            // Already exists so we are doing an update
            $where = $this->_db->quoteInto('policynumber = ?', $quoteNumber);
            $this->update($data, $where);
            $isSuccessful = true;
        }

        return $isSuccessful;
    }

    /**
     * Gets the landlord_interest record for the given policy number
     *
     * @param string $policyNumber
     * @return Model_Insurance_RentRecoveryPlus_LandlordInterest|null
     */
    public function getLandlordInterest($policyNumber)
    {
        $select = $this->select()
            ->where('policynumber = ?', $policyNumber);

        $row = $this->fetchRow($select);
        if ($row) {
            return Model_Insurance_RentRecoveryPlus_LandlordInterest::hydrateFromRow($row->toArray());
        }
        return null;
    }
}