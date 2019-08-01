<?php
declare(strict_types=1);

namespace api\tests;

use api\components\Tokens\TokensFactory;
use api\tests\_generated\FunctionalTesterActions;
use Codeception\Actor;
use common\models\Account;
use InvalidArgumentException;
use Yii;

class FunctionalTester extends Actor {
    use FunctionalTesterActions;

    public function amAuthenticated(string $asUsername = 'admin'): int {
        /** @var Account $account */
        $account = Account::findOne(['username' => $asUsername]);
        if ($account === null) {
            throw new InvalidArgumentException("Cannot find account with username \"{$asUsername}\"");
        }

        $token = TokensFactory::createForAccount($account);
        $this->amBearerAuthenticated((string)$token);

        return $account->id;
    }

    public function notLoggedIn(): void {
        $this->haveHttpHeader('Authorization', null);
        Yii::$app->user->logout();
    }

    public function canSeeAuthCredentials($expectRefreshToken = false): void {
        $this->canSeeResponseJsonMatchesJsonPath('$.access_token');
        $this->canSeeResponseJsonMatchesJsonPath('$.expires_in');
        if ($expectRefreshToken) {
            $this->canSeeResponseJsonMatchesJsonPath('$.refresh_token');
        } else {
            $this->cantSeeResponseJsonMatchesJsonPath('$.refresh_token');
        }
    }

}
