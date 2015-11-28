<?php

namespace Barbon\HostedApi\AppBundle\Controller\Brand;

use Barbon\HostedApi\AppBundle\Service\Brand\SystemBrand;
use Barbon\HostedApi\SecurityBundle\Model\AbstractUser;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Doctrine\Common\Cache\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Barbon\HostedApi\AppBundle\Form\Common\Model\BrandOptions;

/**
 * Class AbstractBrandController
 *
 * @package Barbon\HostedApi\AppBundle\Brand\Controller
 */
abstract class AbstractBrandController extends Controller
{
    /**
     * @var SystemBrand
     */
    protected $systemBrandService;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var IrisEntityManager
     */
    protected $irisEntityManager;

    /**
     * @var int
     */
    protected $httpCacheMaxAgeInSeconds;

    /**
     * Constructor
     *
     * @param SystemBrand $systemBrandService
     * @param int $httpCacheMaxAgeInSeconds
     */
    public function __construct(
        SystemBrand $systemBrandService,
        $httpCacheMaxAgeInSeconds = 3600
    )
    {
        $this->systemBrandService = $systemBrandService;
        $this->irisEntityManager = $this->systemBrandService->irisEntityManager;
        $this->cache = $this->systemBrandService->cache;
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
     * Fetch a system brand object from cache or via REST
     *
     * @return SystemBrand
     * @throws \Exception
     */
    protected function getSystemBrand()
    {
        // Set up system brand service with user token
        $this->systemBrandService->setUserToken($this->getUser());

        return $this->systemBrandService->getSystemBrand();
    }

    /**
     * Get the system brand options object.
     *
     * @return null|BrandOptions
     */
    protected function getSystemBrandOptions()
    {
        // Set up system brand service with user token
        $this->systemBrandService->setUserToken($this->getUser());

        return $this->systemBrandService->getSystemBrandOptions();
    }

    /**
     * Attempt to get the vendor credentials from the user object or the MAC data cache.
     *
     * @return array
     * @throws \Exception
     */
    protected function getVendorCredentials()
    {
        // Set up system brand service with user token
        $this->systemBrandService->setUserToken($this->getUser());

        return $this->systemBrandService->getVendorCredentials();
    }

    /**
     * Sets the request member from the request stack passed in as a call from the container.
     *
     * @param RequestStack $requestStack
     * @internal param RequestStack $request_stack
     */
    public function setRequestFromStack(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }
}