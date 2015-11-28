<?php

namespace RRP\DependencyInjection;

/**
 * Interface ContainerInterface
 *
 * @package RRP\DependencyInjection
 * @author April Portus <april.portus@barbon.com>
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