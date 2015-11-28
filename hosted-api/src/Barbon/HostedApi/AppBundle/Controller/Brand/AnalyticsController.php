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
 * Class AnalyticsController
 *
 * @Route(service="barbon.hosted_api.app.controller.brand.analytics_controller")
 *
 * @package Barbon\HostedApi\AppBundle\Brand\Controller
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
final class AnalyticsController extends AbstractBrandController
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
     * Integrator analytics snippet, rendered in a ESI-compatible non-cached template.  To include in a Twig
     * template:
     * <code>
     * {{ render_esi(url('barbon_hostedapi_app_brand_analytics_index')) }}
     * </code>
     *
     * @Route("/analytics")
     * @Method({"GET"})
     * @Template()
     *
     * @return array
     * @throws \Exception
     */
    public function indexAction()
    {
        $systemBrandOptions = $this->getSystemBrandOptions();

        $template = 'none';
        $analyticsOptions = array();

        try {
            $analytics = $systemBrandOptions->getDisplayPreferences()->getAnalytics();

            if (isset($analytics['google'])) {
                $template = 'google';
                $analyticsOptions = $analytics['google'];
            }
        }
        catch (\Exception $e) { }

        $this->response = $this->render(
            sprintf('BarbonHostedApiAppBundle::Brand/Analytics/%s.html.twig', $template),
            $analyticsOptions,
            $this->response
        );

        return $this->response;
    }
}