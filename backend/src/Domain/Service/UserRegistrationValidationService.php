<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Exception\InvalidUserDataException;
use Webmozart\Assert\Assert;

class UserRegistrationValidationService
{
    private const string PATTERN_EMAIL_HTML5 = '/^[a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/';
    private const string PATTERN_PASSWORD_REQUIREMENTS = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*#?&()_+{}])[A-Za-z\d@#$!%*?&()_+{}]{12,}$/';

    public function validate(array $data): void
    {
        $this->validateUsername($data['username'] ?? null);

        $this->validatePassword($data['password'] ?? null);

        $this->validateEmail($data['email'] ?? null);
    }

    private function validateUsername(?string $username): void
    {
        try {
            Assert::notNull($username, 'username cannot be null');
            Assert::notWhitespaceOnly($username, 'username cannot be blank');
            Assert::string($username, 'username must be a string');
            Assert::minLength($username, 2, 'username must be at least 2');
            Assert::maxLength($username, 255, 'username must be less than 255');
        } catch (\Exception $exception) {
            throw new InvalidUserDataException('Username field is invalid. '.PHP_EOL.$exception->getMessage());
        }
    }

    private function validatePassword(?string $password): void
    {
        try {
            Assert::notNull($password, 'password cannot be null');
            Assert::string($password, 'password must be a string');
            Assert::minLength($password, 12, 'password must be at least 12');
            Assert::maxLength($password, 255, 'password must be less than 255');
            Assert::regex($password, self::PATTERN_PASSWORD_REQUIREMENTS, 'password must contain at least one special character, a lower case and an upper case letter');
        } catch (\Exception $exception) {
            throw new InvalidUserDataException('Password field is invalid. '.PHP_EOL.$exception->getMessage());
        }
    }

    private function validateEmail(?string $email): void
    {
        try {
            Assert::notNull($email, 'email cannot be null');
            Assert::string($email, 'email must be a string');
            Assert::minLength($email, 12, 'email must be at least 12');
            Assert::maxLength($email, 255, 'email must be less than 255');
            Assert::regex($email, self::PATTERN_EMAIL_HTML5, 'email should be formated in HTML5 format');
        } catch (\Exception $exception) {
            throw new InvalidUserDataException('Email field is invalid. '.PHP_EOL.$exception->getMessage());
        }
    }
}
