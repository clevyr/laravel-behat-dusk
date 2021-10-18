Feature: User Login

    In order to login
    As a user
    I want to be able to login

    Scenario: Logging in as a User
        Given There are users
            | email            | password |
            | admin@clevyr.com | password |
        When I am on the "/login" page
        When I fill in email
        When I fill in password
        When I press the submit button
        Then I should be redirected to the "/dashboard" page

