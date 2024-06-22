Feature: Get User Information

    Scenario: Retrieve user details
        Given I am an authorized user
        When I send a GET request to "/api/me"
        Then I receive a status code of 200
        And the response body is in JSON format
        And the response contains the following user information:
        | Field       | Type          | Description                             |
        | id          | string        | Unique identifier of the user           |
        | username    | string        | Username of the user                    |
        | email       | string        | Email address of the user               |

    Scenario: Unauthorized access
        Given I am not authorized
        When I send a GET request to "/api/me"
        Then I receive a status code of 401
