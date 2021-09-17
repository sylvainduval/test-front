<?php
namespace App\Domain;

use Exception;
use GuzzleHttp\Client;

abstract class AbstractDAO
{
	protected $client;
	protected $apiEndpoint;

	abstract protected function buildDomainObjectFromQueryResult(array $data);

	public function __construct($apiEndpoint)
	{
		$this->client = new Client();
		$this->apiEndpoint = $apiEndpoint;
	}

	protected function request($url, $body, $method): ?array
	{
		try {
			return json_decode($this->client->{$method}($url, $body)->getBody()->getContents(), true);
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), 1, $e);
		}
	}

	protected function getClassFieldsMappingKeys($class = null): array
	{
		if (!empty($class)) {
			return array_keys($class->getFieldsMapping());
		}

		return array_keys($this->getFieldsMapping());
	}
}