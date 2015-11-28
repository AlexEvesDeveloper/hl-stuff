<?php

namespace Barbon\HostedApi\Landlord\ReferenceBundle\Controller\NewReference;

use Barbon\IrisRestClient\EntityManager\Exception\NotFoundException;
use Barbon\HostedApi\AppBundle\Traits\SessionModelRetrieverTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Barbon\HostedApi\AppBundle\Exception\CaseNotSubmittedException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @Route("/tenancy-agreement")
 */
final class TenancyAgreementController extends Controller
{
    use SessionModelRetrieverTrait;

    /**
     * Success page after reference purchase
     *
     * @Route()
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param Request $request
     * @return array
     * @throws CaseNotSubmittedException
     */
    public function indexAction(Request $request)
    {
        // Fetch vendor-specific terms from system brand
        $systemBrandService = $this->get('barbon.hosted_api.app.service.brand.system_brand');
        $systemBrandService->setUserToken($this->getUser());
        $vendorTerms = $systemBrandService->getSystemBrandOptions()->getDisplayPreferences()->getTerms();

        return array(
            'vendorTerms' => $vendorTerms
        );
    }

    /**
     * Force download of the specified filename
     *
     * @Route("/download/{filename}", requirements={"filename": ".+"})
     *
     * @param $filename
     * @return BinaryFileResponse
     * @throws NotFoundException
     */
    public function downloadAction($filename)
    {
        $filePath = sprintf('%s/../web/media/%s', $this->get('kernel')->getRootDir(), $filename);

        // Check if requested file exists.
        $fs = new Filesystem();
        if ( ! $fs->exists($filePath)) {
            throw $this->createNotFoundException();
        }

        // Prepare BinaryFileResponse
        $response = new BinaryFileResponse($filePath);
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }
}