<?php

namespace Pipas\Rest;

use Pipas\Rest\Result\DataHash;
use Pipas\Rest\Result\DataSet;

/**
 * Read only service for one concrete table
 * @author Petr Štipek <p.stipek@email.cz>
 */
interface IReadOnlyService
{
	const NAME_SUFFIX = "Service";

	/**
	 * @return IContext
	 */
	function getContext();

	/**
	 * Find one by ID
	 * @param int $id
	 * @return DataHash|null
	 */
	function find($id);

	/**
	 * Returns all available records
	 * @return DataSet
	 */
	function findAll();

	/**
	 * Returns all available records filtered by query
	 * @param array $query
	 * @return DataSet
	 */
	function findBy(array $query);

	/**
	 * Returns the first from available records filtered by query
	 * @param array $query
	 * @return DataHash|null
	 */
	function findOneBy(array $query = array());

	/**
	 * Returns target service name
	 * @return string
	 */
	function getName();
}
