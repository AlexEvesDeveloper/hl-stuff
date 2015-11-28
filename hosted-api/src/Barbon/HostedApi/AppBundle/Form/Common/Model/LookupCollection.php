<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Model;

use Barbon\IrisRestClient\Annotation as Iris;

/**
 * @Iris\Entity\LookupCollection
 */
class LookupCollection
{
    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $employmentStatuses;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $residentialStatuses;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $propertyTypes;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $financialRefereeTypes;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $documentCategories;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $rentGuaranteeOfferingTypes;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $propertyLetTypes;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $applicationTypes;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $completionMethods;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $signaturePreferences;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $propertyBuiltInRanges;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $lettingRefereeTypes;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $caseStatuses;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $financialRefereeStatuses;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $employmentType;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $jobType;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $applicationStatuses;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $tatStatuses;

    /**
     * @Iris\Field(class = "Barbon\HostedApi\AppBundle\Form\Common\Model\LookupEntry")
     * @var LookupEntry[]
     */
    private $securityQuestions;


    /**
     * Get employment statuses
     *
     * @return LookupEntry[]
     */
    public function getEmploymentStatuses()
    {
        return $this->employmentStatuses;
    }

    /**
     * Set employment statuses
     *
     * @param LookupEntry[] $employmentStatuses
     * @return $this
     */
    public function setEmploymentStatuses(array $employmentStatuses)
    {
        $this->employmentStatuses = $employmentStatuses;
        return $this;
    }

    /**
     * Get residential statuses
     *
     * @return LookupEntry[]
     */
    public function getResidentialStatuses()
    {
        return $this->residentialStatuses;
    }

    /**
     * Set residential statuses
     *
     * @param LookupEntry[] $residentialStatuses
     * @return $this
     */
    public function setResidentialStatuses(array $residentialStatuses)
    {
        $this->residentialStatuses = $residentialStatuses;
        return $this;
    }

    /**
     * Get property types
     *
     * @return LookupEntry[]
     */
    public function getPropertyTypes()
    {
        return $this->propertyTypes;
    }

    /**
     * Set property types
     *
     * @param LookupEntry[] $propertyTypes
     * @return $this
     */
    public function setPropertyTypes(array $propertyTypes)
    {
        $this->propertyTypes = $propertyTypes;
        return $this;
    }

    /**
     * Get financial referee types
     *
     * @return LookupEntry[]
     */
    public function getFinancialRefereeTypes()
    {
        return $this->financialRefereeTypes;
    }

    /**
     * Set financial referee types
     *
     * @param LookupEntry[] $financialRefereeTypes
     * @return $this
     */
    public function setFinancialRefereeTypes(array $financialRefereeTypes)
    {
        $this->financialRefereeTypes = $financialRefereeTypes;
        return $this;
    }

    /**
     * Get document categories
     *
     * @return LookupEntry[]
     */
    public function getDocumentCategories()
    {
        return $this->documentCategories;
    }

    /**
     * Set document categories
     *
     * @param LookupEntry[] $documentCategories
     * @return $this
     */
    public function setDocumentCategories(array $documentCategories)
    {
        $this->documentCategories = $documentCategories;
        return $this;
    }

    /**
     * Get rent guarantee offfering types
     *
     * @return LookupEntry[]
     */
    public function getRentGuaranteeOfferingTypes()
    {
        return $this->rentGuaranteeOfferingTypes;
    }

    /**
     * Set rent guarantee offerring types
     *
     * @param LookupEntry[] $rentGuaranteeOfferingTypes
     * @return $this
     */
    public function setRentGuaranteeOfferingTypes(array $rentGuaranteeOfferingTypes)
    {
        $this->rentGuaranteeOfferingTypes = $rentGuaranteeOfferingTypes;
        return $this;
    }

    /**
     * Get property let types
     *
     * @return LookupEntry[]
     */
    public function getPropertyLetTypes()
    {
        return $this->propertyLetTypes;
    }

    /**
     * Set property let types
     *
     * @param LookupEntry[] $propertyLetTypes
     * @return $this
     */
    public function setPropertyLetTypes(array $propertyLetTypes)
    {
        $this->propertyLetTypes = $propertyLetTypes;
        return $this;
    }

    /**
     * Get application types
     *
     * @return LookupEntry[]
     */
    public function getApplicationTypes()
    {
        return $this->applicationTypes;
    }

    /**
     * Set application types
     *
     * @param LookupEntry[] $applicationTypes
     * @return $this
     */
    public function setApplicationTypes(array $applicationTypes)
    {
        $this->applicationTypes = $applicationTypes;
        return $this;
    }

    /**
     * Get completion methods
     *
     * @return LookupEntry[]
     */
    public function getCompletionMethods()
    {
        return $this->completionMethods;
    }

    /**
     * Set completion methods
     *
     * @param LookupEntry[] $completionMethods
     * @return $this
     */
    public function setCompletionMethods(array $completionMethods)
    {
        $this->completionMethods = $completionMethods;
        return $this;
    }

    /**
     * Get signature preferences
     *
     * @return LookupEntry[]
     */
    public function getSignaturePreferences()
    {
        return $this->signaturePreferences;
    }

    /**
     * Set signature preferences
     *
     * @param LookupEntry[] $signaturePreferences
     * @return $this
     */
    public function setSignaturePreferences(array $signaturePreferences)
    {
        $this->signaturePreferences = $signaturePreferences;
        return $this;
    }

    /**
     * Get property build in ranges
     *
     * @return LookupEntry[]
     */
    public function getPropertyBuiltInRanges()
    {
        return $this->propertyBuiltInRanges;
    }

    /**
     * Set property built in ranges
     *
     * @param LookupEntry[] $propertyBuiltInRanges
     * @return $this
     */
    public function setPropertyBuiltInRanges(array $propertyBuiltInRanges)
    {
        $this->propertyBuiltInRanges = $propertyBuiltInRanges;
        return $this;
    }

    /**
     * GEt letting referee types
     *
     * @return LookupEntry[]
     */
    public function getLettingRefereeTypes()
    {
        return $this->lettingRefereeTypes;
    }

    /**
     * Set letting referee types
     *
     * @param LookupEntry[] $lettingRefereeTypes
     * @return $this
     */
    public function setLettingRefereeTypes(array $lettingRefereeTypes)
    {
        $this->lettingRefereeTypes = $lettingRefereeTypes;
        return $this;
    }

    /**
     * Get case statuses
     *
     * @return LookupEntry[]
     */
    public function getCaseStatuses()
    {
        return $this->caseStatuses;
    }

    /**
     * Set case statuses
     *
     * @param LookupEntry[] $caseStatuses
     * @return $this
     */
    public function setCaseStatuses(array $caseStatuses)
    {
        $this->caseStatuses = $caseStatuses;
        return $this;
    }

    /**
     * Get financial referee statuses
     *
     * @return LookupEntry[]
     */
    public function getFinancialRefereeStatuses()
    {
        return $this->financialRefereeStatuses;
    }

    /**
     * Set financial referee statuses
     *
     * @param LookupEntry[] $financialRefereeStatuses
     * @return $this
     */
    public function setFinancialRefereeStatuses(array $financialRefereeStatuses)
    {
        $this->financialRefereeStatuses = $financialRefereeStatuses;
        return $this;
    }

    /**
     * Get employment type
     *
     * @return LookupEntry[]
     */
    public function getEmploymentType()
    {
        return $this->employmentType;
    }

    /**
     * Set employment type
     *
     * @param LookupEntry[] $employmentType
     * @return $this
     */
    public function setEmploymentType(array $employmentType)
    {
        $this->employmentType = $employmentType;
        return $this;
    }

    /**
     * Get job type
     *
     * @return LookupEntry[]
     */
    public function getJobType()
    {
        return $this->jobType;
    }

    /**
     * Set job type
     *
     * @param LookupEntry[] $jobType
     * @return $this
     */
    public function setJobType(array $jobType)
    {
        $this->jobType = $jobType;
        return $this;
    }

    /**
     * Get application statuses
     *
     * @return LookupEntry[]
     */
    public function getApplicationStatuses()
    {
        return $this->applicationStatuses;
    }

    /**
     * Set application statuses
     *
     * @param LookupEntry[] $applicationStatuses
     * @return $this
     */
    public function setApplicationStatuses(array $applicationStatuses)
    {
        $this->applicationStatuses = $applicationStatuses;
        return $this;
    }

    /**
     * Get tat statuses
     *
     * @return LookupEntry[]
     */
    public function getTatStatuses()
    {
        return $this->tatStatuses;
    }

    /**
     * Set tat statuses
     *
     * @param LookupEntry[] $tatStatuses
     * @return $this
     */
    public function setTatStatuses(array $tatStatuses)
    {
        $this->tatStatuses = $tatStatuses;
        return $this;
    }

    /**
     * Get security questions
     *
     * @return LookupEntry[]
     */
    public function getSecurityQuestions()
    {
        return $this->securityQuestions;
    }

    /**
     * Set security questions
     *
     * @param LookupEntry[] $securityQuestions
     * @return $this
     */
    public function setSecurityQuestions(array $securityQuestions)
    {
        $this->securityQuestions = $securityQuestions;
        return $this;
    }
}
