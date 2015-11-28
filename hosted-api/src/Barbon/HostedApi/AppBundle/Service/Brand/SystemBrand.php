<?php

namespace Barbon\HostedApi\AppBundle\Service\Brand;

use Barbon\HostedApi\AppBundle\Form\Common\Model\SystemBrand as SysBrand;
use Barbon\HostedApi\AppBundle\Form\Common\Model\SystemBrandLogo as SysBrandLogo;
use Barbon\HostedApi\SecurityBundle\Model\AbstractUser;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Doctrine\Common\Cache\Cache;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Barbon\HostedApi\AppBundle\Form\Common\Model\BrandOptions;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class SystemBrand
 *
 * @package Barbon\HostedApi\AppBundle\Service\Brand
 */
class SystemBrand extends ContainerAware
{
    /**
     * @var AbstractUser|null
     */
    public $userToken;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Response
     */
    public $response;

    /**
     * @var Cache
     */
    public $cache;

    /**
     * @var IrisEntityManager
     */
    public $irisEntityManager;

    /**
     * @var int
     */
    public $httpCacheMaxAgeInSeconds;

    /**
     * Constructor
     *
     * @param IrisEntityManager $irisEntityManager
     * @param Cache $cache
     * @param int $httpCacheMaxAgeInSeconds
     */
    public function __construct(
        IrisEntityManager $irisEntityManager,
        Cache $cache,
        $httpCacheMaxAgeInSeconds = 3600
    )
    {
        $this->irisEntityManager = $irisEntityManager;
        $this->cache = $cache;
        $this->httpCacheMaxAgeInSeconds = $httpCacheMaxAgeInSeconds;

        // Set up response with public cache parameters
        $this->response = new Response();
        $this->response
            ->setPublic()
            ->setMaxAge($httpCacheMaxAgeInSeconds)
            ->setSharedMaxAge($httpCacheMaxAgeInSeconds)
        ;
    }

    /**
     * Fetch a system brand object
     *
     * @return SysBrand
     */
    public function getSystemBrand()
    {
        return $this->getCachableSystemBrandObject(new SysBrand(), 'systemBrand');
    }

    /**
     * Fetch a system brand logo object
     *
     * @return SysBrandLogo
     */
    public function getSystemBrandLogo()
    {
        $vendorCredentials = $this->getVendorCredentials();

        // Temporarily "resume" the vendor's key and secret for the brand details lookup

        $irisClient = $this->irisEntityManager->getClient();

        // Take note of the original auth details to set back to
        $originalAuth = $irisClient->getSubscriber();

        // Switch to using the vendor credentials and look up the system brand object
        $client = $irisClient->resume(
            array(
                'consumerKey' => $vendorCredentials['vendorKey'],
                'consumerSecret' => $vendorCredentials['vendorSecret']
            )
        );

        $systemBrandLogo = $this->irisEntityManager->find(
            new SysBrandLogo()
        );

        // Now resume the original auth
        $irisClient->setSubscriber($originalAuth);

        return $systemBrandLogo;
    }

    /**
     * Fetch a system brand object from cache or via REST, using the vendor key and secret
     *
     * @param mixed $model
     * @param string $cacheKeyRoot
     * @return mixed
     * @throws \Exception
     */
    private function getCachableSystemBrandObject($model, $cacheKeyRoot)
    {
        $vendorCredentials = $this->getVendorCredentials();

        $cacheKey = $cacheKeyRoot . '-' . $vendorCredentials['vendorKey'];

        // Check if cache contains system brand object details for this key
        if ($systemBrandObjectSerialized = $this->cache->fetch($cacheKey)) {
            // Cache hit
            $systemBrandObject = unserialize($systemBrandObjectSerialized);
        }
        else {
            // Cache miss or stale, do the work of fetching new details

            // Temporarily "resume" the vendor's key and secret for the brand details lookup

            $irisClient = $this->irisEntityManager->getClient();

            // Take note of the original auth details to set back to
            $originalAuth = $irisClient->getSubscriber();

            // Switch to using the vendor credentials and look up the system brand object
            $client = $irisClient->resume(
                array(
                    'consumerKey' => $vendorCredentials['vendorKey'],
                    'consumerSecret' => $vendorCredentials['vendorSecret']
                )
            );

            $systemBrandObject = $this->irisEntityManager->find($model);

            // Now resume the original auth
            $irisClient->setSubscriber($originalAuth);

            // Cache the results
            $this->cache->save($cacheKey, serialize($systemBrandObject));
        }

        return $systemBrandObject;
    }

    /**
     * Get the system brand options object.
     *
     * @return null|BrandOptions
     */
    public function getSystemBrandOptions()
    {
        if ( ! $this->getSystemBrand() instanceof SysBrand) {
            return null;
        }

        return $this->getSystemBrand()->getOptions();
    }

    /**
     * Attempt to get the vendor credentials from the user object or the MAC data cache.
     *
     * @return array
     * @throws \Exception
     */
    public function getVendorCredentials()
    {
        $user = $this->userToken;

        // Try getting vendor key and secret from logged-in User object
        if ($user instanceof AbstractUser) {
            $vendorKey = $user->getVendorKey();
            $vendorSecret = $user->getVendorSecret();
        }
        else {
            // User not logged in or fetch is happening outside a firewall that provides a user token, try getting
            // vendor key and secret from session
            /** @var \Symfony\Component\HttpFoundation\ParameterBag $authData */
            $authData = $this->request->getSession()->get('auth-data');

            if ($authData) {
                $vendorKey = $authData->get('systemKey');
                $vendorSecret = $authData->get('systemSecret');
            } else {
                // todo: Better brand exception
                throw new \Exception('No vendor details available');
            }
        }

        return array(
            'vendorKey' => $vendorKey,
            'vendorSecret' => $vendorSecret
        );
    }

    /**
     * Sets the request member from the request stack passed in as a call from the container.
     *
     * @param RequestStack $requestStack
     */
    public function setRequestFromStack(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * Get userToken
     *
     * @return AbstractUser|null
     */
    public function getUserToken()
    {
        return $this->userToken;
    }

    /**
     * Set userToken
     *
     * @param AbstractUser|null $userToken
     * @return $this
     */
    public function setUserToken($userToken)
    {
        $this->userToken = $userToken;
        return $this;
    }
}