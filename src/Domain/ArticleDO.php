<?php
namespace App\Domain;

class ArticleDO
{
	private $hydraId;
	private $id;
	private $slug;
	private $title;
	private $body;
	private $leading;
	private $createdAt;
	private $createdBy;

	public function getHydraId(): ?string
	{
		return $this->hydraId;
	}

	public function setHydraId(?string $hydraId): void
	{
		$this->hydraId = $hydraId;
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function setId(?int $id): void
	{
		$this->id = $id;
	}

	public function getSlug(): ?string
	{
		return $this->slug;
	}

	public function setSlug(?string $slug): void
	{
		$this->slug = $slug;
	}

	public function getTitle(): ?string
	{
		return $this->title;
	}

	public function setTitle(?string $title): void
	{
		$this->title = $title;
	}

	public function getBody(): ?string
	{
		return $this->body;
	}

	public function setBody(?string $body): void
	{
		$this->body = $body;
	}

	public function getLeading(): ?string
	{
		return $this->leading;
	}

	public function setLeading(?string $leading): void
	{
		$this->leading = $leading;
	}

	public function getCreatedAt(): ?string
	{
		return $this->createdAt;
	}

	public function setCreatedAt(?string $createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	public function getCreatedBy(): ?string
	{
		return $this->createdBy;
	}

	public function setCreatedBy(?string $createdBy): void
	{
		$this->createdBy = $createdBy;
	}
}