<?php

namespace Pipas\Rest;

/**
 * Exception serving as the announcement of the fall, which should solve the developers and appropriately treated.
 * Not recommended to capture at runtime.
 *
 * @author Petr Štipek <p.stipek@email.cz>
 */
class CurlException extends \Exception
{

    private $info;
    private $url;
    private $options;
    private $response;

	public function __construct($message, array $options, array $curlInfo, $response = null)
    {
        $this->message = $message;
		$this->url = $curlInfo['url'];
        $this->setOptions($options);
        $this->setResponse($response);
		$this->info = $curlInfo;
    }

    function getInfo()
    {
        return $this->info;
    }

    /**
     * @return string
     */
    function getUrl()
    {
        return $this->url;
    }

    /**
     * @return array
     */
    function getOptions()
    {
        return $this->options;
    }

    /**
     *
     * @return mixed
     */
    function getResponse()
    {
        return $this->response;
    }

    private function setOptions(array $options)
    {
        if (isset($options[CURLOPT_POSTFIELDS])) {
            $options[CURLOPT_POSTFIELDS] = json_decode($options[CURLOPT_POSTFIELDS]);
        }
        $this->options = $this->translateCurlOptionKeys($options);
    }

    private function setResponse($response)
    {

        if (isset($response['data']) AND is_string($response['data'])) $response['data'] = json_decode($response['data']);
        $this->response = $response;
    }

    /**
	 * Translates keys for CURL configuration from constant's values to name of constant.
	 * It's designed to simplify readability to search for errors
     * @param array $options
     * @return array
     */
    private function translateCurlOptionKeys(array $options)
    {
        $const = get_defined_constants(true)['curl'];
        $newOptions = array();
        foreach ($options as $key => $value) {
            $name = array_search($key, $const);
            if ($name === false) {
                $newOptions[$key] = $value;
            } else {
                $newOptions[$name] = $value;
            }
        }
        return $newOptions;
    }

}
