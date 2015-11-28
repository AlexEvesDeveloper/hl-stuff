<?php

namespace Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model;

use Barbondev\IRISSDK\Common\Model\AbstractResponseModel;
use Guzzle\Service\Command\OperationCommand;
use Guzzle\Common\Collection;

/**
 * Class ReportNotification
 *
 * @package Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ReportNotification extends AbstractResponseModel
{
    /**
     * @var string
     */
    private $referencingApplicationUuId;

    /**
     * @var string
     */
    private $applicantName;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $propertyAddress;

    /**
     * @var bool
     */
    private $finalReport;

    /**
     * {@inheritdoc}
     */
    public static function fromCommand(OperationCommand $command)
    {
        $data = $command->getResponse()->json();

        // Collection of notifications
        if (self::isResponseDataIndexedArray($data)) {

            $notifications = new Collection();

            foreach ($data as $key => $documentData) {
                $notifications->add($key, self::hydrateModelProperties(
                    new self(),
                    $documentData,
                    array(
                        'applicationId' => 'referencingApplicationUuId',
                    )
                ));
            }

            return $notifications;
        }

        // Single notification
        else {
            return self::hydrateModelProperties(
                new self(),
                $data,
                array(
                    'applicationId' => 'referencingApplicationUuId',
                )
            );
        }
    }

    /**
     * Set applicantName
     *
     * @param string $applicantName
     * @return $this
     */
    public function setApplicantName($applicantName)
    {
        $this->applicantName = $applicantName;
        return $this;
    }

    /**
     * Get applicantName
     *
     * @return string
     */
    public function getApplicantName()
    {
        return $this->applicantName;
    }

    /**
     * Set createdAt
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set finalReport
     *
     * @param boolean $finalReport
     * @return $this
     */
    public function setFinalReport($finalReport)
    {
        $this->finalReport = $finalReport;
        return $this;
    }

    /**
     * Get finalReport
     *
     * @return boolean
     */
    public function getFinalReport()
    {
        return $this->finalReport;
    }

    /**
     * Set propertyAddress
     *
     * @param string $propertyAddress
     * @return $this
     */
    public function setPropertyAddress($propertyAddress)
    {
        $this->propertyAddress = $propertyAddress;
        return $this;
    }

    /**
     * Get propertyAddress
     *
     * @return string
     */
    public function getPropertyAddress()
    {
        return $this->propertyAddress;
    }

    /**
     * Set referencingApplicationUuId
     *
     * @param string $referencingApplicationUuId
     * @return $this
     */
    public function setReferencingApplicationUuId($referencingApplicationUuId)
    {
        $this->referencingApplicationUuId = $referencingApplicationUuId;
        return $this;
    }

    /**
     * Get referencingApplicationUuId
     *
     * @return string
     */
    public function getReferencingApplicationUuId()
    {
        return $this->referencingApplicationUuId;
    }
}