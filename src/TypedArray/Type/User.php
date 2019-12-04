<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\TypedArray\Type;

class User
{
    protected string $login;

    protected string $avatarUrl;

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
