<?php

namespace Barbondev\IRISSDK\Common\ClientRegistry\Context;

/**
 * Class LandlordContext
 *
 * @package Barbondev\IRISSDK\Common\ClientRegistry\Context
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 *
 * @method \Barbondev\IRISSDK\Landlord\Landlord\LandlordClient getLandlordClient()
 * @method \Barbondev\IRISSDK\IndividualApplication\Lookup\LookupClient getLookupClient()
 * @method \Barbondev\IRISSDK\IndividualApplication\Note\NoteClient getNoteClient()
 * @method \Barbondev\IRISSDK\IndividualApplication\Product\ProductClient getProductClient()
 * @method \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\ReferencingApplicationClient getReferencingApplicationClient()
 * @method \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\ReferencingCaseClient getReferencingCaseClient()
 * @method \Barbondev\IRISSDK\Utility\AddressFinder\AddressFinderClient getAddressFinderClient()
 * @method \Barbondev\IRISSDK\Utility\Document\DocumentClient getDocumentClient()
 * @method \Barbondev\IRISSDK\Utility\RentAffordability\RentAffordabilityClient getRentAffordabilityClient()
 */
class LandlordContext extends AbstractContext
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'landlord';
    }

    /**
     * {@inheritdoc}
     */
    protected function initialise()
    {
        $this
            ->addClient('landlord', function (array $parameters) {
                return \Barbondev\IRISSDK\Landlord\Landlord\LandlordClient::factor($parameters);
            })
            ->addClient('lookup', function (array $parameters) {
                return \Barbondev\IRISSDK\IndividualApplication\Lookup\LookupClient::factory($parameters);
            })
            ->addClient('note', function (array $parameters) {
                return \Barbondev\IRISSDK\IndividualApplication\Note\NoteClient::factory($parameters);
            })
            ->addClient('product', function (array $parameters) {
                return \Barbondev\IRISSDK\IndividualApplication\Product\ProductClient::factory($parameters);
            })
            ->addClient('referencingApplication', function (array $parameters) {
                return \Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\ReferencingApplicationClient::factory($parameters);
            })
            ->addClient('referencingCase', function (array $parameters) {
                return \Barbondev\IRISSDK\IndividualApplication\ReferencingCase\ReferencingCaseClient::factory($parameters);
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
        ;
    }
}