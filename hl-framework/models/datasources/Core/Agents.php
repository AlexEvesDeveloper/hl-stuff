<?php

/**
 * Model definition for the newagents table.
 *
 * @category   Datasource
 * @package    Datasource_Core
 * @subpackage Agents
 */

class Datasource_Core_Agents extends Zend_Db_Table_Multidb {

    protected $_name = 'newagents';
    protected $_primary = 'agentschemeno';
    protected $_multidb = 'db_legacy_homelet';
    protected $_dependentTables = array ('Model_Agent_Emailaddresses');

    /**
     * Retrieves a list of all agent scheme numbers in existence.
     *
     * @param bool $active Optional boolean filter to include active ASNs in results.
     * @param bool $inactive Optional boolean filter to include inactive ASNs in results.
     *
     * @return array Array of ASNs.
     */
    public function getSchemeNumbers($active = true, $inactive = false) {

        if ($active == false && $inactive == false) {

            // Get no ASNs
            return array();
        }

        $where = '( ';

        if ($active == true && $inactive == false) {

            // Only get active ASNs

            // TODO: Fix filter against rubbish legacy way of marking closed accounts - currently by their name(!)
            $where .= $this->_db->quoteInto("na.name NOT LIKE '%Closed%' AND na.name NOT LIKE '%Close Account%' ", '');

            // Filter against new AGENTCANCELLATION table
            $where .= $this->_db->quoteInto('AND NOT EXISTS (SELECT 1 FROM AGENTCANCELLATION AS ac WHERE na.agentschemeno = ac.AGENTSCHEMENUMBER)', '');

        } elseif ($active == false && $inactive == true) {

            // Only get inactive ASNs

            // TODO: Fix filter against rubbish legacy way of marking closed accounts - currently by their name(!)
            $where .= $this->_db->quoteInto("na.name LIKE '%Closed%' OR na.name LIKE '%Close Account%' ", '');

            // Filter against new AGENTCANCELLATION table
            $where .= $this->_db->quoteInto('OR EXISTS (SELECT 1 FROM AGENTCANCELLATION AS ac WHERE na.agentschemeno = ac.AGENTSCHEMENUMBER)', '');

        } else {

            // Get all ASNs

            $where .= $this->_db->quoteInto('1 = 1', '');
        }

        $where .= ' )';

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('na' => $this->_name),
                array('agentschemeno')
            )
            ->where($where)
            ->order('agentschemeno ASC');

        $agents = array();

        $result = $this->fetchAll($select);
        foreach ($result as $row) {
            if (($row['agentschemeno'] != '') && ($row['agentschemeno'] != '0')) {
                $agents[] = $row['agentschemeno'];
            }
        }

        return $agents;
    }

    /**
     * Retrieves an agent from the datasource.
     *
     * @param mixed $agentSchemeNumber
     * Identifies the agent to retrieve. Permitted values are integer
     * or string.
     *
     * @return mixed
     * Returns a Model_Core_Agent object encapsulating the agent details,
     * or null if not found.
     */
    public function getAgent($agentSchemeNumber) {

        $select = $this->select();
        $select->where('agentschemeno = ?', $agentSchemeNumber);
        $agentRow = $this->fetchRow($select);

        if(!empty($agentRow)) {

            $addressOffice = $this->_legacyAddressToObject(array(
                    $agentRow->address1,
                    $agentRow->address2,
                    $agentRow->address3,
                    $agentRow->address4,
                    $agentRow->postcode
                )
            );
            $phoneAndFaxOffice = new Model_Core_ContactDetails();
            $phoneAndFaxOffice->telephone1 = $agentRow->phone;
            $phoneAndFaxOffice->fax1 = $agentRow->fax;
            $contactMappedOffice = new Model_Core_Agent_ContactMap();
            $contactMappedOffice->category = Model_Core_Agent_ContactMapCategory::OFFICE;
            $contactMappedOffice->address = $addressOffice;
            $contactMappedOffice->phoneNumbers = $phoneAndFaxOffice;

            $addressInvoice = $this->_legacyAddressToObject(array(
                    $agentRow->invoiceaddress1,
                    $agentRow->invoiceaddress2,
                    $agentRow->invoiceaddress3,
                    $agentRow->invoiceaddress4,
                    $agentRow->invoicepostcode
                )
            );
            $phoneAndFaxInvoice = new Model_Core_ContactDetails();
            $phoneAndFaxInvoice->telephone1 = $agentRow->accountsphone;
            $phoneAndFaxInvoice->fax1 = $agentRow->accountsfax;
            $contactMappedInvoice = new Model_Core_Agent_ContactMap();
            $contactMappedInvoice->category = Model_Core_Agent_ContactMapCategory::INVOICE;
            $contactMappedInvoice->address = $addressInvoice;
            $contactMappedInvoice->phoneNumbers = $phoneAndFaxInvoice;

            $addressCorrespondence = $this->_legacyAddressToObject(array(
                    $agentRow->correspondAddress1,
                    $agentRow->correspondAddress2,
                    $agentRow->correspondAddress3,
                    $agentRow->correspondAddress4,
                    $agentRow->correspondPostcode
                )
            );
            $phoneAndFaxCorrespondence = new Model_Core_ContactDetails();
            $phoneAndFaxCorrespondence->telephone1 = null;
            $phoneAndFaxCorrespondence->fax1 = null;
            $contactMappedCorrespondence = new Model_Core_Agent_ContactMap();
            $contactMappedCorrespondence->category = Model_Core_Agent_ContactMapCategory::CORRESPONDENCE;
            $contactMappedCorrespondence->address = $addressCorrespondence;
            $contactMappedCorrespondence->phoneNumbers = $phoneAndFaxCorrespondence;

            $contact = array($contactMappedOffice, $contactMappedInvoice, $contactMappedCorrespondence);

            $status = null;
            switch($agentRow->status) {
                case 'live':
                    $status = Model_Core_Agent_Status::LIVE;
                    break;
                case 'onstop':
                    $status = Model_Core_Agent_Status::ON_STOP;
                    break;
                case 'cancelled':
                    $status = Model_Core_Agent_Status::CANCELLED;
                    break;
                case 'onhold':
                    $status = Model_Core_Agent_Status::ON_HOLD;
                    break;
            }

            // TODO: This should be based on a lookup using `AGENTPREMIERSTATUS`
            $premierStatus = null;
            switch($agentRow->agentPremierStatusID) {
                case '1':
                    $premierStatus = Model_Core_Agent_PremierStatus::STANDARD;
                    break;
                case '2':
                    $premierStatus = Model_Core_Agent_PremierStatus::PREMIER;
                    break;
                case '3':
                    $premierStatus = Model_Core_Agent_PremierStatus::PREMIER_PLUS;
                    break;
            }

            $referencePriceType = null;
            switch($agentRow->refprices) {
                case 'supply':
                    $referencePriceType = Model_Core_Agent_ReferencePriceType::SUPPLY;
                    break;
                case 'retail':
                    $referencePriceType = Model_Core_Agent_ReferencePriceType::RETAIL;
                    break;
            }

            // Create and populate agent domain object
            $agent = new Model_Core_Agent();
            $agent->name = $agentRow->name;
            $agent->contactname = $agentRow->contactname;
            $agent->accountscontactname = $agentRow->accountscontactname;
            $agent->agentSchemeNumber =                     $agentSchemeNumber;
            $agent->homeLetRef =                            $agentRow->homeletref;
            $agent->agentsRateID =                          $agentRow->agentsRateID;
            $agent->logo =                                  $agentRow->logo;
            $agent->documentLogo =                          $agentRow->document_logo;
            $agent->contact =                               $contact;
            $agent->wantDailyApplicationProgressUpdate =    $agentRow->WantDAPU;
            $agent->marketingToTenantsOptIn =               ($agentRow->Optin == 'yes') ? true : false;
            $agent->status =                                $status;
            $agent->premierStatus =                         $premierStatus;
            $agent->enableExternalNews =                    ($agentRow->enableExternalNews == 'yes') ? true : false;
            $agent->agentReferencePriceType =               $referencePriceType;
            $agent->agentStartDate =                        ($agentRow->agent_startdate == '') ? new Zend_Date() : new Zend_Date($agentRow->agent_startdate);
            $agent->agentLapseDate =                        ($agentRow->agent_lapsedate == '') ? new Zend_Date() : new Zend_Date($agentRow->agent_lapsedate);
            $agent->premierStartDate =                      ($agentRow->premier_startdate == '') ? new Zend_Date() : new Zend_Date($agentRow->premier_startdate);
            $agent->premierLapseDate =                      ($agentRow->premier_lapsedate == '') ? new Zend_Date() : new Zend_Date($agentRow->premier_lapsedate);
            $agent->commissionRate =                        $agentRow->commissionrate;
            $agent->newBusinessCommissionRate =             $agentRow->newbuscommissionrate;
            $agent->salespersonId =                         $agentRow->salesman;
            $agent->agentsDealGroupID =                     $agentRow->rateDealGroupID;
            $agent->absoluteType =                          $this->getAbsoluteType($agentSchemeNumber);
            $agent->decommissionInHrtAt =                   $agentRow->decommission_in_hrt_at;
            $agent->hasProductAvailabilityMapping =         $agentRow->has_product_availability_mapping;

            // TODO: The redundant stuffs below need removing - it's already in
            //   the $agent->contact property.  See PB for why it's has to stay
            //   here for now.
            $agent->town =                                     $agentRow->address1;
            if ($agentRow->address2!='') $agent->town =     $agentRow->address2;
            if ($agentRow->address3!='') $agent->town =     $agentRow->address3;
            if ($agentRow->address4!='') $agent->town =     $agentRow->address4;

            $returnVal = $agent;
        }
        else {

            $returnVal = null;
        }

        return $returnVal;
    }


    /**
     * Get all agent types available.
     *
     * @return array Array of (mixed)agent type ID => (string)label tuples.
     */
    public function getAllTypes() {

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('t' => 'NEWAGENTTYPELOOKUP')
            );
        $allTypes = $this->fetchAll($select);
        $returnVal = array();
        foreach ($allTypes as $typeRow) {
            $returnVal[$typeRow->NEWAGENTTYPELOOKUPID] = $typeRow->LABEL;
        }

        return $returnVal;
    }

    /**
     * Get an agent's type.
     *
     * @param mixed $agentSchemeNumber
     *
     * @return mixed Const from Model_Core_Agent_Type or null if none found.
     */
    public function getType($agentSchemeNumber) {

        /*
         * SELECT t.*
         *   FROM newagents AS n JOIN NEWAGENTTYPELOOKUP AS t
         *   ON n.NEWAGENTTYPELOOKUPID = t.NEWAGENTTYPELOOKUPID
         *   WHERE n.agentschemeno = $agentSchemeNumber;
         */
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('n' => $this->_name),
                array()
            )
            ->join(
                array('t' => 'NEWAGENTTYPELOOKUP'),
                'n.NEWAGENTTYPELOOKUPID = t.NEWAGENTTYPELOOKUPID'
            )
            ->where('n.agentschemeno = ?', $agentSchemeNumber);

        $typeRow = $this->fetchRow($select);

        $returnVal = null;
        if (!empty($typeRow)) {
            switch($typeRow->LABEL) {
                case 'historicaccount':
                    $returnVal = Model_Core_Agent_Type::HISTORIC_ACCOUNT;
                    break;
                case 'newcustomer':
                    $returnVal = Model_Core_Agent_Type::NEW_CUSTOMER;
                    break;
                case 'returningcustomer':
                    $returnVal = Model_Core_Agent_Type::RETURNING_CUSTOMER;
                    break;
                case 'legalentitychange':
                    $returnVal = Model_Core_Agent_Type::LEGAL_ENTITY_CHANGE;
                    break;
            }
        }

        return $returnVal;
    }

    /**
     * Set an agent's type.
     *
     * @param mixed Const from Model_Core_Agent_Type.
     * @param mixed $agentSchemeNumber
     *
     * @return void
     */
    public function setType($type, $agentSchemeNumber) {

        $allTypes = $this->getAllTypes();
        foreach($allTypes as $key => $val) {
            switch($val) {
                case 'historicaccount':
                    if ($type == Model_Core_Agent_Type::HISTORIC_ACCOUNT) {
                        $translatedType = $key;
                        break 2;
                    }
                    break;
                case 'newcustomer':
                    if ($type == Model_Core_Agent_Type::NEW_CUSTOMER) {
                        $translatedType = $key;
                        break 2;
                    }
                    break;
                case 'returningcustomer':
                    if ($type == Model_Core_Agent_Type::RETURNING_CUSTOMER) {
                        $translatedType = $key;
                        break 2;
                    }
                    break;
                case 'legalentitychange':
                    if ($type == Model_Core_Agent_Type::LEGAL_ENTITY_CHANGE) {
                        $translatedType = $key;
                        break 2;
                    }
                    break;
            }
        }

        $where = $this->getAdapter()->quoteInto('agentschemeno = ?', $agentSchemeNumber);
        $update = $this->update(array('NEWAGENTTYPELOOKUPID' => $translatedType), $where);
    }

    /**
     * Get all agent Absolute types available.
     *
     * @return array Array of (mixed)agent Absolute type ID => (string)name tuples.
     */
    public function getAllAbsoluteTypes() {

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('t' => 'absoluteType')
            );
        $allTypes = $this->fetchAll($select);
        $returnVal = array();
        foreach ($allTypes as $typeRow) {
            $returnVal[$typeRow->id] = $typeRow->name;
        }

        return $returnVal;
    }

    /**
     * Get an agent's Absolute type.
     *
     * @param mixed $agentSchemeNumber
     *
     * @return mixed Const from Model_Core_Agent_AbsoluteType or null if none found.
     */
    public function getAbsoluteType($agentSchemeNumber) {

        /*
         * SELECT t.*
         *   FROM newagents AS n JOIN absoluteType AS t
         *   ON n.absoluteType_id = t.id
         *   WHERE n.agentschemeno = $agentSchemeNumber;
         */
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('n' => $this->_name),
                array()
            )
            ->join(
                array('t' => 'absoluteType'),
                'n.absoluteType_id = t.id'
            )
            ->where('n.agentschemeno = ?', $agentSchemeNumber);

        $typeRow = $this->fetchRow($select);

        $returnVal = null;
        if (!empty($typeRow)) {
            switch($typeRow->name) {
                case 'Absolute':
                    $returnVal = Model_Core_Agent_AbsoluteType::ABSOLUTE;
                    break;
                case 'Promise':
                    $returnVal = Model_Core_Agent_AbsoluteType::PROMISE;
                    break;
                case 'Essential':
                    $returnVal = Model_Core_Agent_AbsoluteType::ESSENTIAL;
                    break;
            }
        }

        return $returnVal;
    }

    /**
     * Set an agent's Absolute type.
     *
     * @param mixed Const from Model_Core_Agent_AbsoluteType.
     * @param mixed $agentSchemeNumber
     *
     * @return void
     */
    public function setAbsoluteType($absoluteType, $agentSchemeNumber) {

        $allAbsoluteTypes = $this->getAllAbsoluteTypes();
        foreach($allAbsoluteTypes as $key => $val) {
            switch($val) {
                case 'Absolute':
                    if ($absoluteType == Model_Core_Agent_AbsoluteType::ABSOLUTE) {
                        $translatedType = $key;
                        break 2;
                    }
                    break;
                case 'Promise':
                    if ($absoluteType == Model_Core_Agent_AbsoluteType::PROMISE) {
                        $translatedType = $key;
                        break 2;
                    }
                    break;
                case 'Essential':
                    if ($absoluteType == Model_Core_Agent_AbsoluteType::ESSENTIAL) {
                        $translatedType = $key;
                        break 2;
                    }
                    break;
            }
        }

        $where = $this->getAdapter()->quoteInto('agentschemeno = ?', $agentSchemeNumber);
        $update = $this->update(array('absoluteType_id' => $translatedType), $where);
    }

    /**
    * Finds a specific agent
    *
    * @param integer $agentSchemeNo
    * The agent scheme number.
    *
    * @deprecated
    * Deprecated. Use $this->getAgent($agentSchemeNumber) instead.
    *
    * @return Zend_Db_Select
    */
    public function getBySchemeNumber($agentSchemeNo) {

        $select = $this->select();
        return $this->select()->where('agentschemeno = ?', $agentSchemeNo);
    }

    /**
     * @deprecated
     * This shouldn't exist - use the getAgent function above!!
     */
    public function getDetailsByASN($agentSchemeNo) {
        $select = $this->select();
        $select->where('agentschemeno = ?', $agentSchemeNo);
        $agentRow = $this->fetchRow($select);

        if (count($agentRow)>0) {
            $town = $agentRow->address1;
            if ($agentRow->address2 != '') $town = $agentRow->address2;
            if ($agentRow->address3 != '') $town = $agentRow->address3;
            if ($agentRow->address4 != '') $town = $agentRow->address4;

            return array(
                'name'    =>    $agentRow->name,
                'town'    =>    $town,
                'asn'    =>    $agentRow->agentschemeno
            );
        } else {
            return array();
        }
    }

    /**
     * Finds agents that match (scheme number) or (name and town), each of which may be partially given.
    *
     * @param string $agentAsn Agent's scheme number.
     * @param string $agentName Agent's name.
     * @param string $agentTown Agent's town.
    *
     * @return array Simple array of results, primarily for display.
    */
    public function searchByAsnOrNameAndAddress($agentAsn = '', $agentName = '', $agentTown = '') {
        if (strlen($agentAsn) >= 5) {
            $where = $this->_db->quoteInto("agentschemeno LIKE ? AND name NOT LIKE '%Closed%' AND name NOT LIKE '%Close Account%' AND status NOT IN ('cancelled','onhold')", "%".$agentAsn."%");
        } elseif (strlen($agentName) >= 3 && strlen($agentTown) >= 3) {
            $where = '( ';

            // Handle matching synonymous abbreviations in names
            $nameVariants = array($agentName);
            $nameSynonyms = array(
                ' & ' => ' and ',
                'ltd' => 'limited',
                'co.' => 'company',
            );
            foreach($nameSynonyms as $syn1 => $syn2) {
                $t_nameVariants = $nameVariants;
                foreach($nameVariants as $name) {
                    if (stripos($name, $syn1) !== false) {
                        $t_name = str_ireplace($syn1, $syn2, $name);
                        $t_nameVariants[] = $t_name;
                    }
                    if (stripos($name, $syn2) !== false) {
                        $t_name = str_ireplace($syn2, $syn1, $name);
                        $t_nameVariants[] = $t_name;
                    }
                }
                $nameVariants = $t_nameVariants;
            }
            foreach($nameVariants as $name) {
                $where .= $this->_db->quoteInto('name LIKE ? OR ', "%{$name}%");
            }
            $where = substr($where, 0, -3) . ') ';

            $where .= $this->_db->quoteInto('AND ( address1 LIKE ? OR ', "%{$agentTown}%");
            $where .= $this->_db->quoteInto('address2 LIKE ? OR ', "%{$agentTown}%");
            $where .= $this->_db->quoteInto('address3 LIKE ? OR ', "%{$agentTown}%");
            $where .= $this->_db->quoteInto('address4 LIKE ? ) AND ', "%{$agentTown}%");
            // TODO: Fix filter against rubbish legacy way of marking closed accounts - currently by their name(!)
            $where .= $this->_db->quoteInto("name NOT LIKE '%Closed%' AND name NOT LIKE '%Close Account%' ", '');
        } else {
            return false;
        }
        // Filter against new AGENTCANCELLATION table
        $where .= $this->_db->quoteInto('AND NOT EXISTS (SELECT 1 FROM AGENTCANCELLATION AS ac WHERE na.agentschemeno = ac.AGENTSCHEMENUMBER)', '');
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('na' => $this->_name))
            ->where($where)
            ->order('name ASC')
            ->limit(50);

        $agents = array();

        $result = $this->fetchAll($select);
        foreach ($result as $row) {
            if (($row['agentschemeno'] != '') && ($row['agentschemeno'] != '0')) {
                $address = '';
                for ($i = 1; $i < 4; $i++) {
                    $address .= (trim($row["address{$i}"]) != '') ? ($row["address{$i}"] . ', ') : '';
                }
                $address .= $row['postcode'];
                $agents[] = array(
                    'asn'       => $row['agentschemeno'],
                    'name'      => $row['name'],
                    'address'   => $address
                );
            }
        }

        return $agents;
    }


    /**
     * Returns the agent town.
     *
     * @param mixed $agentSchemeNumber
     * The unique agent identifier.
     *
     * @return mixed
     * Returns the town name, or null if the agent cannot be found.
     */
    public function getAgentTown($agentSchemeNumber) {

        $select = $this->select()
            ->from($this->_name, array('address2', 'address3', 'address4'))
            ->where('agentschemeno = ?', $agentSchemeNumber);
        $row = $this->fetchRow($select);

        if(empty($row)) {

            $returnVal = null;
        }
        else {

            if(empty($row['address3'])) {

                if(empty($row['address4'])) {

                    $returnVal = $row['address2'];
                }
                else {

                    $returnVal = $row['address4'];
                }
            }
            else {

                $returnVal = $row['address3'];
            }
        }

        return $returnVal;
    }

    /**
    * Get the Rate Set id of the agent
    *
    * @param int $agentSchemeNumber
    * @return null|int The Rate set ID of the Agent
     */
    function getRatesetID($agentSchemeNumber)
    {
        $select = $this->select()
            ->from($this->_name,array('agentsRateID'))
            ->where('agentschemeno = ?', $agentSchemeNumber);
        $row = $this->fetchRow($select);
        if ($row) {
            return $row->agentsRateID;
        }
        return null;
    }

    /**
     * Get agent-level external news visibility.
     *
     * @param mixed $agentSchemeNumber
     *
     * @return bool True for 'on' and false for 'off'.
     */
    public function getAgentEnableExternalNews($agentSchemeNumber) {

        $select = $this->select()
            ->from($this->_name, array('enableExternalNews'))
            ->where('agentschemeno = ?', $agentSchemeNumber);
        $row = $this->fetchRow($select);

        if (empty($row)) {
            $returnVal = null;
        } else {
            $returnVal = ($row['enableExternalNews'] == 'yes') ? true : false;
        }

        return $returnVal;
    }

    /**
     * Set agent-level external news visibility.
     *
     * @param bool $switch True for 'on' and false for 'off'.
     * @param mixed $agentSchemeNumber
     *
     * @return void
     */
    public function setAgentEnableExternalNews($switch, $agentSchemeNumber) {

        $switch = ($switch === false) ? 'no' : 'yes';
        $where = $this->getAdapter()->quoteInto('agentschemeno = ?', $agentSchemeNumber);
        $update = $this->update(
            array(
                'enableExternalNews' => $switch
            ),
            $where
        );
    }

    /**
     * Takes a badly formatted (all legacy addresses) from the newagents table
     * and puts it neatly into a Model_Core_Address.
     *
     * @param array $legacyAddressArray Address parts from legacy newagents table.
     *
     * @return Model_Core_Address
     */
    private function _legacyAddressToObject($legacyAddressArray) {
        // Address splitting out from legacy data to squeeze it into domain object.

        // Some example actual addresses this has to work with:
        //   array('53, Elphinstone Road', '', 'LONDON', '', 'E17 5EZ')
        //   array('P.O. Box 80', 'Princes Risborough', 'Buckinghamshire', '', 'HP27 0WA')
        //   array('Becor House, Green Lane', '', 'LINCOLN', '', 'LN6 7DL')
        //   array('177-179 Hoe Street', 'Walthamstow', 'London', '', 'E17 3AP')
        //   array('2/1, 14, Norval Street', '', 'GLASGOW', '', 'g117rx')
        //   array('Unit 222, Stratford Workshops, Burford Road', '', 'LONDON', '', 'e15 2sp')
        //   array('Suite 43', '1 Angel Lane', 'LONDON', '', 'E15 1BL')
        // These need to be shoe-horned into these object properties:
        //   id, flatNumber, houseName, houseNumber, addressLine1,
        //   addressLine2, town, county, postCode, country, isOverseasAddress
        $address = new Model_Core_Address();

        // The last address line is always assumed to be a postcode.  May as
        //   well take it out of the array now.
        $address->postCode = array_pop($legacyAddressArray);

        // If line 1 contains (but doesn't end with) a numeric or a comma, it
        //   is probably actually a house name or number and a street name
        //   combined, eg (actual examples):
        //   '53, Elphinstone Road'
        //   '9 Oak Tree Lane'
        //   'Becor House, Green Lane'
        // Problems for splitting are values like (actual examples):
        //   'Suite 43'
        //   '5 Charter House, Lord Montgomery Way'
        //   '2/1, 14, Norval Street'
        //   '278A Battersea Park Road'
        //   '4th Floor'
        $line1 = trim($legacyAddressArray[0]);
        if (preg_match('/^.*[\d,].*[^\d,]$/', $line1) > 0) {
            // Line 1 contains (but doesn't end with) a numeric or a comma
            // Can we preferentially split on a (the last) comma?
            if (strpos($line1, ',') !== false) {
                // Split on and remove last comma
                preg_match('/^(.*),([^,]+)$/', $line1, $matches);
                $houseNumberName = trim($matches[1]);
                $streetName = trim($matches[2]);
            } else {
                // Check if last numeric is followed by an ordinal indicator ('4th')
                if (preg_match('/^(.*\d)(st|nd|rd|th)( [^\s]+)(.*)$/i', $line1) > 0) {
                    // It is, so split after the next full "word" ('4th Floor')
                    preg_match('/^(.*\d)(st|nd|rd|th)( [^\s]+)(.*)$/i', $line1, $matches);
                    $houseNumberName = trim($matches[1] . $matches[2] . $matches[3]);
                    $streetName = trim($matches[4]);
                } else {
                    // It isn't, split on and keep last numeric, and any letters directly attached to it ('278A')
                    preg_match('/^(.*\d[a-z]*)([^\d]+)$/i', $line1, $matches);
                    $houseNumberName = trim($matches[1]);
                    $streetName = trim($matches[2]);
                }
            }
            // Naive check to see if we have a flat number
            if (stripos($houseNumberName, 'flat') !== false) {
                $address->flatNumber = $houseNumberName;
            } else {
                // Check to see if this is a name or a number - here number includes '1-3', '278A', etc
                if (preg_match('/\d/', $houseNumberName) > 0) {
                    $address->houseNumber = $houseNumberName;
                } else {
                    $address->houseName = $houseNumberName;
                }
            }
            // Street name can go in place
            $address->addressLine1 = $streetName;
        } else {
            // Line 1 not splittable, should be just a flat number, house number or house name
            // Naive check to see if we have a flat number
            if (stripos($line1, 'flat') !== false) {
                $address->flatNumber = $line1;
            } else {
                // Check to see if this is a name or a number
                if (is_numeric($line1)) {
                    $address->houseNumber = $line1;
                } else {
                    $address->houseName = $line1;
                }
            }
        }

        // Other parts can go in place
        $address->addressLine2 = trim($legacyAddressArray[1]);
        $address->town = trim($legacyAddressArray[2]);
        $address->county = trim($legacyAddressArray[3]);

        // This is for addresses like array('Unit 222, Stratford Workshops, Burford Road', '', 'LONDON', '', 'e15 2sp')
        // Final cleanup rule:
        // If houseNumber contains a comma
        // AND anything after it is non-numeric
        // AND line 2 is empty,
        // then move anything in line 1 to line 2 and split houseNumber on the comma into houseNumber and line 1
        if ((preg_match('/^(.*),([^\d,]+)$/', $address->houseNumber, $matches) > 0) && ($address->addressLine2 == '')) {
            $address->addressLine2 = $address->addressLine1;
            $address->houseNumber = trim($matches[1]);
            $address->addressLine1 = trim($matches[2]);
        }

        return $address;
    }

    /**
     * Set the uploaded and re-sized image file names for the Connect logo
     *
     * @param string $privateName (uploaded file name)
     * @param string $publicName
     * @param int $agentSchemeNumber
     */
    public function setConnectLogo($privateName, $publicName, $agentSchemeNumber) {

        $where = $this->getAdapter()->quoteInto('agentschemeno = ?', $agentSchemeNumber);
        $update = $this->update(
            array(
                'logo_original' => $privateName,
                'logo' => $publicName
            ),
            $where
        );
    }

    /**
     * Delete the uploaded and re-sized image file names for the Connect logo
     *
     * @param int $agentSchemeNumber
     */
    public function deleteConnectLogo($agentSchemeNumber) {

        $where = $this->getAdapter()->quoteInto('agentschemeno = ?', $agentSchemeNumber);
        $update = $this->update(
            array(
                'logo_original' => new Zend_Db_Expr('NULL'),
                'logo' => new Zend_Db_Expr('NULL')
            ),
            $where
        );
    }

    /**
     * Set the uploaded, re-sized and remote SFTP image file names for the Document logo
     * Also set the transfer-needed flag
     *
     * @param string $privateName (uploaded file name)
     * @param string $publicName
     * @param string $sftpName
     * @param int $agentSchemeNumber
     */
    public function setDocumentLogo($privateName, $publicName, $sftpName, $agentSchemeNumber) {

        $where = $this->getAdapter()->quoteInto('agentschemeno = ?', $agentSchemeNumber);
        $update = $this->update(
            array(
                'document_logo_original' => $privateName,
                'document_logo' => $publicName,
                'document_logo_sftp' => $sftpName,
                'is_logo_transfer_needed' => true
            ),
            $where
        );
    }

    /**
     * Delete the uploaded, re-sized and remote SFTP image file names for the Document logo
     *
     * @param $agentSchemeNumber
     */
    public function deleteDocumentLogo($agentSchemeNumber) {

        $where = $this->getAdapter()->quoteInto('agentschemeno = ?', $agentSchemeNumber);
        $update = $this->update(
            array(
                'document_logo_original' => new Zend_Db_Expr('NULL'),
                'document_logo' => new Zend_Db_Expr('NULL'),
                'document_logo_sftp' => new Zend_Db_Expr('NULL'),
                'is_logo_transfer_needed' => false
            ),
            $where
        );
    }

    /**
     * Gets an associated array containing a list of the Document logos that still require transferring
     *
     * @return array $logoList
     */
    public function getAllDocumentLogoTransfers()
    {
        $select = $this->select()
            ->from($this->_name)
            ->where('is_logo_transfer_needed = ?', true);

        $allRows = $this->fetchAll($select);
        $logoList = array();
        foreach ($allRows as $logoRow) {
            $logoList[ $logoRow->document_logo_sftp ] = $logoRow->document_logo_original;
        }

        return $logoList;
    }

    /**
     * Updates the database with the list of Document logos which have been successfully transferred
     *
     * @param array $transferList
     * @return bool
     */
    public function updateDocumentLogoTransfers($transferList)
    {
        $where = array();
        foreach ($transferList as $sftpName) {
            $where[] = $this->getAdapter()->quoteInto('document_logo_sftp = ?', $sftpName);
        }
        $update = $this->update(
            array(
                'is_logo_transfer_needed' => false
            ),
            $where
        );

        if (count($transferList) != count($update)) {
            return false;
        } else {
            return true;
        }
    }
}