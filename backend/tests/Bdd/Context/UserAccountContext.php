<?php

declare(strict_types=1);


namespace App\Tests\Bdd\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Webmozart\Assert\Assert;
use function PHPStan\Testing\assertType;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertNotNull;

class UserAccountContext implements Context
{

    private HttpClientInterface $httpClient;

    private ?string $jwt = null;

    private ResponseInterface $response;

    private array $userInfo = [];

    public function __construct(

    ) {
        $this->httpClient = HttpClient::createForBaseUri('https://localhost');

    }


    /**
     * @Given I am an authorized user
     */
    public function iAmAnAuthorizedUser()
    {
        $payload =
            [
                "username"  => "john.doe@example.com",
                "password"  => "jDoe@123_*ExAmPle.com"
            ]
        ;

        $response = $this->httpClient->request('POST', 'https://localhost/api/login_check', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => $payload
        ]);
        $data = json_decode($response->getContent(), true);
        $this->jwt = $data['token'];
    }

    /**
     * @When I send a GET request to :arg1
     */
    public function iSendAGetRequestTo($arg1)
    {
        $this->response = $this->httpClient->request('GET', 'https://localhost'.$arg1, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$this->jwt,
            ]
        ]);;
    }

    /**
     * @Then I receive a status code of :arg1
     */
    public function iReceiveAStatusCodeOf($arg1)
    {
        assertEquals($arg1, $this->response->getStatusCode());
    }

    /**
     * @Then the response body is in JSON format
     */
    public function theResponseBodyIsInJsonFormat()
    {
        $responseContent = $this->response->getContent();
        try{
            $data = json_decode($responseContent, true);
        } catch (\Exception $exception){

        }
        assertNotNull($data);
        assertIsArray($data);
        $this->userInfo = $data;
    }

    /**
     * @Then the response contains the following user information:
     */
    public function theResponseContainsTheFollowingUserInformation(TableNode $table)
    {
        $rows = $table->getRows();
        array_shift($rows);
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($rows as $field){
            $name = $field[0];
            $type = $field[1];
            $value = $accessor->getValue($this->userInfo, "[$name]");
            Assert::{$type}($type, $value);
        }
    }

    /**
     * @Given I am not authorized
     */
    public function iAmNotAuthorized()
    {
        $this->jwt = null;
    }
}
