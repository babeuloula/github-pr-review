<?php

declare(strict_types=1);

namespace App\Dto;

final class PullRequest
{
    private string $url;
    private int $number;
    private string $title;
    private User $user;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;
    private ?string $head;
    private ?string $base;
    private string $branchColor;

    public function __construct(array $data)
    {
        $this->url = $data['html_url'];
        $this->number = $data['number'];
        $this->title = $data['title'];
        $this->user = new User($data['user']);
        $this->createdAt = new \DateTimeImmutable($data['created_at']);
        $this->updatedAt = new \DateTimeImmutable($data['updated_at']);
        $this->head = (true === \array_key_exists('head', $data)) ? $data['head']['ref'] : null;
        $this->base = (true === \array_key_exists('base', $data)) ? $data['base']['ref'] : null;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getHead(): ?string
    {
        return $this->head;
    }

    public function getBase(): ?string
    {
        return $this->base;
    }

    public function getBranchColor(): string
    {
        return $this->branchColor;
    }

    public function setBranchColor(string $branchColor): self
    {
        $this->branchColor = $branchColor;

        return $this;
    }
}
