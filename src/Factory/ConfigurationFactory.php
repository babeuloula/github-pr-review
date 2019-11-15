<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Configuration;
use App\Enum\Color;
use Symfony\Component\HttpFoundation\Request;

class ConfigurationFactory
{
    public const DEFAULT_RELOAD_EVERY = 60;

    public function createDefault(): Configuration
    {
        return (new Configuration())
            ->setMode('label')
            ->setBranchDefaultColor((string) Color::PRIMARY())
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
            ->setMode($request->request->get('mode', 'label'))
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
            ->setBranchDefaultColor($request->request->get('branch_default_color', 'primary'))
            ->setFilters($request->request->get('filters', []))
            ->setNotificationsExcludeReasons($request->request->get('notifications_exclude_reasons', []))
            ->setNotificationsExcludeReasonsOtherRepos(
                $request->request->get('notifications_exclude_reasons_other_repos', [])
            )
            ->setEnabledDarkTheme('on' === $request->request->get('enabled_dark_theme', 'off'))
            ->setReloadOnFocus('on' === $request->request->get('reload_on_focus', 'off'))
            ->setReloadEvery($request->request->getInt('reload_every', 60))
        ;
    }
}
