<?php

use Barbondev\IRISSDK\Common\Exception\DefaultException;

/**
 * Class Connect_View_Helper_ReferenceSummaryUrlResolver
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Connect_View_Helper_ReferenceSummaryUrlResolver extends Zend_View_Helper_Abstract
{
    /**
     * @var array Mapping between reference number and IRIS application UUID
     */
    private static $referenceNumberToUuId = array();

    /**
     * Builds the URL to reach summary page for HRT
     * or IRIS references. Returns the URL on success and NULL on fail
     *
     * @param string $referenceNumber
     * @return string|null
     *
     * @todo Implement caching to improve application UUID lookup performance
     */
    public function getReferenceSummaryUrl($referenceNumber)
    {
        // If this is not an IRIS reference number (e.g. HRT)
        if (!preg_match('/^HLT\d+$/', $referenceNumber)) {
            return $this->buildHrtSummaryUrl($referenceNumber);
        }

        if (isset(self::$referenceNumberToUuId[$referenceNumber])) {
            return $this->buildIrisSummaryUrl(self::$referenceNumberToUuId[$referenceNumber]);
        }

        try {
            $applicationResults = $this
                ->getIrisAgentContext()
                ->getReferencingApplicationClient()
                ->findReferencingApplications(array(
                    'referenceNumber' => $referenceNumber,
                    'offset' => 0,
                    'numberOfRecords' => 1,
                ))
            ;
        }
        catch (DefaultException $e) {
            return null;
        }

        $applications = $applicationResults->getRecords();

        if (!count($applications)) {
            return null;
        }

        self::$referenceNumberToUuId[$referenceNumber] = $applications[0]->getReferencingApplicationUuId();

        return $this->buildIrisSummaryUrl(self::$referenceNumberToUuId[$referenceNumber]);
    }

    /**
     * Get IRIS agent context
     *
     * @return \Barbondev\IRISSDK\Common\ClientRegistry\Context\AgentContext
     */
    private function getIrisAgentContext()
    {
        return \Zend_Registry::get('iris_container')
            ->get('iris_sdk_client_registry')
            ->getAgentContext()
        ;
    }

    /**
     * Build IRIS summary URL
     *
     * @param string $applicationUuId
     * @return string
     */
    private function buildIrisSummaryUrl($applicationUuId)
    {
        return sprintf('/iris-referencing/summary?uuid=%s', $applicationUuId);
    }

    /**
     * Build HRT summary URL
     *
     * @param string $referenceNumber
     * @return string
     */
    private function buildHrtSummaryUrl($referenceNumber)
    {
        return sprintf('/referencing/summary?refno=%s', $referenceNumber);
    }
}