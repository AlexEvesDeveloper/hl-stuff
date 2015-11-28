<?php

namespace Barbon\HostedApi\AppBundle\Traits;

use Barbon\HostedApi\AppBundle\Form\Common\Model\ReferencingApplicationCollection;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingApplication;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingGuarantor;
use Barbon\IrisRestClient\EntityManager\Exception\NotFoundException;
use Barbon\IrisRestClient\EntityManager\IrisEntityManager;

trait IrisModelRetrieverTrait
{
    /**
     * @param IrisEntityManager $irisEntityManager
     * @param string $caseId
     * @return ReferencingCase
     * @throws NotFoundException
     */
    public function getCase(IrisEntityManager $irisEntityManager, $caseId)
    {
        try {
            return $irisEntityManager->find(new ReferencingCase(), array('caseId' => $caseId,));
        }
        catch(NotFoundException $ex) {
            throw new NotFoundException(sprintf('Case with caseId "%s" could not be found', $caseId));
        }
    }

    /**
     * @param IrisEntityManager $irisEntityManager
     * @param string $applicationId
     * @return ReferencingApplication
     * @throws NotFoundException
     */
    public function getApplication(IrisEntityManager $irisEntityManager, $applicationId)
    {
        try {
            return $irisEntityManager->find(new ReferencingApplication(), array('applicationId' => $applicationId,));
        }
        catch(NotFoundException $ex) {
            throw new NotFoundException(sprintf('Application with applicationId "%s" could not be found', $applicationId));
        }
    }

    /**
     * @param IrisEntityManager $irisEntityManager
     * @param string $applicationId
     * @return ReferencingGuarantor
     * @throws NotFoundException
     */
    public function getGuarantor(IrisEntityManager $irisEntityManager, $applicationId)
    {
        try {
            return $irisEntityManager->find(new ReferencingGuarantor(), array('applicationId' => $applicationId,));
        }
        catch(NotFoundException $ex) {
            throw new NotFoundException(sprintf('Application with applicationId "%s" could not be found', $applicationId));
        }
    }

    /**
     * @param IrisEntityManager $irisEntityManager
     * @param string $caseId
     * @return ReferencingApplication
     * @throws NotFoundException
     */
    public function getApplications(IrisEntityManager $irisEntityManager, $caseId)
    {
        try {
            $collection = $irisEntityManager->find(new ReferencingApplicationCollection(), array('caseId' => $caseId,));

            if (null !== $collection) {
                $applications = array();
                foreach ($collection as $application) {
                    $applications[] = $application;
                }
            }

            return $applications;
        }
        catch(NotFoundException $ex) {
            throw new NotFoundException(sprintf('Applications with caseId "%s" could not be found', $caseId));
        }
    }
}