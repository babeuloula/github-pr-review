<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enum\NotificationReason;
use App\Enum\NotificationType;

final class Notification
{
    private int $id;
    private bool $unread;
    private NotificationReason $reason;
    private \DateTimeImmutable $updatedAt;
    private ?\DateTimeImmutable $lastReadAt;
    private string $subject;
    private NotificationType $type;
    private string $repository;
    private string $url;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->unread = $data['unread'];
        $this->reason = NotificationReason::from($data['reason']);
        $this->updatedAt = new \DateTimeImmutable($data['updated_at']);
        $this->lastReadAt = (true === \is_string($data['last_read_at']))
            ? new \DateTimeImmutable($data['last_read_at'])
            : null
        ;
        $this->subject = $data['subject']['title'];
        $this->type = NotificationType::from($data['subject']['type']);
        $this->repository = $data['repository']['full_name'];
        $this->setUrl($data['subject']['url']);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isUnread(): bool
    {
        return $this->unread;
    }

    public function getReason(): NotificationReason
    {
        return $this->reason;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getLastReadAt(): ?\DateTimeImmutable
    {
        return $this->lastReadAt;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getType(): NotificationType
    {
        return $this->type;
    }

    public function getRepository(): string
    {
        return $this->repository;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $this->formatUrl($this->type, $url);

        return $this;
    }

    private function formatUrl(NotificationType $type, string $url): string
    {
        return match ($type) {
            NotificationType::ISSUE => \str_replace(
                ['https://api.github.com/repos/'],
                ['https://github.com/'],
                $url
            ),
            NotificationType::PULL_REQUEST => \str_replace(
                ['https://api.github.com/repos/', '/pulls/'],
                ['https://github.com/', '/pull/'],
                $url
            ),
            default => throw new \InvalidArgumentException(
                'Unexpected match value for Notification Type:' . $type->value
            ),
        };
    }
}
