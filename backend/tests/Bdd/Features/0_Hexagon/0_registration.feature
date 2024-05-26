Feature: User Registration

    Background:
        Given I have no users registered

    Scenario: Successful User Registration
        Given I register a user from the following data:
        """
        {
          "username": "johndoe",
          "email": "johndoe@example.com",
          "password": "secret@!@$A123456789"
        }
        """
        Then the user with username "johndoe" should exist
        And the user email should be "johndoe@example.com"
        And the user password should be encrypted


#    Scenario: Registration with missing data
#        Given I register a user from the following data:
#        """
#        {
#          "username": "johndoe",
#          "password": "secret@!@$A123456789"
#        }
#        """
#    Then an "InvalidUserDataException" exception should be thrown
#    And the exception should have a message starting with "Missing required data for registration"
#    And the exception should have a message containing the field with "email"
#
#
#
#    Scenario: Registration with invalid email format
#        Given I register a user from the following data:
#        """
#        {
#          "username": "johndoe",
#          "email": "invalid_email",
#          "password": "secret@!@$A123456789"
#        }
#        """
#        Then an "InvalidUserDataException" exception should be thrown
#        And the exception should have a message starting with "Invalid data for registration"
#        And the exception should have a message containing the field with "email"

#    Scenario: Same username want to register
#        Given I register a user from the following data:
#        """
#        {
#          "username": "johndoe",
#          "email": "johndoe@example.com",
#          "password": "secret123"
#        }
#        """
#        Then the user with username "johndoe" should exist
#        And the user email should be "johndoe@example.com"
#        And the user password should be encrypted
#        And I register a user from the following data:
#        """
#        {
#          "username": "johndoe",
#          "email": "johndoe@example.com",
#          "password": "secret123"
#        }
#        """
#        Then an "UniqueUsernameException" exception should be thrown
#        And the exception should have a message starting with "Username is already in use."
#        And I register a user from the following data:
#        """
#        {
#          "username": "john_doe",
#          "email": "johndoe@example.com",
#          "password": "secret123"
#        }
#        """
#        Then an "UniqueEmailException" exception should be thrown
#        And the exception should have a message starting with "Email is already in use."
#
#        Scenario: Successful Registration with Strong Password
#        Given I register a user from the following data:
#        """
#        {
#        "username": "johndoe",
#        "email": "johndoe@example.com",
#        "password": "StrongPassword123"
#        }
#        """
#        Then the user should be registered successfully
#
#        Scenario: Registration with Weak Password
#        Given I register a user from the following data:
#        """
#        {
#        "username": "johndoe",
#        "email": "johndoe@example.com",
#        "password": "weakpassword"
#        }
#        """
#        Then an "InvalidPasswordException" exception should be thrown
#        And the exception message should start with "Password does not meet complexity requirements"
