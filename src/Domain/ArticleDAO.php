<?php
namespace App\Domain;

use GuzzleHttp\RequestOptions;
use Exception;

class ArticleDAO extends AbstractDAO
{
	private $articleDO;

	public function __construct(ArticleDO $articleDO, $apiEndpoint)
	{
		$this->articleDO = $articleDO;

		parent::__construct($apiEndpoint);
	}

	public function findAll(int $page): array
	{
		$data = $this->request(
			$this->apiEndpoint . '/articles',
			[RequestOptions::JSON => [
				'page' => $page
			]],
			'get'
		);

		$collection = [];
		foreach ($data['hydra:member'] as $record) {
			$collection[] = $this->buildDomainObjectFromQueryResult($record);
		}

		return $collection;
	}

	public function find(string $slug): ?ArticleDO
	{
		try {
			$record = $this->request(
				$this->apiEndpoint . '/articles/' . $slug,
				[],
				'get'
			);
		} catch (Exception $exception) {
			return null;
		}

		return $this->buildDomainObjectFromQueryResult($record);
	}

	private function buildDomainObjectFromQueryResult(array $data): ArticleDO
	{
		/** @var ArticleDO $queryDomainObject */
		$queryDomainObject = new $this->articleDO();
		$queryDomainObject->setHydraId($data['@id']);
		$queryDomainObject->setId($data['id']);
		$queryDomainObject->setTitle($data['title']);
		$queryDomainObject->setSlug($data['slug']);
		$queryDomainObject->setLeading($data['leading_title']);
		$queryDomainObject->setBody($data['body']);
		$queryDomainObject->setCreatedAt($data['createdAt']);
		$queryDomainObject->setCreatedBy($data['createdBy']);

		return $queryDomainObject;
	}
}