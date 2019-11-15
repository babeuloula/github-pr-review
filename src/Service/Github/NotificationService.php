<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Service\Github;

use App\Entity\Configuration;
use App\Entity\User;
use App\Enum\NotificationReason;
use App\Enum\NotificationType;
use App\Service\User\UserService;
use App\TypedArray\NotificationArray;
use App\TypedArray\Type\Notification;
use Github\Api\Notification as NotificationApi;
use Github\Client;

class NotificationService
{
    protected const OTHER_REPOS = 'Other repos';

    /** @var Client */
    protected $client;

    /** @var string[] */
    protected $githubRepos;

    /** @var string[] */
    protected $githubNotificationsExcludeReasons;

    /** @var string[] */
    protected $githubNotificationsExcludeReasonsOtherRepos;

    /** @var int[] */
    protected $notificationsCount = [];

    public function __construct(GithubClientService $client, UserService $userService)
    {
        if (false === $userService->getUser() instanceof User
            || false === $userService->getUser()->getConfiguration() instanceof Configuration
        ) {
            return;
        }

        $this->client = $client->getClient();
        $this->githubRepos = $userService->getUser()->getConfiguration()->getRepositories();
        \natcasesort($this->githubRepos);

        $this->githubNotificationsExcludeReasons = array_map(
            function (NotificationReason $reason): string {
                return (string) $reason;
            },
            $userService
                ->getUser()
                ->getConfiguration()
                ->getNotificationsExcludeReasons()
        );
        $this->githubNotificationsExcludeReasonsOtherRepos = array_map(
            function (NotificationReason $reason): string {
                return (string) $reason;
            },
            $userService
                ->getUser()
                ->getConfiguration()
                ->getNotificationsExcludeReasonsOtherRepos()
        );

        foreach ($this->githubRepos as $repo) {
            $this->notificationsCount[$repo] = 0;
        }

        $this->notificationsCount[static::OTHER_REPOS] = 0;
    }

    /** @return array[] */
    public function getNotifications(): array
    {
        /** @var NotificationApi $notificationsApi */
        $notificationsApi = $this->client->api('notifications');
        $reasons = \array_filter(
            \array_values(NotificationReason::toArray()),
            function (string $reason): bool {
                return false === \in_array($reason, $this->githubNotificationsExcludeReasons);
            }
        );
        $reasonsOtherRepos = \array_filter(
            \array_values(NotificationReason::toArray()),
            function (string $reason): bool {
                return false === \in_array($reason, $this->githubNotificationsExcludeReasonsOtherRepos);
            }
        );
        $notificationsOrdered = [];

        foreach ($this->githubRepos as $repo) {
            foreach ($reasons as $reason) {
                $notificationsOrdered[$repo][$reason] = new NotificationArray();
            }
        }

        $notificationsOrdered[static::OTHER_REPOS] = [];

        foreach ($reasonsOtherRepos as $reason) {
            $notificationsOrdered[static::OTHER_REPOS][$reason] = new NotificationArray();
        }

        foreach ($notificationsApi->all() as $notification) {
            $repo = (true === \array_key_exists($notification['repository']['full_name'], $notificationsOrdered))
                ? $notification['repository']['full_name']
                : static::OTHER_REPOS;
            $reason = $notification['reason'];

            if (true === \array_key_exists($reason, $notificationsOrdered[$repo])) {
                $notification = new Notification($notification);

                $notification->setUrl(
                    $this->formatUrl($notification->getType(), $notification->getUrl())
                );

                $notificationsOrdered[$repo][$reason][] = $notification;
                ++$this->notificationsCount[$repo];
            }
        }

        return $notificationsOrdered;
    }

    /** @return int[] */
    public function getNotificationsCount(): array
    {
        return $this->notificationsCount;
    }

    public function markAsRead(int $threadId): bool
    {
        /** @var NotificationApi $notificationsApi */
        $notificationsApi = $this->client->api('notifications');

        try {
            $notificationsApi->markThreadRead($threadId);
        } catch (\Throwable $exception) {
            return false;
        }

        return true;
    }

    protected function formatUrl(NotificationType $type, string $url): string
    {
        switch ($type) {
            case NotificationType::ISSUE():
                $url = \str_replace(
                    ['https://api.github.com/repos/'],
                    ['https://github.com/'],
                    $url
                );
                break;

            case NotificationType::PULL_REQUEST():
                $url = \str_replace(
                    ['https://api.github.com/repos/', '/pulls/'],
                    ['https://github.com/', '/pull/'],
                    $url
                );
                break;
        }

        return $url;
    }
}
