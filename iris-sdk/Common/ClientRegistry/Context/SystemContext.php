<?php

namespace Barbondev\IRISSDK\Common\ClientRegistry\Context;

/**
 * Class SystemContext
 *
 * @package Barbondev\IRISSDK\Common\ClientRegistry\Context
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Barbondev\IRISSDK\System\Agent\SystemAgentClient getAgentClient()
 * @method \Barbondev\IRISSDK\System\Landlord\SystemLandlordClient getLandlordClient()
 * @method \Barbondev\IRISSDK\SystemApplication\SystemApplication\SystemApplicationClient getSystemApplicationClient()
 * @method \Barbondev\IRISSDK\SystemApplication\Tat\TatClient getTatClient()
 * @method \Barbondev\IRISSDK\Utility\AddressFinder\AddressFinderClient getAddressFinderClient()
 * @method \Barbondev\IRISSDK\Utility\Document\DocumentClient getDocumentClient()
 * @method \Barbondev\IRISSDK\Utility\RentAffordability\RentAffordabilityClient getRentAffordabilityClient()
 * @method \Barbondev\IRISSDK\System\Lookup\LookupClient getLookupClient()
 */
class SystemContext extends AbstractContext
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'system';
    }

    /**
     * {@inheritdoc}
     */
    protected function initialise()
    {
        $this
            ->addClient('agent', function (array $parameters) {
                return \Barbondev\IRISSDK\System\Agent\SystemAgentClient::factory($parameters);
            })
            ->addClient('landlord', function (array $parameters) {
                return \Barbondev\IRISSDK\System\Landlord\SystemLandlordClient::factory($parameters);
            })
            ->addClient('systemApplication', function (array $parameters) {
                return \Barbondev\IRISSDK\SystemApplication\SystemApplication\SystemApplicationClient::factory($parameters);
            })
            ->addClient('tat', function (array $parameters) {
                return \Barbondev\IRISSDK\SystemApplication\Tat\TatClient::factory($parameters);
            })
            ->addClient('addressFinder', function (array $parameters) {
                return \Barbondev\IRISSDK\Utility\AddressFinder\AddressFinderClient::factory($parameters);
            })
            ->addClient('document', function (array $parameters) {
                return \Barbondev\IRISSDK\Utility\Document\DocumentClient::factory($parameters);
            })
            ->addClient('rentAffordability', function (array $parameters) {
                return \Barbondev\IRISSDK\Utility\RentAffordability\RentAffordabilityClient::factory($parameters);
            })
            ->addClient('lookup', function (array $parameters) {
                return \Barbondev\IRISSDK\System\Lookup\LookupClient::factory($parameters);
            })
        ;
    }
}