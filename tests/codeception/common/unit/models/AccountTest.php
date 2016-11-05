<?php
namespace tests\codeception\common\unit\models;

use Codeception\Specify;
use common\components\UserPass;
use common\models\Account;
use tests\codeception\common\fixtures\MojangUsernameFixture;
use tests\codeception\common\unit\TestCase;
use Yii;
use const common\LATEST_RULES_VERSION;

class AccountTest extends TestCase {
    use Specify;

    public function testSetPassword() {
        $model = new Account();
        $model->setPassword('12345678');
        $this->assertNotEmpty($model->password_hash, 'hash should be set');
        $this->assertTrue($model->validatePassword('12345678'), 'validation should be passed');
        $this->assertEquals(Account::PASS_HASH_STRATEGY_YII2, $model->password_hash_strategy, 'latest password hash should be used');
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

    public function testHasMojangUsernameCollision() {
        $this->tester->haveFixtures([
            'mojangUsernames' => MojangUsernameFixture::class,
        ]);

        $this->specify('Expect true if collision with current username', function() {
            $model = new Account();
            $model->username = 'ErickSkrauch';
            expect($model->hasMojangUsernameCollision())->true();
        });

        $this->specify('Expect false if some rare username without any collision on Mojang', function() {
            $model = new Account();
            $model->username = 'rare-username';
            expect($model->hasMojangUsernameCollision())->false();
        });
    }

    public function testGetProfileLink() {
        $model = new Account();
        $model->id = '123';
        $this->assertEquals('http://ely.by/u123', $model->getProfileLink());
    }

    public function testIsAgreedWithActualRules() {
        $this->specify('get false, if rules field set in null', function() {
            $model = new Account();
            expect($model->isAgreedWithActualRules())->false();
        });

        $this->specify('get false, if rules field have version less, then actual', function() {
            $model = new Account();
            $model->rules_agreement_version = 0;
            expect($model->isAgreedWithActualRules())->false();
        });

        $this->specify('get true, if rules field have equals rules version', function() {
            $model = new Account();
            $model->rules_agreement_version = LATEST_RULES_VERSION;
            expect($model->isAgreedWithActualRules())->true();
        });
    }

    public function testSetRegistrationIp() {
        $account = new Account();
        $account->setRegistrationIp('42.72.205.204');
        $this->assertEquals('42.72.205.204', inet_ntop($account->registration_ip));
        $account->setRegistrationIp('2001:1620:28:1:b6f:8bca:93:a116');
        $this->assertEquals('2001:1620:28:1:b6f:8bca:93:a116', inet_ntop($account->registration_ip));
        $account->setRegistrationIp(null);
        $this->assertNull($account->registration_ip);
    }

    public function testGetRegistrationIp() {
        $account = new Account();
        $account->setRegistrationIp('42.72.205.204');
        $this->assertEquals('42.72.205.204', $account->getRegistrationIp());
        $account->setRegistrationIp('2001:1620:28:1:b6f:8bca:93:a116');
        $this->assertEquals('2001:1620:28:1:b6f:8bca:93:a116', $account->getRegistrationIp());
        $account->setRegistrationIp(null);
        $this->assertNull($account->getRegistrationIp());
    }

}
