<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\TypedArray\Type;

use App\Enum\NotificationReason;
use App\Enum\NotificationType;

class Notification
{
    /** @var int */
    protected $id;

    /** @var bool */
    protected $unread;

    /** @var NotificationReason */
    protected $reason;

    /** @var \DateTimeImmutable */
    protected $updatedAt;

    /** @var ?\DateTimeImmutable */
    protected $lastReadAt;

    /** @var string */
    protected $subject;

    /** @var NotificationType */
    protected $type;

    /** @var string */
    protected $repository;

    /** @var string */
    protected $url;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->unread = $data['unread'];
        $this->reason = new NotificationReason($data['reason']);
        $this->updatedAt = new \DateTimeImmutable($data['updated_at']);
        $this->lastReadAt = (true === \is_string($data['last_read_at']))
            ? new \DateTimeImmutable($data['last_read_at'])
            : null;
        $this->subject = $data['subject']['title'];
        $this->type = new NotificationType($data['subject']['type']);
        $this->repository = $data['repository']['full_name'];
        $this->url = $data['subject']['url'];
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
        $this->url = $url;

        return $this;
    }
}
