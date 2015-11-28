<?php

/**
 * Class Datasource_Insurance_MTA
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Datasource_Insurance_MTA extends Zend_Db_Table_Multidb
{
    /**
     * @var string table name
     */
    protected $_name = 'MTA';

    /**
     * @var string primary key
     */
    protected $_primary = 'MTAId';

    /**
     * @var string database
     */
    protected $_multidb = 'db_legacy_homelet';
    
    /**
     * Save a mta object in the database
     *
     * @param Model_Insurance_MTA $mta this is the MTA object you want saving
     * @return bool|int
     */
    public function create(Model_Insurance_MTA $mta)
    {
        $data = array(
            'policynumber'                    => $mta->getPolicynumber(),
            'policyoptions'                   => $mta->getPolicyoptions(),
            'amountscovered'                  => $mta->getAmountscovered(),
            'optionpremiums'                  => $mta->getOptionpremiums(),
            'dateAdded'                       => $mta->getDateAdded(),
            'dateOnRisk'                      => $mta->getDateOnRisk(),
            'dateOffRisk'                     => $mta->getDateOffRisk(),
            'status'                          => $mta->getStatus(),
            'MTAId'                           => $mta->getMTAId(),
            'premium'                         => $mta->getPremium(),
            'quote'                           => $mta->getQuote(),
            'ipt'                             => $mta->getIpt(),
            'amountToPay'                     => $mta->getAmountToPay(),
            'AdminCharge'                     => $mta->getAdminCharge(),
            'displayNotes'                    => $mta->getDisplayNotes(),
            'monthsRemaining'                 => $mta->getmonthsRemaining(),
            'propAddress1'                    => $mta->getPropAddress1(),
            'propAddress3'                    => $mta->getPropAddress3(),
            'propAddress5'                    => $mta->getPropAddress5(),
            'propPostcode'                    => $mta->getPropPostcode(),
            'changeCorrespondenceAndPersonal' => $mta->getChangeCorrespondenceAndPersonal(),
            'riskArea'                        => $mta->getRiskArea(),
            'riskAreaB'                       => $mta->getRiskAreaB(),
            'paidNet'                         => $mta->getPaidNet(),
            'paragonMortgageNumber'           => $mta->getParagonMortgageNumber(),
        );

        $mtaID = $this->insert($data);
        if ( ! $mtaID) {
            // Failed insertion
            Application_Core_Logger::log("Can't insert MTA in table {$this->_name}", 'error');
            return false;
        }

        return $mtaID;
    }

    /**
     * Load an existing mta from the database into the object
     *
     * @param string $mtaID
     * @return null|Model_Insurance_MTA
     */
    public function getByMtaID($mtaID)
    {
        $select =
            $this->select()
                ->where('MTAId = ?', $mtaID);

        $row = $this->fetchRow($select);
        if ($row) {
            $mta = Model_Insurance_MTA::hydrate($row->toArray());
            return $mta;
        }
        return null;
    }

    /**
     * Updates the status of an existing MTA record
     *
     * @param string $policyNumber
     * @param int $mtaID
     * @param string $status
     * @return bool
     */
    public function updateStatus($policyNumber, $mtaID, $status)
    {
        $select = $this->select()
            ->where('MTAId = ?', $mtaID);

        $row = $this->fetchRow($select);
        if (count($row) != 1) {
            return false;
        }
        else if ($row->policynumber == $policyNumber) {
            $this->update(
                array('status' => $status),
                $this->_db->quoteInto('MTAId = ?', $mtaID)
            );
            return true;
        }
        return false;
    }

}
