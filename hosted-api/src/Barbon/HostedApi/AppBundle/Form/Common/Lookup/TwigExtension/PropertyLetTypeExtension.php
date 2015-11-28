<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Lookup\TwigExtension;

use Twig_SimpleFilter;

final class PropertyLetTypeExtension extends AbstractIrisLookupServiceExtension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'iris_property_let_type';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('iris_property_let_type_label', array($this, 'lookupLabel')),
        );
    }
}