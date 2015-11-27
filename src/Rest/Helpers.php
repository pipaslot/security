<?php

namespace Pipas\Rest;

use OutOfRangeException;

/**
 * Auxiliary functions for REST tools
 *
 * @author Petr Štipek <p.stipek@email.cz>
 */
class Helpers {

	/**
	 * Converts value to boolean false/true, or 0/1, or "false"/"true"
	 * @param mixed $value
	 * @throws OutOfRangeException In the event that the value can not recognize
	 * @return bool
	 */
	public static function toBoolean($value)
	{
		if($value == null) return false;
		if(is_bool($value)) return $value;
		elseif(is_string($value))
		{
			$lowercase = strtolower($value);
			if($lowercase == "true") return true;
			if($lowercase == "false") return false;
		}
		elseif(is_numeric($value))
		{
			return $value != 0;
		}
		throw new \OutOfRangeException("Can not convert value '$value' to bool. Use TRUE or FALSE.");
	}

}
