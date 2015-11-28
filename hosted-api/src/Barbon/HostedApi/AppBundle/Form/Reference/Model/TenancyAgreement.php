<?php

namespace Barbon\HostedApi\AppBundle\Form\Reference\Model;

/**
 * TenancyAgreement Model
 *
 * @author Alex Eves <alex.eves@barbon.com>
 */
class TenancyAgreement
{
    /**
     * @var array
     */
    protected $applications = array();

    /**
     * Set application list
     *
     * @param array $applications
     */
    public function setApplications(array $applications)
    {
        $this->applications = $applications;
    }
    
    /**
     * Get application list
     *
     * @return array
     */
    public function getApplications()
    {
        return $this->applications;
    }
}
