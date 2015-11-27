<?php

namespace Pipas\Rest\Result;

use Nette\Reflection\ClassType;
use Pipas\Rest\IReadOnlyService;
use Pipas\Rest\RestException;

/**
 * Class ResultMapper mapping array data to objects
 * @package Pipas\Rest\Result
 */
class ResultMapper
{
	private static $instance;

	public static function get()
	{
		if (!self::$instance) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	/** @var array List of properties defined in annotations */
	private $classProperties = array();

	/**
	 * Maps data array to DataArray and DataHash Objects
	 * @param array $data
	 * @param null|string $classType
	 * @return DataArray|DataHash
	 */
	public function mapData($data, $classType = null)
	{
		if (!is_array($data)) return $data;
		if ($classType) {
			return $this->isArrayOfAssociativeArrays($data) ? $this->mapDataArray($data, $classType) : $this->mapDataHash($data, $classType);
		}
		return $this->isArrayOfAssociativeArrays($data) ? $this->mapDataArray($data) : $this->mapDataHash($data);
	}

	/**
	 * @param array|null $data
	 * @param int $totalCount
	 * @param $classType
	 * @return DataSet|null
	 */
	public function mapDataSet($data, $totalCount = 0, $classType = DataSet::class)
	{
		if ($data === null) return null;
		$cData = array();
		foreach ($data as $row) {
			$cData[$row['id']] = $this->mapDataHash($row);
		}
		return new $classType($cData, $totalCount);
	}

	/**
	 * @param array|DataArray|DataHash|null $data
	 * @param IReadOnlyService $repository
	 * @param $classType
	 * @return Contract
	 */
	public function mapEntity($data, IReadOnlyService $repository, $classType = Contract::class)
	{
		if ($data === null) return null;
		$obj = new $classType($repository);
		$mapped = $this->initDataHash($obj, $data);
		return $mapped;
	}

	/**
	 * @param DataHash|null $dataHash
	 * @param IReadOnlyService|null $repository
	 * @param string $classType
	 * @return Contract|null
	 */
	public function convertDataHashToEntity(DataHash $dataHash = null, IReadOnlyService $repository = null, $classType = Contract::class)
	{
		if ($dataHash == null) return null;
		$entity = new $classType($repository);
		foreach ($dataHash->toArray() as $key => $val) {
			$entity->$key = $val;
		}
		return $entity;
	}

	/**
	 * @param DataSet|null $dataSet
	 * @param IReadOnlyService|null $repository
	 * @param string $classType
	 * @return Contract|null
	 */
	public function convertDataSetToEntitySet(DataSet $dataSet = null, IReadOnlyService $repository = null, $classType = Contract::class)
	{
		if ($dataSet == null) return null;
		$set = new DataSet();
		foreach ($dataSet->getData() as $key => $val) {
			$set->offsetSet($key, $this->mapEntity($val, $repository, $classType));
		}
		return $set;
	}

	/**
	 * @param array $data
	 * @param string $classType Name of target class extended from DataHash
	 * @return DataHash
	 */
	protected function mapDataHash($data, $classType = DataHash::class)
	{
		$obj = new $classType();
		return $this->initDataHash($obj, $data);
	}

	/**
	 * Apply data to DataHash
	 * @param DataHash $hash
	 * @param array $data
	 * @return DataHash
	 */
	protected function initDataHash(DataHash $hash, $data)
	{
		if ($data != null) {
			$defaultObjects = $this->getAnnotatedProperties(get_class($hash));
			foreach ($data as $key => $value) {
				if (isset($defaultObjects[$key])) {
					$hash->initializeProperty($key, $this->mapDataHash($value));
				} else if (is_array($value)) {
					$hash->initializeProperty($key, $this->isAssociativeArray($value) ? $this->mapDataHash($value) : $this->mapDataArray($value));
				} else {
					$hash->initializeProperty($key, $value);
				}
			}
		}
		return $hash;
	}

	/**
	 * @param array $data
	 * @param string $classType
	 * @return DataArray
	 */
	protected function mapDataArray($data, $classType = DataArray::class)
	{
		/** @var DataArray $obj */
		$obj = new $classType();
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$obj->offsetSet($key, $this->isArrayOfAssociativeArrays($value) ? $this->mapDataArray($value) : $this->mapDataHash($value));
			} else {
				$obj->offsetSet($key, $value);
			}
		}
		return $obj;
	}

	/*************************** Annotation parsing *****************************/
	/**
	 * Parse class and returns names and target classes of annotated properties
	 * @param $className
	 * @return mixed
	 * @throws RestException
	 */
	public function getAnnotatedProperties($className)
	{
		if (!isset($this->classProperties[$className])) {
			$this->classProperties[$className] = array();
			$ref = new ClassType($className);
			if ($ref->isAbstract() OR $ref->isInterface()) throw new RestException("Class can not be either abstract nor interface");
			$ann = $ref->getAnnotations();
			$parents = class_parents($className);
			$parents[$className] = $className;
			if ($className != DataHash::class AND (!$parents OR !in_array(DataHash::class, $parents))) {
				throw RestException::notInheritedForm($className, DataHash::class);
			}
			$this->parseProperties($ref, $ann, 'property');
			$this->parseProperties($ref, $ann, 'property-read');
		}
		return $this->classProperties[$className];
	}

	/**
	 * Parse Annotation
	 * @param ClassType $ref
	 * @param array $annotations
	 * @param string $type name of annotation
	 * @throws RestException
	 */
	private function parseProperties(ClassType $ref, array $annotations, $type)
	{
		if (!isset($annotations[$type])) return;
		foreach ($annotations[$type] as $val) {
			$trimmed = trim(preg_replace('!\s+!', ' ', $val));//Replace multiple whitespaces
			$className = strstr($trimmed, ' ', true);
			//Try find full name of existing class
			if (!class_exists($className)) {
				$className = $ref->getNamespaceName() . '\\' . trim($className, '\\');
				if (!class_exists($className)) continue;
			}
			$parents = class_parents($className);
			if ($className != DataHash::class AND (!$parents OR !in_array(DataHash::class, $parents))) {
				throw RestException::notInheritedForm($className, DataHash::class);
			}

			$prop = strstr($trimmed, '$');
			$pos = strpos($prop, ' ');
			$propertyName = $pos != false ? substr($prop, 1, $pos - 1) : substr($prop, 1);

			$property = $this->getClassPropertyByName($ref, $propertyName);
			if ($property AND !$property->protected) {
				throw RestException::notProtectedProperty($ref->getName(), $propertyName);
			}
			$this->classProperties[$ref->getName()][$propertyName] = $className;
		}
	}

	/**
	 * @param ClassType $ref
	 * @param $name
	 * @return \Nette\Reflection\Property|null
	 */
	private function getClassPropertyByName(ClassType $ref, $name)
	{
		foreach ($ref->properties as $prop) {
			if ($prop->name == $name) return $prop;
		}
		return null;
	}
	/*************************** Helpers *****************************/
	/**
	 * Return if array is associative
	 * @param array $arr
	 * @return bool
	 */
	private function isAssociativeArray(array $arr)
	{
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	/**
	 * Returns if array is of associative arrays
	 * @param array $arr
	 * @return bool
	 */
	private function isArrayOfAssociativeArrays(array $arr)
	{
		foreach ($arr as $sub) {
			if (!is_array($sub) OR !$this->isAssociativeArray($sub)) return false;
		}
		return true;
	}
}