<?php
/**
 * @author BaBeuloula <info@babeuloula.fr>
 */
declare(strict_types=1);

namespace App\TypedArray\Type;

class PullRequest
{
    /** @var string */
    protected $url;

    /** @var int */
    protected $number;

    /** @var string */
    protected $title;

    /** @var User */
    protected $user;

    /** @var \DateTimeImmutable */
    protected $createdAt;

    /** @var \DateTimeImmutable */
    protected $updatedAt;

    /** @var string */
    protected $head;

    /** @var string */
    protected $base;

    /** @var string */
    protected $branchColor;

    public function __construct(array $data)
    {
        $this->url = $data['html_url'];
        $this->number = $data['number'];
        $this->title = $data['title'];
        $this->user = new User($data['user']);
        $this->createdAt = new \DateTimeImmutable($data['created_at']);
        $this->updatedAt = new \DateTimeImmutable($data['updated_at']);
        $this->head = $data['head']['ref'];
        $this->base = $data['base']['ref'];
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

    public function getHead(): string
    {
        return $this->head;
    }

    public function getBase(): string
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
