<?php
namespace tests\codeception\common\unit\models;

use Codeception\Specify;
use common\components\UserPass;
use common\models\Account;
use tests\codeception\common\fixtures\AccountFixture;
use tests\codeception\common\unit\DbTestCase;
use Yii;

/**
 * @property array $accounts
 */
class AccountTest extends DbTestCase {
    use Specify;

    public function fixtures() {
        return [
            'accounts' => [
                'class' => AccountFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/accounts.php',
            ],
        ];
    }

    public function testValidateUsername() {
        $this->specify('username required', function() {
            $model = new Account(['username' => null]);
            expect($model->validate(['username']))->false();
            expect($model->getErrors('username'))->equals(['error.username_required']);
        });

        $this->specify('username should be at least 3 symbols length', function() {
            $model = new Account(['username' => 'at']);
            expect($model->validate(['username']))->false();
            expect($model->getErrors('username'))->equals(['error.username_too_short']);
        });

        $this->specify('username should be not more than 21 symbols length', function() {
            $model = new Account(['username' => 'erickskrauch_erickskrauch']);
            expect($model->validate(['username']))->false();
            expect($model->getErrors('username'))->equals(['error.username_too_long']);
        });

        $this->specify('username can contain many cool symbols', function() {
            $shouldBeValid = [
                'русский_ник', 'русский_ник_на_грани!', 'numbers1132', '*__*-Stars-*__*', '1-_.!?#$%^&*()[]',
                '[ESP]Эрик', 'Свят_помидор;', 'зроблена_ў_беларусі:)',
            ];
            foreach($shouldBeValid as $nickname) {
                $model = new Account(['username' => $nickname]);
                expect($nickname . ' passed validation', $model->validate(['username']))->true();
                expect($model->getErrors('username'))->isEmpty();
            }
        });

        $this->specify('username cannot contain some symbols', function() {
            $shouldBeInvalid = [
                'nick@name', 'spaced nick',
            ];
            foreach($shouldBeInvalid as $nickname) {
                $model = new Account(['username' => $nickname]);
                expect($nickname . ' fail validation', $model->validate('username'))->false();
                expect($model->getErrors('username'))->equals(['error.username_invalid']);
            }
        });

        $this->specify('username should be unique', function() {
            $model = new Account(['username' => $this->accounts['admin']['username']]);
            expect($model->validate('username'))->false();
            expect($model->getErrors('username'))->equals(['error.username_not_available']);
        });
    }

    public function testValidateEmail() {
        $this->specify('email required', function() {
            $model = new Account(['email' => null]);
            expect($model->validate(['email']))->false();
            expect($model->getErrors('email'))->equals(['error.email_required']);
        });

        $this->specify('email should be not more 255 symbols (I hope it\'s impossible to register)', function() {
            $model = new Account([
                'email' => 'emailemailemailemailemailemailemailemailemailemailemailemailemailemailemailemailemail' .
                           'emailemailemailemailemailemailemailemailemailemailemailemailemailemailemailemailemail' .
                           'emailemailemailemailemailemailemailemailemailemailemailemailemailemailemailemailemail' .
                           'emailemail', // = 256 symbols
            ]);
            expect($model->validate(['email']))->false();
            expect($model->getErrors('email'))->equals(['error.email_too_long']);
        });

        $this->specify('email should be email (it test can fail, if you don\'t have internet connection)', function() {
            $model = new Account(['email' => 'invalid_email']);
            expect($model->validate(['email']))->false();
            expect($model->getErrors('email'))->equals(['error.email_invalid']);
        });

        $this->specify('email should be unique', function() {
            $model = new Account(['email' => $this->accounts['admin']['email']]);
            expect($model->validate('email'))->false();
            expect($model->getErrors('email'))->equals(['error.email_not_available']);
        });
    }

    public function testSetPassword() {
        $this->specify('calling method should change password and set latest password hash algorithm', function() {
            $model = new Account();
            $model->setPassword('12345678');
            expect('hash should be set', $model->password_hash)->notEmpty();
            expect('validation should be passed', $model->validatePassword('12345678'))->true();
            expect('latest password hash should be used', $model->password_hash_strategy)->equals(Account::PASS_HASH_STRATEGY_YII2);
        });
    }

    public function testValidatePassword() {
        $this->specify('old Ely password should work', function() {
            $model = new Account([
                'email' => 'erick@skrauch.net',
                'password_hash' => UserPass::make('erick@skrauch.net', '12345678'),
            ]);
            expect('valid password should pass', $model->validatePassword('12345678', Account::PASS_HASH_STRATEGY_OLD_ELY))->true();
            expect('invalid password should fail', $model->validatePassword('87654321', Account::PASS_HASH_STRATEGY_OLD_ELY))->false();
        });

        $this->specify('modern hash algorithm should work', function() {
            $model = new Account([
                'password_hash' => Yii::$app->security->generatePasswordHash('12345678'),
            ]);
            expect('valid password should pass', $model->validatePassword('12345678', Account::PASS_HASH_STRATEGY_YII2))->true();
            expect('invalid password should fail', $model->validatePassword('87654321', Account::PASS_HASH_STRATEGY_YII2))->false();
        });

        $this->specify('if second argument is not pass model value should be used', function() {
            $model = new Account([
                'email' => 'erick@skrauch.net',
                'password_hash_strategy' => Account::PASS_HASH_STRATEGY_OLD_ELY,
                'password_hash' => UserPass::make('erick@skrauch.net', '12345678'),
            ]);
            expect('valid password should pass', $model->validatePassword('12345678'))->true();
            expect('invalid password should fail', $model->validatePassword('87654321'))->false();

            $model = new Account([
                'password_hash_strategy' => Account::PASS_HASH_STRATEGY_YII2,
                'password_hash' => Yii::$app->security->generatePasswordHash('12345678'),
            ]);
            expect('valid password should pass', $model->validatePassword('12345678'))->true();
            expect('invalid password should fail', $model->validatePassword('87654321'))->false();
        });
    }

}