<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\{
    Mapping as ORM,
    Mapping\OneToOne
};

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
final class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $githubToken;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @var Configuration|null
     * @OneToOne(targetEntity="Configuration", mappedBy="user", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $configuration;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGithubToken(): ?string
    {
        return $this->githubToken;
    }

    public function setGithubToken(string $githubToken): self
    {
        $this->githubToken = $githubToken;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getConfiguration(): ?Configuration
    {
        return $this->configuration;
    }

    public function setConfiguration(?Configuration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }
}
