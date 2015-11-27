<?php

namespace Pipas\Rest\Result;
use Pipas\Rest\RestException;


/**
 * Access to API data by Array/object approach
 *
 * @author Petr Štipek <p.stipek@email.cz>
 */
class DataHash extends \stdClass implements \ArrayAccess, \Countable, \IteratorAggregate, IToArrayConversion
{
    /** @var array */
	private $_initializedProperties = array();

	/**
	 * Initialize property by ResultMapper
	 * @param string $propertyName
	 * @param mixed $value
	 * @param $defaultObjectType
	 * @return bool
	 * @internal
	 */
    public function initializeProperty($propertyName, $value, $defaultObjectType = DataHash::class)
    {
		if (in_array($propertyName, $this->_initializedProperties)) return false;
        $this->$propertyName = ResultMapper::get()->mapData($value, $defaultObjectType);
		$this->_initializedProperties[] = $propertyName;
        return true;
    }

    /**
     * Returns if property had been initialized by method initializeProperty()
     * @param string $propertyName
     * @return bool
     */
    public function isPropertyInitialized($propertyName)
    {
		return in_array($propertyName, $this->_initializedProperties);
    }


    /**
     * Returns items count.
     * @return int
     */
    public function count()
    {
        return count($this->toArray(false));
    }

    /**
     * Convert object to array
     * @param bool $recurse
	 * @param bool $omitEmpty
     * @return array
     */
	public function toArray($recurse = true, $omitEmpty = false)
    {
        $arr = array();
        foreach (get_object_vars($this) as $name => $value) {
			if (($omitEmpty AND $value === null) OR $this->toArrayFilter($name, $value)) continue;

            if ($recurse AND $value instanceof IToArrayConversion) {
                $arr[$name] = $value->toArray();
            } else {
                $arr[$name] = $value;
            }
        }
        return $arr;
    }

    /**
	 * Callback decisive parameter for whether a given key and value should be ignored when creating an array of object
     * @param mixed $propertyName
     * @param mixed $value
     * @return boolean
     */
    protected function toArrayFilter($propertyName, $value)
    {
		if ($propertyName === "_initializedProperties" OR $propertyName === "_mixedProperties") return true;
        return false;
    }


    /************** Array access ***************/

    /**
     * Returns an iterator over all items.
     * @return \RecursiveArrayIterator
     */
    public function getIterator()
    {
        return new \RecursiveArrayIterator($this);
    }

	/**
	 * Replaces or appends a item.
	 * @param mixed $key
	 * @param mixed $value
	 */
    public function offsetSet($key, $value)
    {
        $this->$key = $value;
    }

	/**
	 * Returns a item.
	 * @param mixed $key
	 * @return mixed
	 */
    public function offsetGet($key)
    {
        return $this->$key;
    }

	/**
	 * Determines whether a item exists.
	 * @param mixed $key
	 * @return bool
	 */
    public function offsetExists($key)
    {
        return isset($this->$key);
    }

	/**
	 * Removes the element from this list.
	 * @param mixed $key
	 */
    public function offsetUnset($key)
    {
        unset($this->$key);
    }

	/************** magic methods **************
	 * @param $name
	 * @return
	 * @throws RestException
	 */

    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        $list = ResultMapper::get()->getAnnotatedProperties(get_class($this));
        if (isset($list[$name])) {
            $this->initializeProperty($name, array(), $list[$name]);
        }
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            return $this->$name;
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);
        if (method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $this->$name = $value;
        }
        return $this;
    }

	/**
	 * @return string
	 */
	function __toString()
	{
		return json_encode($this->toArray(false, true));
	}


}
