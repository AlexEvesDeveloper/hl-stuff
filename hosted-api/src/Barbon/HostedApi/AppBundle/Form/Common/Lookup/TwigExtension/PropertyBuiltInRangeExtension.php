<?php

namespace Barbon\HostedApi\AppBundle\Form\Common\Lookup\TwigExtension;

use Twig_SimpleFilter;

final class PropertyBuiltInRangeExtension extends AbstractIrisLookupServiceExtension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'iris_property_built_in_range';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('iris_property_built_in_range_label', array($this, 'lookupLabel')),
        );
    }
}