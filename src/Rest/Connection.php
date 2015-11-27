<?php

namespace Pipas\Rest;

/**
 * Collection of setting for connection with REST API
 *
 * @author Petr Štipek <p.stipek@email.cz>
 */
class Connection
{
	/** @var string Name of instance or extra identifier */
	private $identifier;

	/** @var string Username */
	private $username;

	/** @var string Password or ApiKey or Api Security token */
	private $password;

	/** @var string Target URI */
	private $url;

	/**
	 * All records created through this API are owned by user specified through constructor.
	 *
	 * @param string $identifier instance name -> can be found in URL e.g. https://raynet.cz/instance -> instance
	 * @param string $username
	 * @param string $password generated token from application (Users' profile -> Change security -> Reset new API key)
	 * @param string $url Target URL address for sending queries. If the value is not defined, then will be used default value (e.g.: https://raynet.cz/api/v2/%s/)
	 */
	public function __construct($identifier, $username, $password, $url)
	{
		$this->identifier = $identifier;
		$this->username = $username;
		$this->password = $password;
		$this->url = $url;
	}

	/**
	 *
	 * @return string
	 */
	function getInstanceName()
	{
		return $this->identifier;
	}

	/**
	 *
	 * @return string
	 */
	function getUserName()
	{
		return $this->username;
	}

	/**
	 *
	 * @return string
	 */
	function getApiKey()
	{
		return $this->password;
	}

	/**
	 *
	 * @return string
	 */
	function getApiUrl()
	{
		return $this->url;
	}

}
