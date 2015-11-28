<?php

namespace Iris\Utility\Lookup;

use Barbondev\IRISSDK\IndividualApplication\Lookup\LookupClient;
use Iris\Utility\Lookup\Model\LookupItem;
use Desarrolla2\Cache\Cache;

/**
 * Class Lookup
 *
 * @package Iris\Utility\Lookup
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class Lookup implements LookupInterface
{
    /**
     * @var Lookup
     */
    private static $instance = false;

    /**
     * @var LookupClient
     */
    private $lookupClient;

    /**
     * @var \Barbondev\IRISSDK\IndividualApplication\Lookup\Model\Lookup
     */
    private $lookup;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->lookupClient = \Zend_Registry::get('iris_container')
            ->get('iris_sdk_client_registry')
            ->getSystemContext()
            ->getLookupClient()
        ;

        $this->lookup = $this->lookupClient->getLookup();

        $this->cache = \Zend_Registry::get('iris_container')->get('iris.lookup_cache');
    }

    /**
     * {@inheritdoc}
     */
    public function findById($categoryName, $id)
    {
        $cacheKey = $this->buildCompoundCacheKey(array(
            $categoryName,
            $id,
        ));

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $item = $this->lookup->getCategoryByName($categoryName)->getItemById($id);

        $lookupItem = new LookupItem($item->getId(), $item->getName());

        $this->cache->set($cacheKey, $lookupItem);

        return $lookupItem;
    }

    /**
     * Return a category of lookup items as a choice
     * array for use in form types
     *
     * @param string $categoryName
     * @return array
     */
    public function getCategoryAsChoices($categoryName)
    {
        $cacheKey = $this->buildCompoundCacheKey(array(
            $categoryName,
        ));

        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $choices = array();
        $items = $this->lookup->getCategoryByName($categoryName)->getItems();

        /** @var \Barbondev\IRISSDK\IndividualApplication\Lookup\Model\LookupItem $item */
        foreach ($items as $item) {
            $choices[$item->getId()] = $item->getName();
        }

        $this->cache->set($cacheKey, $choices);

        return $choices;
    }

    /**
     * Build compound cache key from keys
     *
     * @param array $keys
     * @return string
     */
    private function buildCompoundCacheKey(array $keys)
    {
        return sha1(implode('', $keys) . __CLASS__);
    }

    /**
     * Get singleton instance
     *
     * @return Lookup
     */
    public static function getInstance()
    {
        if (false === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}