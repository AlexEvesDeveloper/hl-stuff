<?php

namespace Iris\Utility\AddressFinder;

use Barbondev\IRISSDK\Common\ClientRegistry\Context\SystemContext;
use Iris\Utility\AddressFinder\Model\Address;

/**
 * Class AddressFinder
 *
 * @package Iris\Utility\AddressFinder
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class AddressFinder implements AddressFinderInterface
{
    /**
     * @var SystemContext
     */
    protected $systemContext;

    /**
     * Constructor
     *
     * @param SystemContext $systemContext
     */
    public function __construct(SystemContext $systemContext)
    {
        $this->systemContext = $systemContext;
    }

    /**
     * {@inheritdoc}
     */
    public function find($postcode)
    {
        $addressFinderResults = $this->systemContext->getAddressFinderClient()->findAddress(array(
            'postcode' => trim($postcode),
        ));

        $addresses = array();

        /** @var \Barbondev\IRISSDK\Utility\AddressFinder\Model\PafAddress $addressFinderResult */
        foreach ($addressFinderResults as $addressFinderResult) {

            $street = trim(implode(', ', array(
                $addressFinderResult->getAddressLineOne(),
                $addressFinderResult->getAddressLineTwo(),
            )), ', ');

            $address = new Address();

            $houseName = '';

            if ($addressFinderResult->getOrganisationDepartment()) {
                $houseName .= sprintf('%s, ', $addressFinderResult->getOrganisationDepartment());
            }

            if ($addressFinderResult->getOrganisation()) {
                $houseName .= sprintf('%s, ', $addressFinderResult->getOrganisation());
            }

            if ($addressFinderResult->getBuildingName()) {
                $houseName .= sprintf('%s, ', $addressFinderResult->getBuildingName());
            }

            $houseName = trim(rtrim($houseName, ', '));

            $address
                ->setFlat($addressFinderResult->getSubBuildingName() ?: null)
                ->setHouseName($houseName ?: null)
                ->setHouseNumber($addressFinderResult->getBuildingNumber() ?: null)
                ->setStreet($street)
                ->setLocality($addressFinderResult->getAddressLineFour())
                ->setTown($addressFinderResult->getAddressLineFive())
                ->setPostcode($addressFinderResult->getPostcode())
                ->setCountry('GB') // Always GB for now
            ;

            $addresses[] = $address;

        }

        return $addresses;
    }
}