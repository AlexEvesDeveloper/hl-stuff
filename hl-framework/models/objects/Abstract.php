<?php
class Model_Abstract {

    /**
     * Return all class constants' names and values for easy iteration over
     * them.  Override if there are more constants than you want returned, or
     * don't want to use (slow) reflection to find them.
     */
    static public function iterableKeys() {

        $reflection = new ReflectionClass(get_called_class());
        return $reflection->getConstants();
    }

    public function toArray() {
        return get_object_vars($this);
    }

    public function toJson() {
        return Zend_Json::encode($this->toArray());
    }

    // Prevent dynamically creating new variables in the object at run-time!
    public function __set($name, $value)
    {
        if(property_exists($this, $name)) {
            $this->$name = $value;
            return true;
        }
        else {
            throw new Exception ("Invalid object variable '$name'");
        }
    }

    // Prevent dynamically retreiving variables that don't exist in the object at run-time!
    public function __get($name)
    {
        if(property_exists($this, $name)) {
              return $this->$name;
        }
        else {
            throw new Exception ("Invalid object variable '$name'");
        }
    }

    public function __construct() {

    }
}
?>