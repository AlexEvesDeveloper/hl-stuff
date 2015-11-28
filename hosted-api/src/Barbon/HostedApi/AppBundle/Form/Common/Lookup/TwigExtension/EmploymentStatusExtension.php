<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Lookup\TwigExtension;

use Twig_SimpleFilter;

final class EmploymentStatusExtension extends AbstractIrisLookupServiceExtension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'iris_employment_status';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('iris_employment_status_label', array($this, 'lookupLabel')),
        );
    }
}