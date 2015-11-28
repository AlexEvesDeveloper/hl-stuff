<?php

namespace Iris\DependencyInjection;

/**
 * Class ContainerInterface
 *
 * @package Iris\DependencyInjection
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface ContainerInterface
{
    /**
     * Get a service or parameter by id
     *
     * @param string $id
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function get($id);
}