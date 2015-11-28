<?php

namespace Barbon\HostedApi\Landlord\ReferenceBundle\Controller\Lookup;

use Barbon\HostedApi\AppBundle\Form\Common\Model\Address;
use Barbon\HostedApi\AppBundle\Form\Common\Model\Product;
use Barbon\HostedApi\AppBundle\Form\Common\Model\ProductCollection;
use Barbon\HostedApi\AppBundle\Form\Common\Model\ProductPrice;
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
 * Class ProductController
 *
 * @Route("/product", service="barbon.hosted_api.landlord.reference.controller.lookup.product_controller")
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
final class ProductController extends Controller
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
     * Constructor
     *
     * @param IrisEntityManager $irisEntityManager
     */
    public function __construct(
        IrisEntityManager $irisEntityManager
    )
    {
        $this->irisEntityManager = $irisEntityManager;

        // Set up response with public cache parameters
        $this->response = new Response();
    }

    /**
     * Product prices lookup.
     *
     * @Route("/prices")
     * @Method({"GET"})
     * @Template()
     *
     * @param Request $request
     * @return Response
     */
    public function pricesAction(Request $request)
    {
        $pricesData = $this->getProductPrices();

        $pricesDataJson = json_encode($pricesData);

        // Formulate JSON response
        $this->response->headers->set('Content-Type', 'application/json');

        $this->response->setContent($pricesDataJson);

        return $this->response;
    }

    /**
     * Fetch all products prices available to this vendor as an array of product ID => gross price pairs
     * todo: Hardcoded criteria should come from user input data instead.
     *
     * @return array
     */
    protected function getProductPrices()
    {
        $products = $this->getProducts();

        $productPrices = array();

        /** @var Product $product */
        foreach ($products as $product) {
            $criteria = array(
                'productId' => $product->getProductId(),
                'propertyLetType' => 1,//$case->getPropertyLetType(),
                'rentGuaranteeOfferingType' => 4,//$case->getRentGuaranteeOfferingType(),
                'shareOfRent' => 500,//$application->getRentShare(),
                'policyLengthInMonths' => 12,//$case->getTenancyTerm(),
                'guarantorSequenceNumber' => 0,//$guarantorSequenceNumber,
                'isRenewal' => 0
            );

            /** @var ProductPrice $productPrice */
            $productPrice = $this->irisEntityManager->find(new ProductPrice, $criteria);

            $productPrices[$product->getProductId()] = $productPrice->getGrossPrice();
        }

        return $productPrices;
    }

    /**
     * Fetch all products available to this vendor.
     * todo: Hardcoded criteria should come from user input data instead.
     *
     * @return ProductCollection
     */
    protected function getProducts()
    {
        $criteria = array(
            'rentGuaranteeOfferingType' => 1,
            'propertyLettingType' => 1
        );

        $products = $this->irisEntityManager->find(new ProductCollection(), $criteria);

        return $products;
    }
}