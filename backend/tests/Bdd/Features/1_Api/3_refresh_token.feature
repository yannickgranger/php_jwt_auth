Feature: Refresh Token Authentication

    Scenario: User successfully logs in and obtains refresh token
        Given a user exists with username "john.doe@example.com" and password "jDoe@123_*ExAmPle.com"
        When the user sends a POST request to "/api/login" with credentials
        Then the response status code should be 200
        And the response should contain a JSON body with:
        * a key "token" with a valid JWT access token
        * a key "refresh_token" with a valid refresh token string

    Scenario: User uses refresh token to obtain a new access token
        Given a user has a valid refresh token obtained from a previous login
        When the user sends a POST request to "/api/refresh-token" with the refresh token in the request body
        Then the response status code should be 200
        And the response should contain a JSON body with:
        * a key "token" with a new valid JWT access token

    Scenario: User attempts to refresh with an invalid refresh token
        Given a user has an invalid refresh token
        When the user sends a POST request to "/api/refresh-token" with the invalid refresh token in the request body
        Then the response status code should be 401
        And the response should contain a JSON body with an error message indicating invalid refresh token

    Scenario: User attempts to refresh after token expiration
        Given a user has a valid refresh token but the associated access token has expired
        When the user sends a POST request to "/api/refresh-token" with the expired refresh token in the request body
        Then the response status code should be 401

    Scenario Outline: User attempts to refresh with a blacklisted refresh token
        Given a user has a refresh token that has been blacklisted by the server
        When the user sends a POST request to "/api/refresh-token" with the blacklisted refresh token in the request body
        Then the response status code should be 401

        Examples:
            | Reason for blacklisting                       |
            | User logged out from another device           |
            | Suspicious activity detected on the account   |

