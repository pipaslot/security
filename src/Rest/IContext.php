<?php

namespace Pipas\Rest;

use Pipas\Rest\Result\DataHash;
use Pipas\Rest\Result\DataSet;

/**
 * Context designed for connection to remote tables
 * @author Petr Štipek <p.stipek@email.cz>
 */
interface IContext
{

	/**
	 * Define mapping for auto-loading of services
	 * @example 'MyRest/Services/*Service'
	 * @param $namespace
	 * @return $this
	 */
	function addServiceMapping($namespace);

	/**
	 * Return drive for connection to the API via REST
	 * @return IDriver
	 */
	function getDriver();

	/**
	 * Returns instance of repository under this context
	 * @param string $name
	 * @return IService
	 */
	function getService($name);

	/**
	 * Validate the short time token
	 * @param string $token
	 * @return bool
	 */
	function validateToken($token);

	/**
	 * Find one by ID
	 * @param string $serviceName
	 * @param $id
	 * @return DataHash|null
	 */
	function find($serviceName, $id);

	/**
	 * Returns all available records
	 * @param string $serviceName
	 * @return DataSet
	 */
	function findAll($serviceName);

	/**
	 * Returns all available records filtered by query
	 * @param string $serviceName
	 * @param array $query
	 * @return DataSet
	 */
	function findBy($serviceName, array $query);

	/**
	 * Returns the first from available records filtered by query
	 * @param string $serviceName
	 * @param array $query
	 * @return DataHash|null
	 */
	function findOneBy($serviceName, array $query = array());

	/**
	 * Create new record
	 * @param string $serviceName
	 * @param array $entity
	 * @return int New ID
	 */
	function create($serviceName, array $entity);

	/**
	 * Update record
	 * @param string $serviceName
	 * @param array $entity
	 * @return bool
	 */
	function update($serviceName, array $entity);

	/**
	 * Delete record
	 * @param string $uri full name with Id
	 * @return bool
	 */
	function delete($uri);
}
