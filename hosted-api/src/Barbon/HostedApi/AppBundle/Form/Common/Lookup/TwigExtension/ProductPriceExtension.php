<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Lookup\TwigExtension;

use Barbon\HostedApi\AppBundle\Form\Common\Model\ProductPrice;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\AbstractReferencingApplication;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingApplication;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingGuarantor;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Twig_SimpleFilter;

final class ProductPriceExtension extends \Twig_Extension
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
        return 'iris_product_price';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('iris_product_price', array($this, 'getPrice')),
        );
    }

    /**
     * @param $productId
     * @param ReferencingCase $case
     * @param AbstractReferencingApplication $application
     * @param int $guarantorSequenceNumber
     * @return int
     */
    public function getPrice($productId, ReferencingCase $case, AbstractReferencingApplication $application, $guarantorSequenceNumber = 0)
    {
        $criteria = array(
            'productId' => $productId,
            'propertyLetType' => $case->getPropertyLetType(),
            'rentGuaranteeOfferingType' => $case->getRentGuaranteeOfferingType(),
            'shareOfRent' => $application->getRentShare(),
            'policyLengthInMonths' => $case->getTenancyTerm(),
            'guarantorSequenceNumber' => $guarantorSequenceNumber,
            'isRenewal' => 0
        );

        $productPrice = $this->irisEntityManager->find(new ProductPrice, $criteria);

        return $productPrice->getGrossPrice();
    }
}