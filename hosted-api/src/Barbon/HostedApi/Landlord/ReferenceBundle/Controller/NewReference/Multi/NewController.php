<?php

namespace Barbon\HostedApi\Landlord\ReferenceBundle\Controller\NewReference\Multi;

use Barbon\HostedApi\AppBundle\Event\NewMultiReferenceEvent;
use Barbon\HostedApi\AppBundle\Event\NewReferenceEvents;
use Barbon\HostedApi\AppBundle\Traits\IrisModelRetrieverTrait;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase;
use Barbon\HostedApi\Landlord\AuthenticationBundle\Form\Model\DirectLandlord;
use Barbon\HostedApi\AppBundle\Form\Common\Model\ProspectiveLandlord;

/**
 * @Route("/add", service="barbon.hosted_api.landlord.reference.controller.new_reference.multi.new_controller")
 */
class NewController extends Controller
{
    use IrisModelRetrieverTrait;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var IrisEntityManager
     */
    protected $irisEntityManager;

    /**
     * @var FormTypeInterface
     */
    protected $formType;

    /**
     * Constructor
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param IrisEntityManager $irisEntityManager
     * @param FormTypeInterface $formType
     */
    public function __construct
    (
        EventDispatcherInterface $eventDispatcher,
        IrisEntityManager $irisEntityManager,
        FormTypeInterface $formType
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->irisEntityManager = $irisEntityManager;
        $this->formType = $formType;
    }

    /**
     * @Route("/{caseId}", defaults={"caseId" = null})
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param null $caseId
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request, $caseId)
    {
        // If $caseId is given, get the Case from IRIS and prepopulate the form with Property Details AND Landlord Details
        // Otherwise, we can only prepopulate the form with Landlord Details, and will have to do this manually.
        $case = (null !== $caseId) ? $this->getCase($this->irisEntityManager, $caseId) : $this->getPrePopulatedCase();

        // Build the form.
        $form = $this->createForm($this->formType, array('case' => $case), array(
            'attr' => array(
                'id' => 'generic_step_form',
                'class' => 'referencing branded',
                'novalidate' => 'novalidate',
            )
        ));

        // Process a client round trip, if necessary
        if ($request->isXmlHttpRequest()) {
            $form->submit($request);
            return $this->render('BarbonHostedApiLandlordReferenceBundle:NewReference/Multi/Validate:index.html.twig', array(
                'form' => $form->createView()
            ));
        }

        // Submit the form
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Dispatch the new multi reference event.
            $this->eventDispatcher->dispatch(
                NewReferenceEvents::MULTI_REFERENCE_CREATED,
                new NewMultiReferenceEvent($case)
            );

            // Send the user to the success page.
            return $this->redirectToRoute('barbon_hostedapi_landlord_reference_newreference_multi_confirmation_index');
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Create a ReferencingCase with pre populated ProspectiveLandlord object.
     *
     * @return ReferencingCase
     */
    private function getPrePopulatedCase()
    {
        // First, get the DirectLandlord object from IRIS and instantiate an empty ProspectiveLandlord.
        $directLandlord = $this->irisEntityManager->find(new DirectLandlord());
        $prospectiveLandlord = new ProspectiveLandlord();

        // Now map the DirectLandlord into a new ProspectiveLandlord:
        // foreach property in the DirectLandlord, if the ProspectiveLandlord contains a setter method, set it using the corresponding get method.
        // TODO getter and setter discovery can be handled more effectively
        $directLandlordReflect = new \ReflectionClass($directLandlord);
        $properties = $directLandlordReflect->getProperties();
        foreach ($properties as $property) {
            $setMethod = sprintf('set%s', ucfirst($property->getName()));
            $getMethod = sprintf('get%s', ucfirst($property->getName()));
            if (method_exists($prospectiveLandlord, $setMethod) && method_exists($directLandlord, $getMethod)) {
                $prospectiveLandlord->{$setMethod}($directLandlord->{$getMethod}());
            }
        }

        // Add the newly formed ProspectiveLandlord to the case.
        $case = new ReferencingCase();
        $case->setProspectiveLandlord($prospectiveLandlord);

        return $case;
    }
}