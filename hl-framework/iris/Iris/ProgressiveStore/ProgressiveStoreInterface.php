<?php

namespace Iris\ProgressiveStore;

use Barbondev\IRISSDK\Common\ClientRegistry\Context\ContextInterface;

/**
 * Interface ProgressiveStoreInterface
 *
 * @package Iris\ProgressiveStore
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
interface ProgressiveStoreInterface
{
    /**
     * Set IRIS SDK context
     *
     * @param ContextInterface $context
     * @return $this
     */
    public function setIrisSdkContext(ContextInterface $context);

    /**
     * Initialise the prototypes
     *
     * @return void
     */
    public function initialisePrototypes();

    /**
     * Get the name of the progressive store
     *
     * @return string
     */
    public function getName();

    /**
     * Get a prototype by class name
     *
     * @param string $class
     * @return mixed
     */
    public function getPrototypeByClass($class);

    /**
     * Persist prototypes
     *
     * @param string $class
     * @return void
     */
    public function persistPrototypes($class);

    /**
     * Fetch data
     *
     * @param string $class
     * @return mixed
     */
    public function fetch($class);

    /**
     * Add a prototype
     *
     * @param object $prototype
     * @return $this
     */
    public function addPrototype($prototype);

    /**
     * Store data
     *
     * @param mixed $data
     * @return object
     */
    public function store($data);

    /**
     * Store the current prototypes
     *
     * @return $this
     */
    public function storePrototypes();

    /**
     * Clears the current list of prototypes
     *
     * @return $this
     */
    public function clearPrototypes();

    /**
     * Dumps all prototypes currently held in storage as a
     * debug string
     *
     * @return string
     */
    public function dumpDebugStorage();
}