<?php

namespace Iris\Referencing\Application;

use Barbondev\IRISSDK\Common\ClientRegistry\Context\ContextInterface;

/**
 * Class ApplicationCounter
 *
 * @package Iris\Referencing\Application
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ApplicationCounter
{
    /**
     * @var ContextInterface
     */
    private $context;

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
     * Get the difference in the number of tenants prescribed
     * and the actual number of applications logged
     *
     * @param string $applicationUuId
     * @param int $numberOfTenants
     * @return int
     */
    public function getApplicantCountDifference($applicationUuId, $numberOfTenants)
    {
        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
        $application = $this
            ->context
            ->getReferencingApplicationClient()
            ->getReferencingApplication(array(
                'referencingApplicationUuId' => $applicationUuId,
            ))
        ;

        $applications = $this
            ->context
            ->getReferencingCaseClient()
            ->getApplications(array(
                'referencingCaseUuId' => $application->getReferencingCaseUuId(),
            ))
        ;

        return ($numberOfTenants - count($applications));
    }
}