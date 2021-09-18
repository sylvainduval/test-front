<?php

namespace App\Domain;

use Exception;
use GuzzleHttp\Client;

abstract class AbstractDAO
{
	protected $client;
	protected $apiEndpoint;

	abstract protected function buildDomainObjectFromQueryResult(array $data);

	/**
	 * @param string $apiEndpoint
	 *
	 * @return void
	 */
	public function __construct(string $apiEndpoint)
	{
		$this->client = new Client();
		$this->apiEndpoint = $apiEndpoint;
	}

	/**
	 * @param string $url
	 * @param array $body
	 * @param string $method
	 *
	 * @return array|null
	 *
	 * @throws Exception
	 */
	protected function request(string $url, array $body, string $method): ?array
	{
		try {
			return json_decode($this->client->{$method}($url, $body)->getBody()->getContents(), true);
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), 1, $e);
		}
	}
}