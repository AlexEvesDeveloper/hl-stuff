<?php

/**
 * Class Model_Insurance_RentRecoveryPlus_RrpTenantReference
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Model_Insurance_RentRecoveryPlus_RrpTenantReference extends Model_Insurance_RentRecoveryPlus_AbstractResponseModel
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $policyNumber;

    /**
     * @var string
     */
    private $referenceNumber;

    /**
     * @var int
     */
    private $termId;

    /**
     * @var int
     */
    private $mtaId;

    /**
     * @var string
     */
    private $dateCreatedAt;

    /**
     * Hydrate a single application
     *
     * @param array $data
     * @return object
     */
    public static function hydrate($data)
    {
        return self::hydrateModelProperties(
            new self(),
            $data,
            array(),
            array()
        );
    }

    /**
     * Hydrate from the database row names
     *
     * @param $data
     * @return object
     */
    public static function hydrateFromRow($data)
    {
        return self::hydrateModelProperties(
            new self(),
            $data,
            self::getDBNameProperties(),
            array()
        );
    }

    /**
     * Gets a array of the mapping between the database table name and class properties
     *
     * @return array
     */
    private static function getDBNameProperties()
    {
        return array(
            'id'               => 'id',
            'policynumber'     => 'policyNumber',
            'reference_number' => 'referenceNumber',
            'term_id'          => 'termId',
            'mta_id'           => 'mtaId',
            'date_created_at'  => 'dateCreatedAt',
        );
    }

    /**
     * Gets the Id
     *
     * @return int Id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the Id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Gets the policy number
     *
     * @return string
     */
    public function getPolicyNumber()
    {
        return $this->policyNumber;
    }

    /**
     * Sets the policy number
     *
     * @param string $policyNumber
     * @return $this
     */
    public function setPolicyNumber($policyNumber)
    {
        $this->policyNumber = $policyNumber;
        return $this;
    }

    /**
     * Gets the reference number
     *
     * @return string
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * Sets the reference number
     *
     * @param string $referenceNumber
     * @return $this
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;
        return $this;
    }

    /**
     * Gets the date created at string
     *
     * @return string
     */
    public function getDateCreatedAt()
    {
        return $this->dateCreatedAt;
    }

    /**
     * Sets the date created at
     *
     * @param mixed $dateCreatedAt
     * @return $this
     */
    public function setDateCreatedAt($dateCreatedAt)
    {
        $this->dateCreatedAt = $this->transformDate($dateCreatedAt);
        return $this;
    }

    /**
     * Gets the MTA Id
     *
     * @return int mtaId
     */
    public function getMtaId()
    {
        return $this->mtaId;
    }

    /**
     * Sets the MTA Id
     *
     * @param int $mtaId
     * @return $this
     */
    public function setMtaId($mtaId)
    {
        $this->mtaId = $mtaId;
        return $this;
    }

    /**
     * Gets the term Id
     *
     * @return int
     */
    public function getTermId()
    {
        return $this->termId;
    }

    /**
     * Sets the term Id
     *
     * @param int $termId
     * @return $this
     */
    public function setTermId($termId)
    {
        $this->termId = $termId;
        return $this;
    }

}