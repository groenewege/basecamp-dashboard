<?php

namespace Basecamp;

use Basecamp\DummyLogger;
use Basecamp\BasecampException;
use Basecamp\BasecampEntity;
use Basecamp\BasecampEventIterator;
use Basecamp\BasecampEvent;


/**
 * Basecamp API Wrapper
 *
 * provides easy access to the event function of the Basecamp API.
 *
 * @see https://github.com/37signals/bcx-api/blob/master/sections/events.md
 * @author Gunther Groenewege <gunther@groenewege.com>
 * @based_upon : https://github.com/bdunlap/basecamp.php
 */
class Basecamp
{

	protected $logger = null;
	protected $baseUrl = null;
	protected $credentials = null;
	protected $helloHeader = null;

	function __construct($appName, $appContact, $basecampAccountId, $basecampUsername,
						 $basecampPassword, $logger = NULL)
	{
		if (is_null($logger)) {
			$this->logger = new DummyLogger;
		}

		$this->baseUrl = "https://basecamp.com/$basecampAccountId/api/v1";
		$this->credentials = "$basecampUsername:$basecampPassword";
		$this->helloHeader = "User-Agent: $appName ($appContact)";
	}

	/**
	 * Retrieve the 50 last events of the Basecamp account
	 * @return BasecampEvent
	 */
	public function getEvents()
	{
		return $this->getEntities(
			'/events.json',
			'BasecampEventIterator',
			'BasecampEvent'
		);
	}

	/**
	 * Generic method to fetch different types of entities of the mite API
	 *
	 * @param string $endpoint			API url
	 * @param string $iterator			Classname of the returned iterator class
	 * @param string $entity_class		Classname of intatiated entity type
	 * @param string $property			Property name of returned API response
	 */
	private function getEntities($endpoint, $iterator = 'ArrayIterator', $entity_class = 'BasecampEntity')
	{
		try
		{
			$response = $this->callApi('GET', $endpoint);

			if (!is_array($response))
			{
				throw new BasecampException(__FUNCTION__.'(): No response from API interface.');
			}

			$iterator = '\\Basecamp\\'.$iterator;
			$entity_class = '\\Basecamp\\'.$entity_class;
			$entities = new $iterator();
			foreach ($response as $r)
			{
				$entities->append(new $entity_class($r));
			}

			return $entities;
		}
		catch (\Exception $e)
		{
			throw new BasecampException($e->getMessage());
		}
	}

	private function callApi($method, $path, $params=array(), $response_headers=array())
	{
		$url = $this->baseUrl.'/'.ltrim($path, '/');

		$query = in_array($method, array('GET','DELETE')) ? $params : array();

		$payload = in_array($method, array('POST','PUT')) ? stripslashes(json_encode($params)) : array();

		$request_headers = in_array($method, array('POST','PUT')) ? array("Content-Type: application/json; charset=utf-8", 'Expect:') : array();
		$request_headers[] = $this->helloHeader;

		$this->logger->debug("About to send API request:\n"
			.print_r(compact('method', 'url', 'query',
				'payload', 'request_headers'), 1));

		$response = $this->curl_http_api_request_($method, $url, $this->credentials, $query, $payload, $request_headers, $response_headers);

		$statusCode = $response_headers['http_status_code'];
		if ($statusCode >= 400) {
			throw new \Exception("HTTP error $statusCode:\n"
				.print_r(compact('method', 'url', 'query',
					'payload', 'request_headers',
					'response_headers', 'response',
					'shops_myshopify_domain',
					'shops_token'), 1));
		}

		return json_decode($response, true);
	}

	private function curl_http_api_request_($method, $url, $credentials, $query='', $payload='', $request_headers=array(), &$response_headers=array())
	{
		$url = $this->curl_append_query_($url, $query);
		$ch = curl_init($url);
		$this->curl_setopts_($ch, $credentials, $method, $payload, $request_headers);
		$response = curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		curl_close($ch);

		if ($errno) throw new \Exception("cUrl error: $error", $errno);

		list($message_headers, $message_body) = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);
		$response_headers = $this->curl_parse_headers_($message_headers);

		return $message_body;
	}

	private function curl_append_query_($url, $query)
	{
		if (empty($query)) return $url;
		if (is_array($query)) return "$url?".http_build_query($query);
		else return "$url?$query";
	}

	private function curl_setopts_($ch, $credentials, $method, $payload, $request_headers)
	{
		curl_setopt($ch, CURLOPT_USERPWD, $credentials);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		if ('GET' == $method)
		{
			curl_setopt($ch, CURLOPT_HTTPGET, true);
		}
		else
		{
			curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $method);
			if (!empty($payload))
			{
				if (is_array($payload)) $payload = http_build_query($payload);
				curl_setopt ($ch, CURLOPT_POSTFIELDS, $payload);
			}
		}
		if (!empty($request_headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
	}

	private function curl_parse_headers_($message_headers)
	{
		$header_lines = preg_split("/\r\n|\n|\r/", $message_headers);
		$headers = array();
		list(, $headers['http_status_code'], $headers['http_status_message']) = explode(' ', trim(array_shift($header_lines)), 3);
		foreach ($header_lines as $header_line)
		{
			list($name, $value) = explode(':', $header_line, 2);
			$name = strtolower($name);
			$headers[$name] = trim($value);
		}

		return $headers;
	}

}

spl_autoload_register(function($class) {
	$file = implode(DIRECTORY_SEPARATOR, explode('\\', ltrim($class, '\\')));
	$path = dirname(__DIR__) . DIRECTORY_SEPARATOR . $file . '.php';
	if(file_exists($path)) include $path;
	else throw new \Exception ('Can\'t load needed library "' . $path . '"');
});

