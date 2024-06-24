Feature: User Registration

    Background:
        Given I have no users registered

    Scenario: Successful User Registration
        Given I register a user from the following data:
        """
        {
          "username": "john.doe@example.com",
          "email": "john.doe@example.com",
          "password": "jDoe@123_*ExAmPle.com"
        }
        """
        Then the user with username "john.doe@example.com" should exist
        And the user email should be "john.doe@example.com"
        And the user password should be encrypted


    Scenario: Registration with missing data
        Given I register a user from the following data:
        """
        {
          "username": "johndoe",
          "password": "secret@!@$A123456789"
        }
        """
    Then an "InvalidUserDataException" exception should be thrown
    And the exception message should be :
    """
    Invalid data for registration
    Email field is invalid.
    email cannot be null
    """

    Scenario: Registration with invalid email format
        Given I register a user from the following data:
        """
        {
          "username": "johndoe",
          "email": "invalid_email",
          "password": "secret@!@$A123456789"
        }
        """
        Then an "InvalidUserDataException" exception should be thrown
        And the exception message should be :
        """
        Invalid data for registration
        Email field is invalid.
        email should be formated in HTML5 format
        """

    Scenario: Same username want to register
        Given I register a user from the following data:
        """
        {
          "username": "john.doe@example.com",
          "email": "john.doe@example.com",
          "password": "secret123@123ASd"
        }
        """
        Then the user with username "john.doe@example.com" should exist
        And the user email should be "john.doe@example.com"
        And the user password should be encrypted
        And I register a user from the following data:
        """
        {
          "username": "johndoe",
          "email": "john.doe@example.com",
          "password": "secreQwet123&^%aQR"
        }
        """
        Then an "UniqueUsernameException" exception should be thrown
        And the exception message should be :
        """
        Email is already taken.
        """

        Scenario: Successful Registration with Strong Password
        Given I register a user from the following data:
        """
        {
            "username": "johndoe@example.com",
            "email": "johndoe@example.com",
            "password": "StrongPassword123@1%!"
        }
        """
        Then the user "johndoe@example.com" should be registered successfully

        Scenario: Registration with Weak Password
        Given I register a user from the following data:
        """
        {
            "username": "johndoe",
            "email": "johndoe@example.com",
            "password": "weakpassword"
        }
        """
        Then an "InvalidPasswordException" exception should be thrown
        And the exception message should be :
        """
        Invalid data for registration
        Password field is invalid.
        password must be at least 16
        """

    Scenario: Registration with Weak Password
        Given I register a user from the following data:
        """
        {
            "username": "johndoe",
            "email": "johndoe@example.com",
            "password": "1111111111111111"
        }
        """
        Then an "InvalidPasswordException" exception should be thrown
        And the exception message should be :
        """
        Invalid data for registration
        Password field is invalid.
        password should contain at least one uppercase and lowercase letter, one number
        """
