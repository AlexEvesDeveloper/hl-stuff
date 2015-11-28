<?php

namespace Barbon\HostedApi\AppBundle\Controller\Brand;

use Barbon\HostedApi\AppBundle\Service\Brand\SystemBrand;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Doctrine\Common\Cache\Cache;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContactDetailsController
 *
 * @Route(service="barbon.hosted_api.app.controller.brand.contact_details_controller") 
 *
 * @package Barbon\HostedApi\AppBundle\Brand\Controller
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
final class ContactDetailsController extends AbstractBrandController
{
    /**
     * Constructor
     *
     * @param SystemBrand $systemBrandService
     * @param int $httpCacheMaxAgeInSeconds
     */
    public function __construct(SystemBrand $systemBrandService, $httpCacheMaxAgeInSeconds = 0)
    {
        parent::__construct($systemBrandService, $httpCacheMaxAgeInSeconds);

        // Ensure contact details don't get cached in TWIG template.
        $this->response->setPrivate();
    }

    /**
     * Integrator brand contact details, rendered in a ESI-compatible non-cached template.  To include in a Twig
     * template:
     * <code>
     * {{ render_esi(url('barbon_hostedapi_app_brand_contactdetails_index')) }}
     * </code>
     *
     * @Route("/contact-details")
     * @Method({"GET"})
     * @Template()
     *
     * @return array
     * @throws \Exception
     */
    public function indexAction()
    {
        $systemBrand = $this->getSystemBrand();

        $this->response = $this->render(
            'BarbonHostedApiAppBundle::Brand/ContactDetails/index.html.twig',
            array(
                'agentBrand' => $systemBrand
            ),
            $this->response
        );

        return $this->response;
    }
}