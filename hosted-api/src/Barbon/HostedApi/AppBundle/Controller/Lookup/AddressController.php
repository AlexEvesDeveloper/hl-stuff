<?php

namespace Barbon\HostedApi\AppBundle\Controller\Lookup;

use Barbon\HostedApi\AppBundle\Form\Common\Model\Address;
use Barbon\HostedApi\AppBundle\Form\Lookup\Model\AddressCollection;
use Barbon\HostedApi\AppBundle\Form\Lookup\Model\AddressLookupCollection;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Doctrine\Common\Cache\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class AddressController
 *
 * @Route("/address", service="barbon.hosted_api.app.controller.lookup.address_controller") 
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
final class AddressController extends Controller
{
    /**
     * @var Response
     */
    private $response;

    /**
     * @var IrisEntityManager
     */
    private $irisEntityManager;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * Constructor
     *
     * @param IrisEntityManager $irisEntityManager
     * @param Cache $cache
     */
    public function __construct(
        IrisEntityManager $irisEntityManager,
        Cache $cache
    )
    {
        $this->irisEntityManager = $irisEntityManager;

        $this->cache = $cache;

        // Set up response with public cache parameters
        $this->response = new Response();
    }

    /**
     * Postcode lookup from address
     *
     * @Route("/postcode/{postcode}", defaults={"postcode" = null})
     * @Method({"GET"})
     * @Template()
     *
     * @param Request $request
     * @return Response
     */
    public function postcodeAction(Request $request)
    {
        // Get postcode from request
        $postcode = $request->get('postcode');

        // todo: Check postcode is in valid format

        // Set cache key
        $cacheKey = 'postcodeLookup-' . $postcode;

        // Check if cache contains address lookup details for this postcode
        if ($addressDataJson = $this->cache->fetch($cacheKey)) {
            // Cache hit, do nothing
        }
        else {
            // Cache miss or stale, do the work of fetching new details
            $addressDataAsPaf = $this->irisEntityManager->find(
                new AddressLookupCollection(),
                array('postcode' => $postcode)
            );

            $addressData = $this->addressLookupCollectionToAddressCollection($addressDataAsPaf);

            $addressDataJson = json_encode($addressData);

            // Cache the results
            $this->cache->save($cacheKey, $addressDataJson);
        }

        // Formulate JSON response
        $this->response->headers->set('Content-Type', 'application/json');

        $this->response->setContent($addressDataJson);

        return $this->response;
    }

    /**
     * Converts a collection of PAF-formatted AddressLookup objects into a collection of Address formatted objects.
     *
     * @param AddressLookupCollection $addressLookupCollection
     * @return AddressCollection
     */
    protected function addressLookupCollectionToAddressCollection(AddressLookupCollection $addressLookupCollection)
    {
        $addressCollection = new AddressCollection();

        foreach ($addressLookupCollection as $addressFinderResult) {

            $street = trim(implode(', ', array(
                $addressFinderResult->getAddress1(),
                $addressFinderResult->getAddress2(),
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
                ->setLocality($addressFinderResult->getAddress4())
                ->setTown($addressFinderResult->getAddress5())
                ->setPostcode($addressFinderResult->getPostcode())
                ->setCountry('GB') // Always GB for now
            ;

            $addressCollection[] = $address;

        }

        return $addressCollection;
    }
}