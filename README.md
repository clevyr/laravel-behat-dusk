[![Latest Version on Packagist](https://img.shields.io/packagist/v/clevyr/laravel-behat.svg?style=flat-square)](https://packagist.org/packages/clevyr/laravel-behat)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/clevyr/laravel-behat/run-tests?label=tests)](https://github.com/clevyr/laravel-behat/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/clevyr/laravel-behat/Check%20&%20fix%20styling?label=code%20style)](https://github.com/clevyr/laravel-behat/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/clevyr/laravel-behat.svg?style=flat-square)](https://packagist.org/packages/clevyr/laravel-behat)

Laravel Behat Dusk is a simple package that integrates
Behat with Laravel and Laravel Dusk with the goal of writing BDD tests
as painless as possible.

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
- config
    - behat-dusk.php
- features
    - bootstrap
        - FeatureContext
- behat.yml

Configuration File
```php
return [
    // Base url when running Laravel Dusk
    'dusk_base_url' => env('DUSK_BASE_URL') ?? 'http://localhost'
];
```

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
use Clevyr\LaravelBehat\BehatDuskTestCase;
use Clevyr\LaravelBehat\Traits\RefreshScenario;
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
