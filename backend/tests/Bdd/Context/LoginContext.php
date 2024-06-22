<?php

declare(strict_types=1);

namespace App\Tests\Bdd\Context;

use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Service\UserRegistrationService;
use App\Domain\Service\UserRegistrationValidationService;
use App\Presentation\Dto\UserRegistrationDto;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use function PHPUnit\Framework\assertNotNull;

final class LoginContext implements Context
{
    private UserRepositoryInterface $userRepository;
    private HttpClientInterface $httpClient;
    private UserRegistrationService $userRegistrationService;
    private UserRegistrationValidationService $userRegistrationValidationService;
    private ?ResponseInterface $response = null;
    private ?string $jwt = null;

    private JWTTokenManagerInterface $tokenManager;

    public function __construct(
        KernelInterface $kernel,
        UserRegistrationService $userRegistrationService,
        UserRegistrationValidationService $userRegistrationValidationService,
        UserRepositoryInterface $userRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->httpClient = HttpClient::createForBaseUri('http://localhost');
        $this->userRegistrationService = $userRegistrationService;
        $this->userRegistrationValidationService = $userRegistrationValidationService;
        $container = $kernel->getContainer();
        $this->tokenManager = $container->get('lexik_jwt_authentication.jwt_manager');
    }

    /**
     * @Given i have a registered user :arg1
     */
    public function iHaveARegisteredUser($arg1)
    {
        $data = json_decode('{
          "username": "john.doe@example.com",
          "email": "john.doe@example.com",
          "password": "jDoe@123_*ExAmPle.com"
        }', true);

        try {
            $this->userRegistrationValidationService->validate($data);
            $userDto = new UserRegistrationDto(data: $data);
            $this->userRegistrationService->register($userDto);
        } catch (\Exception $exception) {
        }

        $users = $this->userRepository->findAll();
        assertGreaterThanOrEqual(1, count($users));
        $user = $users[0];
        assertEquals($user->getUsername(), $arg1);
    }

    /**
     * @Then the user make a http :arg1 request to :arg2 with body:
     */
    public function theUserMakeAHttpRequestToWithBody($arg1, $arg2, PyStringNode $string)
    {
        $body = json_decode($string->getRaw(), true);
        if ($arg1 === 'POST') {
            $this->response = $this->httpClient->request($arg1, $arg2, [
                'json' => $body,
            ]);
        }
    }

    /**
     * @Given the user make a http :arg1 request to :arg2
     */
    public function theUserMakeAHttpRequestTo($arg1, $arg2)
    {
        $this->response = null;
        $this->response = $this->httpClient->request(
            $arg1,
            'https://localhost'.$arg2,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$this->jwt,
                ],
            ]);
    }

    /**
     * @Then the response status code should be :arg1
     */
    public function theResponseStatusCodeShouldBe($arg1)
    {
        assertEquals($arg1, $this->response->getStatusCode());
    }

    /**
     * @Then the response content should include a jwt token
     */
    public function theResponseContentShouldIncludeAJwtToken()
    {
        $content = json_decode($this->response->getContent(), true);
        assertArrayHasKey('token', $content);
        $this->jwt = $content['token'];
    }

    /**
     * @Given the user has a valid token
     */
    public function theUserHasAValidToken()
    {
        assertNotNull($this->jwt);
        $jwtContent = $this->tokenManager->parse($this->jwt);
        assertArrayHasKey('roles', $jwtContent);
        assertArrayHasKey('username', $jwtContent);
    }

    /**
     * @Given the jwt contains the following elements:
     */
    public function theJwtContainsTheFollowingElements(PyStringNode $string)
    {
        $jwtContent = $this->tokenManager->parse($this->jwt);
        unset($jwtContent['iat']);
        unset($jwtContent['exp']);
        $expected = json_decode(implode($string->getStrings()), true);
        assertEquals($jwtContent, $expected);
    }

    /**
     * @Given the response content should contain a :arg1
     */
    public function theResponseContentShouldContainA($arg1)
    {
        $content = json_decode($this->response->getContent(), true);
        if ($arg1 === 'datetime') {
            assertArrayHasKey('date', $content);
            assertArrayHasKey('timezone_type', $content);
            assertArrayHasKey('timezone', $content);
        }
    }
}
