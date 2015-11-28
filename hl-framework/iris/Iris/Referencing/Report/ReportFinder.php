<?php

namespace Iris\Referencing\Report;

use Barbondev\IRISSDK\Common\ClientRegistry\Context\ContextInterface;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;

/**
 * Class ReportFinder
 *
 * @package Iris\Referencing\Report
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ReportFinder
{
    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     */
    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * Try to find interim report document for application
     *
     * @param string $applicationUuId
     * @return \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\Document|null
     */
    public function getInterimReportDocumentNodeId($applicationUuId)
    {
        return $this->getApplication($applicationUuId)->getInterimReportNodeId();
    }

    /**
     * Try to find final report document for application
     *
     * @param string $applicationUuId
     * @return \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\Document|null
     */
    public function getFinalReportDocumentNodeId($applicationUuId)
    {
        return $this->getApplication($applicationUuId)->getFinalReportNodeId();
    }

    /**
     * Get referencing application
     *
     * @param string $applicationUuId
     * @return ReferencingApplication
     */
    private function getApplication($applicationUuId)
    {
        return $this
            ->context
            ->getReferencingApplicationClient()
            ->getReferencingApplication(array(
                'referencingApplicationUuId' => $applicationUuId,
            ))
        ;
    }
}