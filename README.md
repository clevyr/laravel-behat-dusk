![GitHub release (latest SemVer including pre-releases)](https://img.shields.io/github/v/release/clevyr/laravel-behat-dusk?include_prereleases)
![](https://github.com/clevyr/laravel-behat-dusk/workflows/Run%20Tests/badge.svg?branch=master)

Laravel Behat Dusk is a simple package with the goal of integrating
Behat with Laravel and Laravel Dusk.

**This is not a 1:1 implementation of Behat with Laravel,
the current goal is to enable a simple and quick way to write Behavior Driven Tests
in Laravel Applications.**

---

## Pre-requisites
- Laravel 8+
- PHP 8.0+
- Basic Understanding of Behavioral Driven Development and Behat

## Installation

```bash
composer require clevyr/laravel-behat-dusk --dev
```

Install the Package
```bash
php artisan lbd:install
```

The following files will be outputted
- features
    - bootstrap
        - FeatureContext
- behat.yml

#### Testing Environments
Laravel Behat Dusk's environment setup works the exact same way as Laravel Dusk.
Take a look at their documentation for environment handling. [https://laravel.com/docs/8.x/dusk#environment-handling]()

#### Running in Docker

- Create a .env.dusk.local file by running `cp .env .env.dusk.local` and add it to your .gitignore file
- Update the following `.env.dusk.local` environment file with the following

```dotenv
APP_URL=http://laravel.test
DUSK_DRIVER_URL=http://selenium:4444/wd/hub
```

If you are not using Laravel Sail use the docker-compose container
names for your Application and Selenium Containers.

- Run Laravel Behat Dusk in the app container `sail artisan lbd`

## Usage

#### Creating a context file
This creates a Context file in the `features/bootstrap` and appends the relevant
configuration data inside the behat.yml

`php artisan lbd:make ExampleContext`

```bash
--profile[=PROFILE]  Create under the profile in the Behat Config [default: "default"]
--suite[=SUITE]      Create under the suite in the Behat Config [default: "default"]
```

#### Running Behat
Run this command to run the Behat test runner, you are able to use all arguments from the Behat CLI

`php artisan lbd`

#### Traits
**Refresh the database before each Scenario**
`use RefreshScenario;`


#### Example feature file and context file

login.feature file
```yaml
Feature: User Login

    In order to acess the site
    As a user
    I want to be able to login

    Scenario: Logging in as a User
        Given There are users
            | email            | password |
            | user@example.com | password |
        When I am on the "/login" page
        When I fill in email
        When I fill in password
        When I press the submit button
        Then I should be redirected to the "/dashboard" page
```

FeatureContext.php
```php
<?php

use Behat\Gherkin\Node\TableNode;
use Clevyr\LaravelBehatDusk\BehatDuskTestCase;
use Clevyr\LaravelBehatDusk\Traits\RefreshScenario;
use Laravel\Dusk\Browser;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends BehatDuskTestCase
{
    use RefreshScenario;

    /**
     * @var array $user
     */
    public array $user;

    /**
     * @Given /^There are users$/
     * @param TableNode $table
     */
    public function thereAreUsers(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $this->user = ['email' => $row['email'], 'password' => $row['password']];
        }
    }

    /**
     * @When /^I am on the "([^"]*)" page$/
     * @param string $page
     * @throws Throwable
     */
    public function iAmOnThePage(string $page)
    {
        $this->browse(function (Browser $browser) use ($page) {
            $browser->visit($page);
        });
    }

    /**
     * @When /^I fill in (.*)$/
     * @throws Throwable
     */
    public function iFillIn()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->type('email', $this->user['email'])
                ->type('password', $this->user['password']);
        });
    }

    /**
     * @When /^I press the submit button$/
     * @throws Throwable
     */
    public function iPressTheSubmitButton()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->press('[type="submit"]')
                ->waitForLocation('/dashboard');
        });
    }

    /**
     * @Then /^I should be redirected to the "([^"]*)" page$/
     * @param string $page
     * @throws Throwable
     */
    public function iShouldBeRedirectToThePage(string $page)
    {
        $this->browse(function (Browser $browser) use ($page) {
            $browser->assertPathIs($page);
        });
    }
}
```

## Testing
`composer test`

## Security Vulnerabilities
Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits
- [Clevyr, Inc](https://clevyr.com)
- [All Contributors](../../contributors)

## License
Laravel Behat Dusk is [GPL-3.0 License Licensed](LICENSE.md)
