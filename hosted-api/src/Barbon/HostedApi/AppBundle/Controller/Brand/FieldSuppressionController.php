<?php

namespace Barbon\HostedApi\AppBundle\Controller\Brand;

use Barbon\HostedApi\AppBundle\Service\Brand\SystemBrand;
use Barbon\HostedApi\SecurityBundle\Model\AbstractUser;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Doctrine\Common\Cache\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Barbon\HostedApi\AppBundle\Form\Common\Model\BrandOptions;

/**
 * Class FieldSuppressionController
 *
 * @Route(service="barbon.hosted_api.app.controller.brand.field_suppression_controller")
 *
 * @package Barbon\HostedApi\AppBundle\Brand\Controller
 */
class FieldSuppressionController extends AbstractBrandController
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

        // Ensure field suppression details don't get cached in TWIG template.
        $this->response->setPrivate();
    }

    /**
     * Integrator field suppression JavaScript config.
     *
     * @Route("/field-suppression")
     * @Method({"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $suppressFields = $this->getVendorSuppressedFields();

        $this->response = $this->render(
            'BarbonHostedApiAppBundle::Brand/FieldSuppression/index.html.twig',
            array(
                'suppressFields' => $suppressFields
            ),
            $this->response
        );

        $this->response->headers->set('Content-Type', 'application/javascript');

        return $this->response;
    }

    /**
     * Gets the vendor's suppressed fields from IRIS, if they're set.
     *
     * @return array
     */
    private function getVendorSuppressedFields()
    {
        $suppressFields = array();

        $options = $this->getSystemBrandOptions();
        if ( ! $options) {
            // No brand options found for this vendor, return default fields.
            return $suppressFields;
        }

        $behaviours = $options->getBehaviours();
        if ( ! $behaviours) {
            // No behaviours found for this vendor, return default fields.
            return $suppressFields;
        }

        $suppressFields = $behaviours->getSuppressFields();

        return $suppressFields;
    }
}