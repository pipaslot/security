<?php


namespace Pipas\Rest\Result;

/**
 * @author Petr Štipek <p.stipek@email.cz>
 */
interface IContractReadonly extends \ArrayAccess
{
	/**
	 * @param $id
	 * @return self
	 */
	function setId($id);

	/**
	 * @return int|null
	 */
	function getId();
}