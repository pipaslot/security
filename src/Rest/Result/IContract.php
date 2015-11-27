<?php


namespace Pipas\Rest\Result;

/**
 * Read-write entity
 *
 * @author Petr Štipek <p.stipek@email.cz>
 */
interface IContract extends IContractReadonly
{
	/**
	 * Convert entity to array for create operation
	 * @return array
	 */
	function toArrayForCreate();

	/**
	 * Convert entity to array for update operation
	 * @return array
	 */
	function toArrayForUpdate();
}