<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Lookup;

use Barbon\HostedApi\AppBundle\Form\Common\Model\LookupCollection;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Doctrine\Common\Cache\Cache;

class IrisLookupContainer
{
    /**
     * @var LookupCollection
     */
    private $lookupCollection;

    /**
     * @var IrisEntityManager
     */
    private $entityManager;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * Constructor
     *
     * @param IrisEntityManager $entityManager
     * @param Cache $cache
     */
    public function __construct(IrisEntityManager $entityManager, Cache $cache)
    {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }

    /**
     * Get lookup collection model
     *
     * @return LookupCollection
     */
    public function getCollection()
    {
        if (null === $this->lookupCollection) {
            $this->lookupCollection = $this->cache->fetch('LookupCollection');

            if ( ! $this->lookupCollection) {
                $this->lookupCollection = $this->entityManager->find(new LookupCollection(), array());
                $this->cache->save('LookupCollection', $this->lookupCollection);
            }
        }

        return $this->lookupCollection;
    }
}
