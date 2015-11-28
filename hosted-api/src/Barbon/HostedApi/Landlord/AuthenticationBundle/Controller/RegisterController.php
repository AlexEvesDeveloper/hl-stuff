<?php

namespace Barbon\HostedApi\Landlord\AuthenticationBundle\Controller;

use Barbon\HostedApi\AppBundle\Form\Common\Model\BrandOptions\DisplayPreferences;
use Barbon\HostedApi\AppBundle\Service\Brand\SystemBrand;
use Barbon\HostedApi\Landlord\AuthenticationBundle\Form\Model\DirectLandlord;
use Barbon\HostedApi\Landlord\AuthenticationBundle\Form\Type\DirectLandlordType;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/register", service="barbon.hosted_api.landlord.authentication.controller.register_controller")
 */
class RegisterController extends Controller
{
    /**
     * @var IrisEntityManager
     */
    private $irisEntityManager;

    /**
     * @var DirectLandlordType
     */
    private $directLandlordType;

    /**
     * @var ApiUserProvider $userProvider
     */
    protected $userProvider;

    /**
     * Constructor
     *
     * @param IrisEntityManager $irisEntityManager
     * @param DirectLandlordType $directLandlordType
     * @param UserProviderInterface $userProvider
     * @param SystemBrand $systemBrandService
     */
    public function __construct(
        IrisEntityManager $irisEntityManager, 
        DirectLandlordType $directLandlordType,
        UserProviderInterface $userProvider,
        SystemBrand $systemBrandService
    )
    {
        $this->irisEntityManager = $irisEntityManager;
        $this->directLandlordType = $directLandlordType;
        $this->userProvider = $userProvider;
        $this->systemBrandService = $systemBrandService;
    }

    /**
     * @Route()
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param Request $request
     * @return array|RedirectResponse
     *
     * @throws RequestException
     */
    public function indexAction(Request $request)
    {
        $form = $this
            ->createForm($this->directLandlordType, new DirectLandlord(), array(
                'action' => $this->generateUrl('barbon_hostedapi_landlord_authentication_entrypoint_index')
            ))
            ->add('register', 'submit', array(
                'label' => 'Register',
                'attr' => array(
                    'class' => 'btn-primary pull-right'
                )
            )
        );

        $masterRequest = $this->container->get('request_stack')->getMasterRequest();

        // Only process if this is the form that's been POSTed
        if ($masterRequest->request->has($form->getName())) {

            $form->handleRequest($masterRequest);

            if ($form->isValid()) {

                $directLandlord = $form->getData();

                $registrationSuccessful = true;

                // Attempt to register direct landlord, handling any errors from IRIS
                try {
                    // Switch to vendor credentials before creating the Landlord
                    // Checking for its existence in the session has already been handled in the EntryPointController
                    $vendorCredentials = $request->getSession()->get('auth-data');
                    $this->irisEntityManager->getClient()->setSystemCredentials($vendorCredentials->get('systemKey'), $vendorCredentials->get('systemSecret'));

                    // Create the Landlord
                    $this->irisEntityManager->persist($directLandlord);
                } catch (RequestException $e) {

                    $registrationSuccessful = false;

                    switch ($e->getCode()) {

                        // Catch missing/invalid details
                        case 400:
                        case 422:
                            $form->addError(new FormError('One or more required details are bad or missing, please contact us if this problem persists.'));
                            break;

                        // Catch duplicate registrations
                        case 409:
                            $form->addError(new FormError('A landlord with this e-mail address already exists, please try logging in or resetting your password instead.'));
                            $form->get('email')->addError(new FormError('E-mail address already exists'));
                            break;

                        // Catch and re-throw server errors and any other kind
                        case 500:
                        default:
                            throw $e;
                    }

                }

                if ($registrationSuccessful) {
                    // Commence auto login
                    $username = $form->getData()->getEmail();
                    $password = $form->getData()->getPassword();
                    $this->authenticateUser($username, $password);

                    // Return rendered registration successful message
                    return $this->render('BarbonHostedApiLandlordAuthenticationBundle:Register:success.html.twig');

                }
            }
        }

        // Send custom registration text to the view
        $options = $this->systemBrandService->getSystemBrandOptions();
        $customHeaderText = null;
        if (array_key_exists(DisplayPreferences::CUSTOM_TEXT_REGISTRATION_HEADER, $options->getDisplayPreferences()->getCustomText())) {
            $customHeaderText = $options->getDisplayPreferences()->getCustomText()[DisplayPreferences::CUSTOM_TEXT_REGISTRATION_HEADER];
        }

        $form = $form->createView();

        return compact(
            'form',
            'customHeaderText'
        );
    }

    /**
     * @Route()
     * @Method({"GET"})
     * @Template()
     *
     * @return array
     */
    public function successAction()
    {
        return array();
    }

    /**
     * Load the new user from IRIS and set them as logged in.
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    private function authenticateUser($username, $password)
    {
        $user = $this->userProvider->loadUserByUsername($username, $password);

        // TODO: move this security.context related code into the security bundle
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'login_secured', $user->getRoles());

        $this->container->get('security.context')->setToken($token);
    }
}
