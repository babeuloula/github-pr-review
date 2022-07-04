<?php

declare(strict_types=1);

namespace App\Service\Github;

use App\Dto\Notification;
use App\Entity\User;
use App\Enum\NotificationReason;
use App\Service\User\UserService;
use Github\Api\Notification as NotificationApi;
use Github\Client;

final class NotificationService
{
    private const OTHER_REPOS = 'Other repos';

    private Client $client;

    /** @var string[] */
    private array $githubRepos;

    /** @var string[] */
    private array $githubNotificationsExcludeReasons;

    /** @var string[] */
    private array $githubNotificationsExcludeReasonsOtherRepos;

    /** @var int[] */
    private array $notificationsCount = [];

    public function __construct(GithubClientService $client, UserService $userService)
    {
        if (false === $userService->getUser() instanceof User) {
            return;
        }

        $this->client = $client->getClient();
        $this->githubRepos = $userService->getUser()->getConfiguration()->getRepositories();
        \natcasesort($this->githubRepos);

        $this->githubNotificationsExcludeReasons = $userService
            ->getUser()
            ->getConfiguration()
            ->getNotificationsExcludeReasons()
        ;
        $this->githubNotificationsExcludeReasonsOtherRepos = $userService
            ->getUser()
            ->getConfiguration()
            ->getNotificationsExcludeReasonsOtherRepos()
        ;

        foreach ($this->githubRepos as $repo) {
            $this->notificationsCount[$repo] = 0;
        }

        $this->notificationsCount[self::OTHER_REPOS] = 0;
    }

    /** @return array[] */
    public function getNotifications(): array
    {
        /** @var NotificationApi $notificationsApi */
        $notificationsApi = $this->client->api('notifications');
        /** @var NotificationReason[] $reasons */
        $reasons = \array_filter(
            \array_values(NotificationReason::cases()),
            function (NotificationReason $reason): bool {
                return false === \in_array($reason->value, $this->githubNotificationsExcludeReasons, true);
            }
        );
        /** @var NotificationReason[] $reasonsOtherRepos */
        $reasonsOtherRepos = \array_filter(
            \array_values(NotificationReason::cases()),
            function (NotificationReason $reason): bool {
                return false === \in_array($reason->value, $this->githubNotificationsExcludeReasonsOtherRepos, true);
            }
        );
        $notificationsOrdered = [];

        foreach ($this->githubRepos as $repo) {
            foreach ($reasons as $reason) {
                $notificationsOrdered[$repo][$reason->value] = [];
            }
        }

        $notificationsOrdered[self::OTHER_REPOS] = [];

        foreach ($reasonsOtherRepos as $reason) {
            $notificationsOrdered[self::OTHER_REPOS][$reason->value] = [];
        }

        foreach ($notificationsApi->all() as $notification) {
            $repo = (true === \array_key_exists($notification['repository']['full_name'], $notificationsOrdered))
                ? $notification['repository']['full_name']
                : self::OTHER_REPOS;
            $reason = $notification['reason'];

            if (true === \array_key_exists($reason, $notificationsOrdered[$repo])) {
                $notification = new Notification($notification);

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
}
