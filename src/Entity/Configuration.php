<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Color;
use App\Enum\UseMode;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigurationRepository::class)]
class Configuration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    protected int $id;

    /** @var string[] */
    #[ORM\Column(type: 'json')]
    protected array $repositories;

    #[ORM\Column(type: 'enum_use_mode', length: 25)]
    protected UseMode $mode;

    /** @var string[] */
    #[ORM\Column(type: 'json')]
    protected array $labelsReviewNeeded;

    /** @var string[] */
    #[ORM\Column(type: 'json')]
    protected array $labelsChangesRequested;

    /** @var string[] */
    #[ORM\Column(type: 'json')]
    protected array $labelsAccepted;

    /** @var string[] */
    #[ORM\Column(type: 'json')]
    protected array $labelsWip;

    #[ORM\Column(type: 'json')]
    protected array $branchesColors;

    #[ORM\Column(type: 'enum_color', length: 25)]
    protected Color $branchDefaultColor;

    /** @var string[] */
    #[ORM\Column(type: 'json')]
    protected array $filters;

    /** @var string[] */
    #[ORM\Column(type: 'json')]
    protected array $notificationsExcludeReasons;

    /** @var string[] */
    #[ORM\Column(type: 'json')]
    protected array $notificationsExcludeReasonsOtherRepos;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    protected bool $enabledDarkTheme;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    protected bool $reloadOnFocus;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true, 'default' => 60])]
    protected int $reloadEvery;

    public function __construct()
    {
        $this->repositories = [];
        $this->mode = UseMode::default();
        $this->labelsReviewNeeded = [];
        $this->labelsChangesRequested = [];
        $this->labelsAccepted = [];
        $this->labelsWip = [];
        $this->branchesColors = [];
        $this->branchDefaultColor = Color::default();
        $this->filters = [];
        $this->notificationsExcludeReasons = [];
        $this->notificationsExcludeReasonsOtherRepos = [];
        $this->enabledDarkTheme = false;
        $this->reloadOnFocus = false;
        $this->reloadEvery = 60;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /** @return string[] */
    public function getRepositories(): array
    {
        return $this->repositories;
    }

    /** @param string[] $repositories */
    public function setRepositories(array $repositories): self
    {
        $this->repositories = $repositories;

        return $this;
    }

    public function getMode(): UseMode
    {
        return $this->mode;
    }

    public function setMode(UseMode $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    /** @return string[] */
    public function getLabelsReviewNeeded(): array
    {
        return $this->labelsReviewNeeded;
    }

    /** @param string[] $labelsReviewNeeded */
    public function setLabelsReviewNeeded(array $labelsReviewNeeded): self
    {
        $this->labelsReviewNeeded = $labelsReviewNeeded;

        return $this;
    }

    /** @return string[] */
    public function getLabelsChangesRequested(): array
    {
        return $this->labelsChangesRequested;
    }

    /** @param string[] $labelsChangesRequested */
    public function setLabelsChangesRequested(array $labelsChangesRequested): self
    {
        $this->labelsChangesRequested = $labelsChangesRequested;

        return $this;
    }

    /** @return string[] */
    public function getLabelsAccepted(): array
    {
        return $this->labelsAccepted;
    }

    /** @param string[] $labelsAccepted */
    public function setLabelsAccepted(array $labelsAccepted): self
    {
        $this->labelsAccepted = $labelsAccepted;

        return $this;
    }

    /** @return string[] */
    public function getLabelsWip(): array
    {
        return $this->labelsWip;
    }

    /** @param string[] $labelsWip */
    public function setLabelsWip(array $labelsWip): self
    {
        $this->labelsWip = $labelsWip;

        return $this;
    }

    /** @return array<string, string> */
    public function getBranchesColors(): array
    {
        return $this->branchesColors;
    }

    public function setBranchesColors(array $branchesColors): self
    {
        $this->branchesColors = $branchesColors;

        return $this;
    }

    public function getBranchDefaultColor(): Color
    {
        return $this->branchDefaultColor;
    }

    public function setBranchDefaultColor(Color $branchDefaultColor): self
    {
        $this->branchDefaultColor = $branchDefaultColor;

        return $this;
    }

    /** @return string[] */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /** @param string[] $filters */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    /** @return string[] */
    public function getNotificationsExcludeReasons(): array
    {
        return $this->notificationsExcludeReasons;
    }

    /** @param string[] $notificationsExcludeReasons */
    public function setNotificationsExcludeReasons(array $notificationsExcludeReasons): self
    {
        $this->notificationsExcludeReasons = $notificationsExcludeReasons;

        return $this;
    }

    /** @return string[] */
    public function getNotificationsExcludeReasonsOtherRepos(): array
    {
        return $this->notificationsExcludeReasonsOtherRepos;
    }

    /** @param string[] $notificationsExcludeReasonsOtherRepos */
    public function setNotificationsExcludeReasonsOtherRepos(array $notificationsExcludeReasonsOtherRepos): self
    {
        $this->notificationsExcludeReasonsOtherRepos = $notificationsExcludeReasonsOtherRepos;

        return $this;
    }

    public function isEnabledDarkTheme(): bool
    {
        return $this->enabledDarkTheme;
    }

    public function setEnabledDarkTheme(bool $enabledDarkTheme): self
    {
        $this->enabledDarkTheme = $enabledDarkTheme;

        return $this;
    }

    public function isReloadOnFocus(): bool
    {
        return $this->reloadOnFocus;
    }

    public function setReloadOnFocus(bool $reloadOnFocus): self
    {
        $this->reloadOnFocus = $reloadOnFocus;

        return $this;
    }

    public function getReloadEvery(): int
    {
        return $this->reloadEvery;
    }

    public function setReloadEvery(int $reloadEvery): self
    {
        $this->reloadEvery = $reloadEvery;

        return $this;
    }
}
