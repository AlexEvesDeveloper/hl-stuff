<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Lookup;

use Barbon\HostedApi\AppBundle\Form\Common\Model\ProductCollection;
use Barbon\HostedApi\AppBundle\Form\Common\Model\ProductPrice;
use Barbon\HostedApi\AppBundle\Service\Brand\SystemBrand;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Doctrine\Common\Cache\Cache;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;

class Product implements ChoiceListInterface
{
    /**
     * @var IrisEntityManager
     */
    private $entityManager;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var SystemBrand
     */
    protected $systemBrandService;

    /**
     * @var int
     */
    private $rentGuaranteeOfferingType;

    /**
     * @var int
     */
    private $propertyLettingType;

    /**
     * @var string[]
     */
    private $choices = array();

    /**
     * @var string[]
     */
    private $values = array();

    /**
     * @var string[]
     */
    private $labels = array();

    /**
     * @var bool
     */
    private $isInitialised = false;

    /**
     * Constructor
     *
     * @param IrisEntityManager $entityManager
     * @param Cache $cache
     * @param SystemBrand $systemBrandService
     */
    public function __construct(IrisEntityManager $entityManager, Cache $cache, SystemBrand $systemBrandService)
    {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
        $this->systemBrandService = $systemBrandService;
    }

    /**
     * Set rent guarantee offering type
     *
     * @param $rentGuaranteeOfferingType
     * @return $this
     */
    public function setRentGuaranteeOfferingType($rentGuaranteeOfferingType)
    {
        if ( ! is_string($rentGuaranteeOfferingType) || '' == $rentGuaranteeOfferingType || null === $rentGuaranteeOfferingType) {
            return $this;
        }

        $this->isInitialised = ($this->rentGuaranteeOfferingType === $rentGuaranteeOfferingType) ? true : false;
        $this->rentGuaranteeOfferingType = $rentGuaranteeOfferingType;

        return $this;
    }

    /**
     * Set property letting type
     *
     * @param $propertyLettingType
     * @return $this
     */
    public function setPropertyLettingType($propertyLettingType)
    {
        if ( ! is_string($propertyLettingType) || '' == $propertyLettingType || null == $propertyLettingType) {
            return $this;
        }

        $this->isInitialised = ($this->propertyLettingType === $propertyLettingType) ? true : false;
        $this->propertyLettingType = $propertyLettingType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $this->initialiseChoices();
        $products = array();
        foreach ($this->choices as $i => $choice) {
            $products[$this->choices[$i]] = $this->values[$i];
        }

        return $products;
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        $this->initialiseChoices();
        $products = array();
        foreach ($this->values as $i => $choice) {
            $products[$this->values[$i]] = $this->choices[$i];
        }

        return $products;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreferredViews()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getRemainingViews()
    {
        $this->initialiseChoices();
        $views = array();
        foreach ($this->labels as $i => $label) {
            $views[] = new ChoiceView($this->values[$i], $this->values[$i], $label);
        }

        return $views;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoicesForValues(array $values)
    {
        $this->initialiseChoices();
        $choices = array();

        foreach ($values as $i => $givenValue) {
            foreach ($this->values as $j => $value) {
                if ($value === $givenValue) {
                    $choices[$i] = $this->choices[$j];
                    unset($values[$i]);

                    if (0 === count($values)) {
                        break 2;
                    }
                }
            }
        }

        return $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function getValuesForChoices(array $choices)
    {
        $this->initialiseChoices();
        $values = array();

        foreach ($choices as $i => $givenChoice) {
            foreach ($this->choices as $j => $choice) {
                if ($choice === $givenChoice) {
                    $values[$i] = $this->values[$j];
                    unset($choices[$i]);

                    if (0 === count($choices)) {
                        break 2;
                    }
                }
            }
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndicesForChoices(array $choices)
    {
        $this->initialiseChoices();
        $indices = array();

        foreach ($choices as $i => $givenChoice) {
            foreach($this->choices as $j => $choice) {
                if ($choice === $givenChoice) {
                    $indices[$i] = $j;
                    unset($choices[$i]);

                    if (0 === count($choices)) {
                        break 2;
                    }
                }
            }
        }

        return $indices;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndicesForValues(array $values)
    {
        $this->initialiseChoices();
        $indices = array();

        foreach ($values as $i => $givenValue) {
            foreach ($this->values as $j => $value) {
                if ($value === $givenValue) {
                    $indices[$i] = $j;
                    unset($values[$i]);

                    if (0 === count($values)) {
                        break 2;
                    }
                }
            }
        }

        return $indices;
    }

    /**
     * Initialise the product collection from the products service
     *
     * @return ProductCollection|null
     */
    private function initialiseChoices()
    {
        // Fetch a vendor key if possible, to cache against
        try {
            $vendorKey = $this->systemBrandService->getVendorCredentials()['vendorKey'];
        }
        catch (\Exception $e) {
            $vendorKey = 'defaultVendor';
        }

        if (null !== $this->rentGuaranteeOfferingType && null !== $this->propertyLettingType && ! $this->isInitialised) {

            $cacheKey = sprintf(
                'Lookup-Product-Collection-%s-%s-%s',
                $this->rentGuaranteeOfferingType,
                $this->propertyLettingType,
                $vendorKey
            );
            $productCollection = $this->cache->fetch($cacheKey);

            $this->values = array();
            $this->choices = array();

            if ( ! $productCollection) {
                $productCollection = $this->entityManager->find(new ProductCollection(), array(
                    'rentGuaranteeOfferingType' => $this->rentGuaranteeOfferingType,
                    'propertyLettingType' => $this->propertyLettingType,
                ));

                $this->cache->save($cacheKey, $productCollection);
            }

            /** @var \Barbon\HostedApi\AppBundle\Form\Common\Model\Product $product */
            foreach ($productCollection as $product) {
                $this->values[] = (string) $product->getProductId();
                $this->choices[] = (string) $product->getProductId(); //$product->getName();
                $this->labels[] = (string) $product->getName();
            }

            $this->isInitialised = true;
            return $productCollection;
        }
        
        return null;
    }
}
