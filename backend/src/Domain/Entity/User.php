<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\Uid\Uuid;

class User
{
    private Uuid $id;
    private string $username;
    private string $email;
    private string $password;

    public function __construct(
        Uuid $id,
        string $username,
        string $email,
        string $password
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
