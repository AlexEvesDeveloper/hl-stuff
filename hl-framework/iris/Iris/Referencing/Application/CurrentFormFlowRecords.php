<?php

namespace Iris\Referencing\Application;

use Iris\DependencyInjection\ContainerInterface;

/**
 * Class CurrentFormFlowApplication
 *
 * @package Iris\Referencing\Application
 * @author Ashley J. Dawson
 */
class CurrentFormFlowRecords
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get current application from current progressive store
     *
     * @return \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication
     */
    public function getApplication()
    {
        // Test agent store

        /** @var \Iris\ProgressiveStore\ProgressiveStoreInterface $progressiveStore */
        $progressiveStore = $this
            ->container
            ->get('iris.referencing.form_set.progressive_store.agent_progressive_store')
        ;

        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
        $application = $progressiveStore
            ->getPrototypeByClass('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication');

        if ($application && $application->getReferencingApplicationUuId()) {
            return $application;
        }

        // Test agent guarantor store

        /** @var \Iris\ProgressiveStore\ProgressiveStoreInterface $progressiveStore */
        $progressiveStore = $this
            ->container
            ->get('iris.referencing.form_set.progressive_store.agent_guarantor_progressive_store')
        ;

        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
        $application = $progressiveStore
            ->getPrototypeByClass('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication');

        if ($application && $application->getReferencingApplicationUuId()) {
            return $application;
        }

        // Test system store

        /** @var \Iris\ProgressiveStore\ProgressiveStoreInterface $progressiveStore */
        $progressiveStore = $this
            ->container
            ->get('iris.referencing.form_set.progressive_store.system_progressive_store')
        ;

        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
        $application = $progressiveStore
            ->getPrototypeByClass('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication');

        if ($application && $application->getReferencingApplicationUuId()) {
            return $application;
        }

        // Test landlord store

        /** @var \Iris\ProgressiveStore\ProgressiveStoreInterface $progressiveStore */
        $progressiveStore = $this
            ->container
            ->get('iris.referencing.form_set.progressive_store.landlord_progressive_store')
        ;

        /** @var \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication $application */
        $application = $progressiveStore
            ->getPrototypeByClass('Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication');

        if ($application && $application->getReferencingApplicationUuId()) {
            return $application;
        }

        throw new \RuntimeException('Failed to load current application for financial referee form type');
    }
}