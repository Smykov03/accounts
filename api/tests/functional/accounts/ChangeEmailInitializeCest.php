<?php
namespace api\tests\functional\accounts;

use api\tests\_pages\AccountsRoute;
use api\tests\FunctionalTester;

class ChangeEmailInitializeCest {

    /**
     * @var AccountsRoute
     */
    private $route;

    public function _before(FunctionalTester $I) {
        $this->route = new AccountsRoute($I);
    }

    public function testChangeEmailInitialize(FunctionalTester $I) {
        $I->wantTo('send current email confirmation');
        $id = $I->amAuthenticated();

        $this->route->changeEmailInitialize($id, 'password_0');
        $I->canSeeResponseCodeIs(200);
        $I->canSeeResponseIsJson();
        $I->canSeeResponseContainsJson([
            'success' => true,
        ]);
    }

    public function testChangeEmailInitializeFrequencyError(FunctionalTester $I) {
        $I->wantTo('see change email request frequency error');
        $id = $I->amAuthenticated('ILLIMUNATI');

        $this->route->changeEmailInitialize($id, 'password_0');
        $I->canSeeResponseContainsJson([
            'success' => false,
            'errors' => [
                'email' => 'error.recently_sent_message',
            ],
        ]);
        $I->canSeeResponseJsonMatchesJsonPath('$.data.canRepeatIn');
    }

}
