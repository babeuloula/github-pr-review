<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Configuration;
use App\Enum\Color;
use App\Enum\NotificationReason;
use App\Enum\UseMode;
use Symfony\Component\HttpFoundation\Request;

class ConfigurationFactory
{
    public const DEFAULT_RELOAD_EVERY = 60;

    public function createDefault(): Configuration
    {
        return (new Configuration())
            ->setMode(UseMode::getDefault())
            ->setBranchDefaultColor(Color::getDefault())
            ->setEnabledDarkTheme(false)
            ->setReloadOnFocus(false)
            ->setReloadEvery(static::DEFAULT_RELOAD_EVERY)
        ;
    }

    public function createFromRequest(Request $request, ?Configuration $previousConfiguration): Configuration
    {
        $configuration = new Configuration();

        if ($previousConfiguration instanceof Configuration) {
            $configuration = $previousConfiguration;
        }

        return $configuration
            ->setRepositories($request->request->get('repositories', []))
            ->setMode(
                new UseMode(
                    $request->request->get(
                        'mode',
                        (string) UseMode::getDefault()
                    )
                )
            )
            ->setLabelsReviewNeeded($request->request->get('labels_review_needed', []))
            ->setLabelsChangesRequested($request->request->get('labels_changes_requested', []))
            ->setLabelsAccepted($request->request->get('labels_accepted', []))
            ->setLabelsWip($request->request->get('labels_wip', []))
            ->setBranchsColors(
                array_map(
                    /** @return string[] : [branch => color] */
                    function (string $data): array {
                        return explode(':', $data);
                    },
                    $request->request->get('branchs_colors', [])
                )
            )
            ->setBranchDefaultColor(
                new Color(
                    $request->request->get(
                        'branch_default_color',
                        (string) Color::getDefault()
                    )
                )
            )
            ->setFilters($request->request->get('filters', []))
            ->setNotificationsExcludeReasons(
                array_map(
                    function (string $reason): NotificationReason {
                        return new NotificationReason($reason);
                    },
                    $request->request->get('notifications_exclude_reasons', [])
                )
            )
            ->setNotificationsExcludeReasonsOtherRepos(
                array_map(
                    function (string $reason): NotificationReason {
                        return new NotificationReason($reason);
                    },
                    $request->request->get('notifications_exclude_reasons_other_repos', [])
                )
            )
            ->setEnabledDarkTheme('on' === $request->request->get('enabled_dark_theme', 'off'))
            ->setReloadOnFocus('on' === $request->request->get('reload_on_focus', 'off'))
            ->setReloadEvery($request->request->getInt('reload_every'))
        ;
    }
}
