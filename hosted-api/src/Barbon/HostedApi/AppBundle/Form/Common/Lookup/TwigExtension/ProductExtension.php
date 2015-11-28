<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Lookup\TwigExtension;

use Barbon\HostedApi\AppBundle\Form\Common\Model\Product;
use Barbon\HostedApi\AppBundle\Form\Common\Model\ProductCollection;
use Twig_SimpleFilter;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;

final class ProductExtension extends \Twig_Extension
{
    /**
     * @var IrisEntityManager
     */
    private $irisEntityManager;

    /**
     * @param IrisEntityManager $irisEntityManager
     */
    public function __construct(IrisEntityManager $irisEntityManager)
    {
        $this->irisEntityManager = $irisEntityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'iris_product';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('iris_product_label', array($this, 'lookupLabel')),
        );
    }

    /**
     * @param $id
     * @param $rentGuaranteeOfferingType
     * @param $propertyLettingType
     * @return mixed
     * @throws \Exception
     */
    public function lookupLabel($id, $rentGuaranteeOfferingType, $propertyLettingType)
    {
        $products = $this->irisEntityManager->find(new ProductCollection(), array(
            'rentGuaranteeOfferingType' => $rentGuaranteeOfferingType,
            'propertyLettingType' => $propertyLettingType
        ));

        // Search the given product ID within the collection of Products retrieved from IRIS.
        foreach ($products as $product) {
            if ($product->getProductId() == $id) {
                return $product->getName();
            }
        }

        // Error if our loop couldn't find a match.
        throw new \Exception(sprintf('Unable to find a valid product name for product ID: %d', $id));
    }
}