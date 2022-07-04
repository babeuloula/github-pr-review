<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Configuration;
use App\Enum\Color;
use App\Enum\NotificationReason;
use App\Enum\UseMode;
use Symfony\Component\HttpFoundation\Request;

class ConfigurationFactory
{
    public static function createFromRequest(Request $request, Configuration $configuration): Configuration
    {
        /** @var array[] $data */
        $data = $request->request->all();

        return $configuration
            ->setRepositories($data['repositories'] ?? [])
            ->setMode(
                UseMode::from(
                    $request->request->get('mode', UseMode::default()->value) // @phpstan-ignore-line
                )
            )
            ->setLabelsReviewNeeded($data['labels_review_needed'] ?? [])
            ->setLabelsChangesRequested($data['labels_changes_requested'] ?? [])
            ->setLabelsAccepted($data['labels_accepted'] ?? [])
            ->setLabelsWip($data['labels_wip'] ?? [])
            ->setBranchesColors(
                array_map(
                    /** @return array<string, string> */
                    static function (string $data): array {
                        // phpcs:ignore
                        [$branch, $color] = explode(':', $data);

                        try {
                            $color = Color::from($color);
                        } catch (\Throwable) {
                            $color = Color::default();
                        }

                        return [$branch, $color->value];
                    },
                    $data['branchs_colors'] ?? []
                )
            )
            ->setBranchDefaultColor(
                Color::from(
                    $request->request->get('branch_default_color', Color::default()->value) // @phpstan-ignore-line
                )
            )
            ->setFilters($data['filters'] ?? [])
            ->setNotificationsExcludeReasons(
                array_map(
                    static function (string $reason): string {
                        return NotificationReason::from($reason)->value;
                    },
                    $data['notifications_exclude_reasons'] ?? []
                )
            )
            ->setNotificationsExcludeReasonsOtherRepos(
                array_map(
                    static function (string $reason): string {
                        return NotificationReason::from($reason)->value;
                    },
                    $data['notifications_exclude_reasons_other_repos'] ?? []
                )
            )
            ->setEnabledDarkTheme('on' === $request->request->get('enabled_dark_theme', 'off'))
            ->setReloadOnFocus('on' === $request->request->get('reload_on_focus', 'off'))
            ->setReloadEvery($request->request->getInt('reload_every', (new Configuration())->getReloadEvery()))
        ;
    }
}
