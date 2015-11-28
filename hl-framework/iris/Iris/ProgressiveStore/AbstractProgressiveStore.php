<?php

namespace Iris\ProgressiveStore;

use Barbondev\IRISSDK\Common\ClientRegistry\Context\ContextInterface;
use Iris\ProgressiveStore\Exception\AccessorsNotFoundException;
use Iris\ProgressiveStore\Exception\PrototypeNotFoundException;
use Iris\ProgressiveStore\Exception\StepNotFoundException;
use Barbondev\IRISSDK\Common\Model\Address;

/**
 * Class AbstractProgressiveStore
 *
 * @package Iris\FormSet\ProgressiveStore
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
abstract class AbstractProgressiveStore implements ProgressiveStoreInterface
{
    /**
     * Form step name constant
     */
    const FORM_STEP_NAME = 'step';

    /**
     * Session namespace
     */
    const SESSION_NAMESPACE = 'progressive_store';

    /**
     * @var array
     */
    protected $prototypes;

    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     * Constructor
     */
    final public function __construct()
    {
        $this->initialisePrototypes();

        $this->prototypes = $this->getSessionData($this->getName(), $this->prototypes);
    }

    /**
     * {@inheritdoc}
     */
    public function clearPrototypes()
    {
        $this->prototypes = array();

        $this->setSessionData($this->getName(), array());

        // In fact, clear everything
        $this->clearSessionNamespace();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function storePrototypes()
    {
        $this->setSessionData($this->getName(), $this->prototypes);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function store($data)
    {
        if (!isset($data[self::FORM_STEP_NAME])) {
            throw new StepNotFoundException(sprintf('Step form not found with name %s', self::FORM_STEP_NAME));
        }

        $data = $data[self::FORM_STEP_NAME];

        $prototype = clone $this->mergeIntoPrototype($data);

        $this->persistPrototypes(is_object($data) ? get_class($data) : null);

        $this->storePrototypes();

        return $prototype;
    }

    /**
     * Format a datetime to push to IRIS API
     *
     * @param mixed|\DateTime $dateTime
     * @return null|string
     */
    protected function transformDateTime($dateTime)
    {
        if ($dateTime instanceof \DateTime) {
            return $dateTime->format('Y-m-d');
        }

        if ($dateTime) {
            return $dateTime;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrototypeByClass($class)
    {
        if (!isset($this->prototypes[$class])) {
            return null;
        }

        return $this->prototypes[$class];
    }

    /**
     * Returns TRUE if a prototype exists
     *
     * @param object $object
     * @return bool
     */
    private function prototypeExists($object)
    {
        return array_key_exists(get_class($object), $this->prototypes);
    }

    /**
     * Merge a request object into a prototype
     *
     * @param object $object
     * @throws Exception\PrototypeNotFoundException
     * @throws Exception\AccessorsNotFoundException
     * @return object
     */
    private function mergeIntoPrototype($object)
    {
        if (!$this->prototypeExists($object)) {
            throw new PrototypeNotFoundException(sprintf('Prototype not found with class name %s', get_class($object)));
        }

        $class = get_class($object);
        $prototype = $this->prototypes[$class];

        foreach (get_class_methods($class) as $setterMethod) {
            if (preg_match('/^set\w+$/i', $setterMethod)) {
                $getterMethod = sprintf('get%s', ltrim($setterMethod, 'set'));
                if (method_exists($prototype, $setterMethod) && method_exists($object, $getterMethod)) {
                    if (null !== $object->$getterMethod()) {
                        $prototype->$setterMethod($object->$getterMethod());
                    }
                }
                else {
                    throw new AccessorsNotFoundException(sprintf(
                        'Accessors not found for %s, trying to use %s and %s', $class, $setterMethod, $getterMethod
                    ));
                }
            }
        }

        return $prototype;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($class)
    {
        return array(
            self::FORM_STEP_NAME => clone $this->prototypes[$class],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setIrisSdkContext(ContextInterface $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addPrototype($prototype)
    {
        $this->prototypes[get_class($prototype)] = $prototype;

        return $this;
    }

    /**
     * Set session data
     *
     * @param string $name
     * @param mixed $value
     */
    private function setSessionData($name, $value)
    {
        $_SESSION[self::SESSION_NAMESPACE][$name] = $value;
    }

    /**
     * Get session data
     *
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    private function getSessionData($name, $default = null)
    {
        if (isset($_SESSION[self::SESSION_NAMESPACE][$name])) {
            return $_SESSION[self::SESSION_NAMESPACE][$name];
        }

        return $default;
    }

    /**
     * Clear the entire namespace
     *
     * @return void
     */
    public function clearSessionNamespace()
    {
        if (isset($_SESSION[self::SESSION_NAMESPACE])) {
            unset($_SESSION[self::SESSION_NAMESPACE]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dumpDebugStorage()
    {
        return print_r($_SESSION[self::SESSION_NAMESPACE], true);
    }

    /**
     * Checks to see if an address is empty. Returns TRUE if any
     * address properties are null
     *
     * @param Address|null $address
     * @return bool
     */
    protected function isAddressEmpty(Address $address = null)
    {
        if (null === $address) {
            return true;
        }

        $reflection = new \ReflectionObject($address);

        foreach ($reflection->getProperties() as $property) {

            if (null !== $property->getValue($address)) {

                if ('country' == $property->getName() && 'United Kingdom' == $property->getValue($address)) {
                    return true;
                }

                return false;
            }
        }

        return true;
    }
}
