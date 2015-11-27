<?php

namespace Pipas\Rest\Result;

/**
 * Array used for iterations under objects
 *
 * @author Petr Štipek <p.stipek@email.cz>
 */
class DataArray implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /** @var DataHash[] */
    protected $data;

    function __construct(array $data = array())
    {
        $this->data = $data;
    }

    /**
	 * Looks for record by Id keys
     * @param int $id
     * @return DataHash|null
     */
    public function find($id)
    {
        return isset($this->data[$id]) ? $this->data[$id] : null;
    }

    /**
	 * Sort by column name
     * @param mixed $column
	 * @param bool $ascending
	 * @return bool    Information about the sort of success
     */
    public function sortBy($column, $ascending = true)
    {
        $sortBy = array();
        foreach ($this->data as $key => $row) {
            if (!isset($row[$column])) return false;
            $sortBy[$key] = $row[$column];
        }
        array_multisort($sortBy, $ascending ? SORT_ASC : SORT_DESC, $this->data);

        return true;
    }

    /**
	 * Returns associative array for form select list
	 * @param string|array $valueProperty Property name, which is to list as a value, or an array of properties that are to appear in the specified order with the applied format
	 * @param string $format The format for function vsprint, which said how to compose properties
     * @return array
     */
    public function toList($valueProperty = 'name', $format = '%s')
    {
        $props = is_array($valueProperty) ? $valueProperty : array($valueProperty);

        $select = array();
        foreach ($this->data as $row) {
            $values = array();
            foreach ($props as $key => $propName) {
                $values[$key] = $row[$propName];
            }
			$select[(string)$row->id] = vsprintf($format, $values);
        }
        return $select;
    }

    /**
     * Iterator pro foreach
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * Ger first element
     * @return mixed|false
     */
    function getFirst()
    {
        return reset($this->data);
    }

    /**
     * Database result
     * @return DataHash
     */
    function getData()
    {
        return $this->data;
    }

	/**
	 * Returns a item.
	 * @param mixed $offset
	 * @return mixed
	 */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

	/**
	 * Returns a item.
	 * @param mixed $offset
	 * @return mixed
	 */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

	/**
	 * Removes the element from this list.
	 * @param mixed $offset
	 */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Returns items count.
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }
}
