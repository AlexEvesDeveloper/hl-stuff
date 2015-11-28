<?php

namespace Barbon\HostedApi\AppBundle\Traits;

use Barbon\HostedApi\AppBundle\Exception\CaseNotSubmittedException;
use Barbon\HostedApi\AppBundle\Form\Reference\Model\ReferencingCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

trait SessionModelRetrieverTrait
{
    /**
     * @param Session|SessionInterface $session
     * @return ReferencingCase
     * @throws CaseNotSubmittedException
     */
    public function getCase(SessionInterface $session)
    {
        $case = unserialize($session->get('submitted-case'));

        if (false === $case) {
            throw new CaseNotSubmittedException('submitted-case could not be retrieved from the session.');
        }

        return $case;
    }
}