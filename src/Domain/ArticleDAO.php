<?php

namespace App\Domain;

use GuzzleHttp\RequestOptions;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use App\Entity\Article;

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

	public function find(string $slug): ?Article
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

	public function insert(Article $article): Article
	{
		try {
			$record = $this->request(
				$this->apiEndpoint . '/articles',
				[RequestOptions::JSON => [
					'title' => $article->getTitle(),
					'body' => $article->getBody(),
					'createdBy' => $article->getCreatedBy(),
					'leadingTitle' => $article->getLeading(),
				]],
				'post'
			);

			return $this->buildDomainObjectFromQueryResult($record);
		} catch (Exception $exception) {
			throw new BadRequestException($exception->getMessage());
		}
	}

	public function delete(Article $article): void
	{
		try {
			$this->request(
				$this->apiEndpoint . '/articles/' . $article->getSlug(),
				[],
				'delete'
			);

			return;
		} catch (Exception $exception) {
			throw new BadRequestException($exception->getMessage());
		}
	}

	protected function buildDomainObjectFromQueryResult(array $data): Article
	{
		$queryDomainObject = new Article();
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