<?php

namespace Iris\Twig\Extension;

/**
 * Class DurationExtension
 *
 * @package Iris\Twig\Extension
 * @author Ashley J. Dawson
 */
class DurationExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('iris_duration_years', array($this, 'durationYearsFilter')),
            new \Twig_SimpleFilter('iris_duration_months', array($this, 'durationMonthsFilter'))
        );
    }

    /**
     * Get the years duration from total months
     *
     * @param int $totalMonths
     * @return string
     */
    public function durationYearsFilter($totalMonths)
    {
        return floor($totalMonths / 12);
    }

    /**
     * Get the remaining months duration from total months
     *
     * @param int $totalMonths
     * @return string
     */
    public function durationMonthsFilter($totalMonths)
    {
        return $totalMonths % 12;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'iris_duration';
    }
}