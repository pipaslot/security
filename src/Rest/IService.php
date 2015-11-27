<?php

namespace Pipas\Rest;

use Pipas\Rest\Result\IContract;

/**
 * Read and Write basic interface
 *
 * @author Petr Štipek <p.stipek@email.cz>
 */
interface IService extends IReadOnlyService
{

	/**
	 * Create new record and set up entity id from API
	 * @param IContract $entity
	 * @return IContract
	 */
	function create(IContract $entity);

	/**
	 * Update record by entity
	 * @param IContract $entity
	 * @return bool
	 */
	function update(IContract $entity);

	/**
	 * Delete record by entity
	 * @param IContract $entity
	 * @return bool
	 */
	function delete(IContract $entity);
}
