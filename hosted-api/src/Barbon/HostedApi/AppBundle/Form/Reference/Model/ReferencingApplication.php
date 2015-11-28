<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\Model;

use Barbon\HostedApi\AppBundle\Form\Common\Model\LettingReferee;
use Barbon\IrisRestClient\Annotation as Iris;

/**
 * @Iris\Entity\ReferencingApplication
 */
final class ReferencingApplication extends AbstractReferencingApplication
{
    /**
     * @Iris\Field
     * @var LettingReferee
     */
    private $lettingReferee;
    
    /**
     * @var ReferencingGuarantor[]
     */
    private $guarantors;


    /**
     * Get letting referee
     *
     * @return LettingReferee
     */
    public function getLettingReferee()
    {
        return $this->lettingReferee;
    }

    /**
     * Set letting referee
     *
     * @param LettingReferee $lettingReferee
     * @return $this
     */
    public function setLettingReferee(LettingReferee $lettingReferee)
    {
        $this->lettingReferee = $lettingReferee;
        return $this;
    }

    /**
     * Get the guarantors
     *
     * @return ReferencingGuarantor[]
     */
    public function getGuarantors()
    {
        return $this->guarantors;
    }

    /**
     * Set the guarantors
     *
     * @param ReferencingGuarantor[] $guarantors
     * @return $this
     */
    public function setGuarantors($guarantors)
    {
        $this->guarantors = $guarantors;
        return $this;
    }

    /**
     * @param ReferencingGuarantor $guarantor
     * @return $this
     */
    public function addGuarantor(ReferencingGuarantor $guarantor)
    {
        $this->guarantors[] = $guarantor;
        return $this;
    }
}
