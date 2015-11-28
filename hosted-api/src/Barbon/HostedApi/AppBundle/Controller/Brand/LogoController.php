<?php

namespace Barbon\HostedApi\AppBundle\Controller\Brand;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Barbon\HostedApi\AppBundle\Form\Common\Model\SystemBrandLogo;

/**
 * Class LogoController
 *
 * @Route(service="barbon.hosted_api.app.controller.brand.logo_controller") 
 *
 * @package Barbon\HostedApi\AppBundle\Brand\Controller
 *
 * @author Paul Swift <paul.swift@barbon.com>
 */
final class LogoController extends AbstractBrandController
{
    /**
     * System brand logo
     *
     * @Route("/logo")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function indexAction()
    {
        $contentType = 'image/png';

        try {
            // Get brand logo
            $systemBrandLogo = $this->systemBrandService->getSystemBrandLogo();

            // Get content type
            $contentType = $systemBrandLogo->getContentType();

            // Get binary from Guzzle stream
            /** @var \GuzzleHttp\Stream\Stream $systemLogo */
            $systemLogo = $systemBrandLogo->getLogo();

            // Get stream contents
            $image = $systemLogo->getContents();
        }
        catch (\Exception $e) {
            // Fret not if no binary image could be found or de-streamed, we'll use an empty image

            // Create a 1-pixel PNG with a transparent colour in its palette
            $png = imagecreate(1, 1);
            $colour = imagecolorallocatealpha($png, 255, 255, 255, 127);

            // Enable output buffering
            ob_start();

            // Output PNG into buffer
            imagepng($png);

            // Capture the output
            $image = ob_get_contents();

            // Clear the output buffer
            ob_end_clean();
        }

        // Output image
        $this->response->headers->set('Content-Type', $contentType);
        $this->response->setContent($image);

        // todo: Possibly filter the output with Assetic
        return $this->response;
    }
}