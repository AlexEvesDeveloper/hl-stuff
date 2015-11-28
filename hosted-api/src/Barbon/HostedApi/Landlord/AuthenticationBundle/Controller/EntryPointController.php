<?php

namespace Barbon\HostedApi\Landlord\AuthenticationBundle\Controller;

use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Common\Cache\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Barbon\HostedApi\AppBundle\Exception\InvalidMacException;

/**
 * The entry point for landlord authentication, either log in or register
 *
 * @Route(service="barbon.hosted_api.landlord.authentication.controller.entry_point_controller")
 */
class EntryPointController extends Controller
{

    /**
     * @var IrisEntityManager
     */
    private $irisEntityManager;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * Constructor
     *
     * @param IrisEntityManager $irisEntityManager
     * @param Cache $cache
     */
    public function __construct(IrisEntityManager $irisEntityManager, Cache $cache)
    {
        $this->irisEntityManager = $irisEntityManager;
        $this->cache = $cache;
    }

    /**
     * @Route("/entry-point/{mac}", defaults={"mac" = null})
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function indexAction(Request $request, $mac)
    {
        // Attempting to do anything here as a logged in user will fail. Set the current user token to null to log user out.
        $this->get('security.token_storage')->setToken(null);

        if ( ! $mac) {
            if (! $request->getSession()->get('auth-data')) {
                // No MAC code, nothing in the session, so we can't help - return to front page.
                return $this->redirectToRoute('barbon_hostedapi_app_index_index');
            } 
        } else {
            $cacheKey = sprintf('mac-%s', $mac);
        
            // If MAC isn't found in the cache, it's already been processed - redirect back to this route without the MAC, and try again.
            if ( ! $this->cache->contains($cacheKey)) {
                return $this->redirectToRoute('barbon_hostedapi_landlord_authentication_entrypoint_index');
            }           
 
            // store data to session and empty the cache
            $authData = unserialize($this->cache->fetch($cacheKey));
            $request->getSession()->set('auth-data', $authData);
            $this->cache->delete($cacheKey);            
        }

        // Decide which tab should start as visible, so that is a registration attempt is in progress it re-shows that tab.
        $selectedTab = $request->query->get('action') ?: 'register';
        if ($request->isMethod(Request::METHOD_POST)) {
            if ($request->request->has('direct_landlord')) {
                $selectedTab = 'register';
            }
        }

        return array(
            'selectedTab' => $selectedTab
        );
    }
}
