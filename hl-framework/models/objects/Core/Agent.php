<?php

/**
 * Represents a letting agent in the system.
 */
class Model_Core_Agent extends Model_Abstract {

    /**
     * The letting agent's name.
     *
     * @var string
     */
    public $name;

    /**
     * The letting agent's unique identifier.
     *
     * @var mixed Value may be an integer or a string.
     */
    public $agentSchemeNumber;

   /**
     * The letting agent's occasionally unique identifier.
     *
     * @var mixed Value may be an integer or a string.
     */
    public $homeLetRef;

    public $agentsRateID;

    public $agentsDealGroupID;

    /**
     * The letting agent's logo for displaying in connect.
     *
     * @var string Identifies the agent's uploaded logo, if any.
     */
    public $logo;

    /**
     * The letting agent's logo for displaying in documents.
     *
     * @var string Identifies the agent's uploaded logo, if any.
     */
    public $documentLogo;

    /**
     * The letting agent's e-mail addresses.
     *
     * @var array Array of Model_Core_Agent_EmailMap objects.
     */
    public $email;

    /**
     * The letting agent's physical addresses and contact numbers.
     *
     * @var array Array of Model_Core_Agent_ContactMap objects.
     */
    public $contact;

    /**
     * The letting agent's contact name
     * @var string
     */
    public $contactname;

        /**
     * The letting agent's account contact name
     * @var string
     */
    public $accountscontactname;

    /**
     * The letting agent's "DAPU" preference.
     *
     * @var string
     */
    public $wantDailyApplicationProgressUpdate;

    /**
     * The letting agent's preference for us marketing to their tenants.
     *
     * @var bool True indicates an agent is fine with us sending promotions to
     * their tenants.
     */
    public $marketingToTenantsOptIn;

    /**
     * The letting agent's status.
     *
     * @var Model_Core_Agent_Status Indicates an agent's status.
     */
    public $status;

    /**
     * The letting agent's "Absolute" type.
     *
     * @var Model_Core_Agent_AbsoluteType Indicates an agent's "Absolute" type.
     */
    public $absoluteType;

    /**
     * The letting agent's external news preference.  In practice the value of
     * this setting overrides that of any associated
     * Model_Core_Agent_User::enableExternalNews
     *
     * @var bool Indicates if an agency allows its users to see external news.
     */
    public $enableExternalNews;

    /**
     * The letting agent's reference price type.
     *
     * @var Model_Core_Agent_ReferencePriceType Indicates an reference price
     * type.
     */
    public $agentReferencePriceType;

    /**
     * The letting agent's start date.
     *
     * @var Zend_Date Indicates an agent's start date.
     */
    public $agentStartDate;

    /**
     * The letting agent's lapse date.
     *
     * @var Zend_Date Indicates an agent's lapse date.
     */
    public $agentLapseDate;

    /**
     * The letting agent's premier start date.
     *
     * @var Zend_Date Indicates an agent's premier start date.
     */
    public $premierStartDate;

    /**
     * The letting agent's premier lapse date.
     *
     * @var Zend_Date Indicates an agent's premier lapse date.
     */
    public $premierLapseDate;

    /**
     * The letting agent's commission rate.
     *
     * @var float Indicates an agent's commission rate.
     */
    public $commissionRate;

    /**
     * The letting agent's new business commission rate.
     *
     * @var float Indicates an agent's new business commission rate.
     */
    public $newBusinessCommissionRate;

    /**
     * The letting agent's salesperson's ID.
     *
     * @var mixed Indicates an agent's salesperson's ID.
     */
    public $salespersonId;

    /**
     * The letting agent's type.
     *
     * @var Model_Core_Agent_Type Indicates an agent's type.
     */
    public $agentType;

    /**
     * The letting agent's premier status.
     *
     * @var Model_Core_Agent_PremierStatus Indicates an agent's premier status.
     */
    public $premierStatus;

    /**
     * Agent town
     *
     * @var string
     */
    public $town;

    /**
     * @var string|null
     */
    public $decommissionInHrtAt;

    /**
     * @var bool
     */
    public $hasProductAvailabilityMapping;

   	// PB - Started creating some getters and setters as I want to migrate this objects from using public variables :)
   	public function getSchemeNumber() { return $this->agentSchemeNumber; }
   	public function getHNumber() { return $this->homeLetRef; }
   	public function getName() { return $this->name; }
}
