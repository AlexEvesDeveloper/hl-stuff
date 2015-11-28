<?php

namespace Iris\Utility\AddressFinder;

/**
 * Interface AddressFinderInterface
 *
 * @package Iris\Utility\AddressFinder
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface AddressFinderInterface
{
    /**
     * Find addresses based on full postcode
     *
     * @param string $postcode
     * @return array of Model\Address
     */
    public function find($postcode);
}