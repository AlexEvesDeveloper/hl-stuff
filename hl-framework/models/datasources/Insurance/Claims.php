<?php

/**
 * Class Datasource_Insurance_Claims
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Datasource_Insurance_Claims extends Zend_Db_Table_Multidb
{
    /**
     * @var string table name
     */
    protected $_name = 'claims';

    /**
     * @var string primary key
     */
    protected $_primary = 'policynumber';

    /**
     * @var string database
     */
    protected $_multidb = 'db_legacy_homelet';
    
    /**
     * Save a quote object in the database
     *
     * @param Model_Insurance_Claims $claims this is the claims object you want saving
     * @return boolean
     */
    public function save(Model_Insurance_Claims $claims)
    {
        $success = true;

        // Firstly we need to see if this already exists
        $select=$this->select();
        $select->where('policynumber = ?', $claims->getPolicyNumber());
        $row = $this->fetchRow($select);

        $data = array(
                'claimnumber'         =>  $claims->getClaimNumber(),
                'policynumber'        =>  $claims->getPolicyNumber(),
                'claimdatetime'       =>  $claims->getClaimdatetime(),
                'incidentdatetime'    =>  $claims->getIncidentdatetime(),
                'incidentdescription' =>  $claims->getIncidentdescription(),
                'beingprocessed'      =>  $claims->getBeingprocessed(),
                'processed'           =>  $claims->getProcessed(),
                'claimstatus'         =>  $claims->getClaimstatus(),
            );
        if (count($row) > 0) {

            // Already exists so we are doing an update
            $where = $this->_db->quoteInto('policynumber = ?', $claims->getPolicyNumber());
            $this->update($data, $where);
        }
        else {
            // New claim so just insert
            if (!$this->insert($data)) {
                // Failed insertion
                Application_Core_Logger::log("Can't insert claim in table {$this->_name}", 'error');
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Load an existing claim from the database into the object
     *
     * @param string $policyNumber
     * @return null|Model_Insurance_Claims
     */
    public function getByPolicyNumber($policyNumber)
    {
        $select = $this->select()
                       ->where('policynumber = ?', $policyNumber);

        $row = $this->fetchRow($select);
        if ($row) {
            $claims = Model_Insurance_Claims::hydrate($row->toArray());
            return $claims;
        }
        return null;
    }
}
