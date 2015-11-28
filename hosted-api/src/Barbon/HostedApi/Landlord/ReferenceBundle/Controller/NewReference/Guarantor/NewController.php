<?php

namespace Barbon\HostedApi\Landlord\ReferenceBundle\Controller\NewReference\Guarantor;

use Barbon\HostedApi\AppBundle\Event\NewGuarantorReferenceEvent;
use Barbon\HostedApi\AppBundle\Event\NewReferenceEvents;
use Barbon\HostedApi\AppBundle\Form\Reference\EventListener\ReferencingGuarantorDecoratorBridgeSubscriber;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingGuarantor;
use Barbon\HostedApi\AppBundle\Traits\IrisModelRetrieverTrait;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/add", service="barbon.hosted_api.landlord.reference.controller.new_reference.guarantor.new_controller")
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
     * @var ReferencingGuarantorDecoratorBridgeSubscriber
     */
    protected $referencingGuarantorDecoratorBridgeSubscriber;

    /**
     * Constructor
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param IrisEntityManager $irisEntityManager
     * @param FormTypeInterface $formType
     * @param ReferencingGuarantorDecoratorBridgeSubscriber $referencingGuarantorDecoratorBridgeSubscriber
     */
    public function __construct
    (
        EventDispatcherInterface $eventDispatcher,
        IrisEntityManager $irisEntityManager,
        FormTypeInterface $formType,
        ReferencingGuarantorDecoratorBridgeSubscriber $referencingGuarantorDecoratorBridgeSubscriber
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->irisEntityManager = $irisEntityManager;
        $this->formType = $formType;
        $this->referencingGuarantorDecoratorBridgeSubscriber = $referencingGuarantorDecoratorBridgeSubscriber;
    }

    /**
     * @Route("/{applicationId}")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param $applicationId
     * @return array
     */
    public function indexAction(Request $request, $applicationId)
    {
        // Validate the $applicationId, throws Exception if invalid.
        $application = $this->getApplication($this->irisEntityManager, $applicationId);

        // Get the Case for this Tenant and put in the session, as it's needed throughout
        $case = $this->getCase($this->irisEntityManager, $application->getCaseId());
        $request->getSession()->set('submitted-case', serialize($case));

        // Create an empty ReferencingGuarantor object.
        $guarantor = new ReferencingGuarantor();
        $guarantor->setCaseId($application->getCaseId());

        // Build the form.
        $form = $this->createForm($this->formType, $guarantor, array(
            'guarantor_decorator' => $this->referencingGuarantorDecoratorBridgeSubscriber->getGuarantorDecorator(),
            'attr' => array(
                'id' => 'generic_step_form',
                'class' => 'referencing branded individual-guarantor-form',
                'novalidate' => 'novalidate',
            )
        ));

        // Process a client round trip, if necessary
        if ($request->isXmlHttpRequest()) {
            $form->submit($request);
            return $this->render('BarbonHostedApiLandlordReferenceBundle:NewReference/Guarantor/Validate:index.html.twig', array(
                'form' => $form->createView()
            ));
        }

        // Submit the form.
        $form->handleRequest($request);

        if ($form->isValid()) {
            $case = $this->getCase($this->irisEntityManager, $application->getCaseId());

            // Dispatch the new guarantor reference event.
            $this->eventDispatcher->dispatch(
                NewReferenceEvents::GUARANTOR_REFERENCE_CREATED,
                new NewGuarantorReferenceEvent($case, $application, $guarantor)
            );

            // Send the user to the success page.
            return $this->redirectToRoute('barbon_hostedapi_landlord_reference_newreference_guarantor_confirmation_index', array(
                'applicationId' => $applicationId
            ));
        }

        return array(
            'form' => $form->createView()
        );
    }
}