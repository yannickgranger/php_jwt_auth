<?php

declare(strict_types=1);

namespace App\Tests\Bdd\Context;

use App\Domain\Entity\User;
use App\Domain\Exception\InvalidUserDataException;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Service\UserRegistrationService;
use App\Domain\Service\UserRegistrationValidationService;
use App\Presentation\Dto\UserRegistrationDto;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertTrue;

final class RegistrationContext implements Context
{
    private ?\Exception $exception = null;
    private array $data;
    private UserRegistrationService $userRegistrationService;
    private UserRepositoryInterface $userRepository;

    private User $user;
    private UserRegistrationValidationService $userRegistrationValidationService;

    public function __construct(
        UserRegistrationService $userRegistrationService,
        UserRegistrationValidationService $userRegistrationValidationService,
        UserRepositoryInterface $userRepository,
    ) {
        $this->userRegistrationService = $userRegistrationService;
        $this->userRegistrationValidationService = $userRegistrationValidationService;
        $this->userRepository = $userRepository;
        $this->data = [];
    }

    /**
     * @Given I have no users registered
     */
    public function iHaveNoUsersRegistered(): void
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $this->userRepository->remove($user);
        }
        assertCount(0, $this->userRepository->findAll());
    }

    /**
     * @Given I register a user from the following data:
     */
    public function iRegisterAUserFromTheFollowingData(PyStringNode $string): void
    {
        $data = json_decode($string->getRaw(), true);
        try {
            $this->userRegistrationValidationService->validate($data);
            $userDto = new UserRegistrationDto(data: $data);
            $this->userRegistrationService->register($userDto);
        } catch (\Exception $exception) {
            $this->exception = $exception;
        }
        $this->data = $data;
    }

    /**
     * @Then the user with username :arg1 should exist
     */
    public function theUserWithUsernameShouldExist($arg1): void
    {
        assertTrue($this->userRepository->existsByUsername($arg1));
        $this->user = $this->userRepository->findByUsername($arg1);
    }

    /**
     * @Then the user email should be :arg1
     */
    public function theUserEmailShouldBe($arg1): void
    {
        assertEquals($arg1, $this->user->getEmail());
    }

    /**
     * @Then the user password should be encrypted
     */
    public function theUserPasswordShouldBeEncrypted(): void
    {
        assertNotEquals($this->data['password'], $this->user->getPassword());
        assertTrue(password_verify($this->data['password'], $this->user->getPassword()));
    }

    /**
     * @Then an :arg1 exception should be thrown
     */
    public function anExceptionShouldBeThrown($arg1): void
    {
        if ($arg1 === 'InvalidUserDataException') {
            assertInstanceOf(InvalidUserDataException::class, $this->exception);
        }
    }

    /**
     * @Then the exception message should be :
     */
    public function theExceptionMessageShouldBe(PyStringNode $string): void
    {
        assertEquals(implode(PHP_EOL, $string->getStrings()), $this->exception->getMessage());
    }

    /**
     * @Then the user :arg1 should be registered successfully
     */
    public function theUserShouldBeRegisteredSuccessfully($arg1): void
    {
        assertInstanceOf(User::class, $this->userRepository->findByUsername($arg1));
    }

    public function getException(): \Exception
    {
        return $this->exception;
    }
}
