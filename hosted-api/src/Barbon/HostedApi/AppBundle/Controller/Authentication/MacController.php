<?php

namespace Barbon\HostedApi\AppBundle\Controller\Authentication;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Cache\Cache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Barbon\HostedApi\AppBundle\Service\Authentication\MacManager;

/**
 * Creates a handoff URL, with an appended MAC, to return to the client
 *
 * @Route("/mac", service="barbon.hosted_api.app.controller.authentication.mac_controller")
 */
class MacController extends Controller
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var MacManager
     */
    private $macManager;

    const ENTRY_POINT_URL = 'authentication/entry-point';

    /**
     * Constructor
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache, MacManager $macManager)
    {
        $this->cache = $cache;
        $this->macManager = $macManager;
    }

    /**
     * @Route("/{userType}")
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request, $userType)
    {
        // Generate a MAC value
        $mac = $this->macManager->generate()->getMac();

        // Cache the posted data using the MAC as its reference
        $this->cachePostedData($request, $mac);

        // Construct the response
        $response = array(
            'url' => $this->getHandoffUrl($request, $userType, $mac)
        );

        // Return a json encoded handoff url
        return new Response(json_encode($response, JSON_UNESCAPED_SLASHES));
    }

    /**
     *  Cache the posted data for retrieval at later time
     *
     * @param Request $request
     * @param string $cacheKey
     *
     * @return void
     */
    private function cachePostedData(Request $request, $cacheKey)
    {
        $cacheKey = sprintf('mac-%s', $cacheKey);
        
        $this->cache->save($cacheKey, serialize($request->request), 3600);
    }

    /**
     *  Construct a formatted handoff URL
     *
     * @param Request $request
     * @param string $userType
     * @param string $cacheKey
     *
     * @return string
     */
    private function getHandoffUrl(Request $request, $userType, $cacheKey)
    {
        // http://www.example.com/(app.php|app_dev.php)/(landlord|agent)/authentication/entry-point/random-mac-value
        return sprintf('%s://%s%s/%s/%s/%s',
            $request->getScheme(),
            $request->getHost(),
            $request->getBaseUrl(),
            $userType,
            self::ENTRY_POINT_URL,
            $cacheKey
        );
    }
}
