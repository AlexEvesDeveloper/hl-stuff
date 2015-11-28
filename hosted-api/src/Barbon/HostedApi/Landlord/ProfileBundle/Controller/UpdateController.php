<?php

namespace Barbon\HostedApi\Landlord\ProfileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;
use Barbon\HostedApi\Landlord\AuthenticationBundle\Form\Model\DirectLandlord;

/**
 * Landlord dashboard page
 *
 * @Route("/update", service="barbon.hosted_api.landlord.profile.controller.update_controller")
 */
class UpdateController extends Controller
{
    /**
     * @var IrisEntityManager
     */
    private $irisEntityManager;

    /**
     * Constructor
     *
     * @param IrisEntityManager $irisEntityManager
     */
    public function __construct(IrisEntityManager $irisEntityManager)
    {
        $this->irisEntityManager = $irisEntityManager;
    }

    /**
     * @Route()
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return Response 
     */
    public function indexAction(Request $request)
    {
        // Determine which field to update...
        $fieldToUpdate = $request->request->get('name');
        // ...and which value to update it with
        $newValue = $request->request->get('value');

        // Now grab the existing model from IRIS
        $landlord = $this->irisEntityManager->find(new DirectLandlord());

        // We want to update the object, then write it back...
        // ...IRIS requires an ID, and valid long and lat values, so just do as it says - they have no effect
        $landlord->setDirectLandlordId(1);
        $landlord->getAddress()->setLatitude(sprintf('%.2f', 0.0));
        $landlord->getAddress()->setLongitude(sprintf('%.2f', 0.0));

        // Set the new value that was posted in by dynamically forming the setter method for this field
        // TODO can be done better
        $setterMethod = sprintf('set%s', ucfirst($fieldToUpdate));
        $landlord->{$setterMethod}($newValue);

        // Validate the new object in accordance with the Constraint annotations on the model. 
        $validator = $this->container->get('validator');
        $errors = $validator->validate($landlord);

        if (0 < count($errors)) {
            // Error found, return the error message, don't attempt persisting to IRIS
            $response = array('status' => 'error', 'msg' => $errors[0]->getMessage());
            return new Response(json_encode($response), 400);
        }

        // Form validation won't catch this requirement, so run the check manually
        // TODO explore a solution that uses validation_groups in conjuction with the $validator->validate method()
        if ( null == $landlord->getDayPhone() && null == $landlord->getEveningPhone()) {
            $response = array('status' => 'error', 'msg' => 'At least one telephone number must be provided');
            return new Response(json_encode($response), 400);
        }

        try {
            $this->irisEntityManager->persist($landlord);
        } catch (\Exception $e) {
            // Most likely IRIS is down, although will also get here if we passed the validation above, but not on the IRIS side
            return new Response(null, 503);
        }

        // successful update
        return new Response(null, 204);
    }
}
