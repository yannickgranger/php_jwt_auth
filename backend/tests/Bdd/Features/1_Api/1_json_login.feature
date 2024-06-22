Feature: User login with json login


    Background:
        Given i have a registered user "john.doe@example.com"
        Then the user make a http "POST" request to "/api/login_check" with body:
        """
        {
            "username": "john.doe@example.com",
            "password": "jDoe@123_*ExAmPle.com"
        }
        """
        And the response status code should be "200"
        And the response content should include a jwt token


    Scenario: User can access "/api/time"
        Given the user has a valid token
        And the jwt contains the following elements:
        """json

        {
            "username": "john.doe@example.com",
            "roles": [
                "PORTFOLIO_USER"
            ]
        }
        """
        Given the user make a http "GET" request to "/api/time"
        And the response status code should be "200"
        And the response content should contain a "datetime"
