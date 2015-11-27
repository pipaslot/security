<?php

namespace Pipas\Rest;

/**
 * Class to control communication between the application and the REST API
 *
 * @author Petr Štipek <p.stipek@email.cz>
 */
interface IDriver
{

	/**
	 * Send GET request to receive the data
	 * @param string $uri
	 * @param array $query
	 * @return mixed
	 */
	function sendGet($uri, $query = array());

	/**
	 * Update record by POST request
	 * @param string $uri
	 * @param mixed $data
	 * @return mixed
	 */
	function sendPost($uri, array $data);

	/**
	 * Create new record by PUT request
	 * @param string $uri
	 * @param mixed $data
	 * @return mixed
	 */
	function sendPut($uri, array $data);

	/**
	 * Delete data by DELETE request
	 * @param string $uri
	 * @return mixed
	 */
	function sendDelete($uri);

	/**
	 * Constructing URL for API communication
	 * @param string $service
	 * @param array $query
	 * @return string
	 */
	function buildUri($service, $query = array());

	/**
	 * Check if the connection is working
	 * @return bool
	 */
	function checkConnection();
}
