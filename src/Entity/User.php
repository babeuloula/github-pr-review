<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\UseMode;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    protected const DEFAULT_ROLE = 'ROLE_USER';
    protected const ROLE_LABEL = 'ROLE_LABEL';
    protected const ROLE_FILER = 'ROLE_FILER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    protected int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    protected string $username;

    /** @var string[] */
    #[ORM\Column(type: 'json')]
    protected array $roles = [];

    #[ORM\Column(type: 'string', length: 255)]
    protected string $token;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $name;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    protected bool $enabled;

    #[ORM\OneToOne(targetEntity: Configuration::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    protected Configuration $configuration;

    public function __construct()
    {
        $this->setEnabled(true);
        $this->setRoles([self::DEFAULT_ROLE]);
        $this->setConfiguration(new Configuration());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::DEFAULT_ROLE;
        $roles[] = match ($this->getConfiguration()->getMode()) {
            UseMode::FILTER => self::ROLE_FILER,
            UseMode::LABEL => self::ROLE_LABEL,
        };

        return array_unique($roles);
    }

    /** @param string[] $roles */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function setConfiguration(Configuration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }
}
