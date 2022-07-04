<?php

declare(strict_types=1);

namespace App\Dto;

class User
{
    /** @var string */
    protected $login;

    /** @var string */
    protected $avatarUrl;

    public function __construct(array $data)
    {
        $this->login = $data['login'];
        $this->avatarUrl = $data['avatar_url'];
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }
}
