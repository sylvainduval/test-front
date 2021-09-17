<?php
namespace App\Domain;

use GuzzleHttp\RequestOptions;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ArticleDAO extends AbstractDAO
{
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

	public function insert(ArticleDO $articleDO): ArticleDO
	{
		try {
			$record = $this->request(
				$this->apiEndpoint . '/articles',
				[RequestOptions::JSON => [
					'title' => $articleDO->getTitle(),
					'body' => $articleDO->getBody(),
					'createdBy' => $articleDO->getCreatedBy(),
					'leadingTitle' => $articleDO->getLeading(),
				]],
				'post'
			);

			return $this->buildDomainObjectFromQueryResult($record);
		} catch (Exception $exception) {
			throw new BadRequestException($exception->getMessage());
		}
	}

	public function delete(ArticleDO $articleDO): void
	{
		try {
			$this->request(
				$this->apiEndpoint . '/articles/' . $articleDO->getSlug(),
				[],
				'delete'
			);

			return;
		} catch (Exception $exception) {
			throw new BadRequestException($exception->getMessage());
		}
	}

	protected function buildDomainObjectFromQueryResult(array $data): ArticleDO
	{
		$queryDomainObject = new ArticleDO();
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