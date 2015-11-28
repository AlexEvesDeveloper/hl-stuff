<?php

namespace Iris\Twig\Extension;

use Iris\Utility\Lookup\Lookup;

/**
 * Class LookupExtension
 *
 * @package Iris\Twig\Extension
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class LookupExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('iris_lookup_name', array($this, 'lookupNameFilter')),
        );
    }

    /**
     * Get lookup item name by id and category name
     *
     * @param int $id
     * @param string $categoryName
     * @return string
     */
    public function lookupNameFilter($id, $categoryName)
    {
        return Lookup::getInstance()->findById($categoryName, $id)->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'iris_lookup';
    }
}