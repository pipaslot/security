<?php

namespace Pipas\Rest\Result;

/**
 * Interface enabling recursive conversion to array
 * @author Petr Štipek <p.stipek@email.cz>
 */
interface IToArrayConversion {

	/**
	 * Converts object to array
	 * @param bool $recurse
	 * @param bool $omitEmpty
	 * @return array
	 */
	function toArray($recurse = true, $omitEmpty = false);

}
