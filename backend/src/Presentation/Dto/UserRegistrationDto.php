<?php

declare(strict_types=1);

namespace App\Presentation\Dto;

readonly class UserRegistrationDto
{
    private string $username;
    private string $email;
    private string $password;

    public function __construct(array $data)
    {
        $this->username = $data['username'];
        $this->password = $data['password'];
        $this->email = $data['email'];
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
        ];
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
