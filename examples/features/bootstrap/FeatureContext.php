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
