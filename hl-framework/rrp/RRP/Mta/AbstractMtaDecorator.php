<?php

namespace RRP\Mta;

use RRP\DependencyInjection\LegacyContainer;

/**
 * Class AbstractMtaDecorator
 *
 * @package RRP\Mta
 * @author April Portus <april.portus@barbon.com>
 */
abstract class AbstractMtaDecorator
{
    /**
     * Containerised identifier for the Rent Recovery Plus data-source
     */
    const LEGACY_RENT_RECOVERY_PLUS_DATASOURCE = 'rrp.legacy.datasource.rrp_mta';

    /**
     * @var \RRP\DependencyInjection\LegacyContainer
     */
    protected $container;

    /**
     * @var \Datasource_Insurance_MTA
     */
    protected $legacyMta;

    /**
     * @var \Datasource_Insurance_RentRecoveryPlus_RentRecoveryPlusMTA
     */
    protected $rrpMta;

    /**
     * @var object
     */
    protected $legacyMtaData;

    /**
     * @var object
     */
    protected $rrpMtaData;

    /**
     * Constructor
     */
    public function __construct($mtaDatasourceIdentifier)
    {
        $this->container = new LegacyContainer();
        $this->legacyMta = $this->container->get($mtaDatasourceIdentifier);
        $this->rrpMta = $this->container->get(self::LEGACY_RENT_RECOVERY_PLUS_DATASOURCE);
        $this->legacyMtaData = $this->container->get(str_replace('.datasource.', '.', $mtaDatasourceIdentifier));
        $this->rrpMtaData = $this->container->get(str_replace('.datasource.', '.', self::LEGACY_RENT_RECOVERY_PLUS_DATASOURCE));
    }

    /**
     * Gets the Legacy Container
     *
     * @return LegacyContainer
     */
    public function getContainer()
    {
        return $this->container;
    }

}