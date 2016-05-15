<?php
namespace tests\codeception\api\models\authentication;

use api\models\authentication\RegistrationForm;
use Codeception\Specify;
use common\models\Account;
use common\models\EmailActivation;
use tests\codeception\api\unit\DbTestCase;
use tests\codeception\common\fixtures\AccountFixture;
use Yii;

/**
 * @property array $accounts
 */
class RegistrationFormTest extends DbTestCase {
    use Specify;

    public function setUp() {
        parent::setUp();
        /** @var \yii\swiftmailer\Mailer $mailer */
        $mailer = Yii::$app->mailer;
        $mailer->fileTransportCallback = function () {
            return 'testing_message.eml';
        };
    }

    protected function tearDown() {
        if (file_exists($this->getMessageFile())) {
            unlink($this->getMessageFile());
        }

        parent::tearDown();
    }

    public function fixtures() {
        return [
            'accounts' => [
                'class' => AccountFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/accounts.php',
            ],
        ];
    }

    public function testValidatePasswordAndRePasswordMatch() {
        $this->specify('error.rePassword_does_not_match if password and rePassword not match', function() {
            $model = new RegistrationForm([
                'password' => 'enough-length',
                'rePassword' => 'password',
            ]);
            expect($model->validate(['rePassword']))->false();
            expect($model->getErrors('rePassword'))->equals(['error.rePassword_does_not_match']);
        });

        $this->specify('no errors if password and rePassword match', function() {
            $model = new RegistrationForm([
                'password' => 'enough-length',
                'rePassword' => 'enough-length',
            ]);
            expect($model->validate(['rePassword']))->true();
            expect($model->getErrors('rePassword'))->isEmpty();
        });
    }

    public function testSignup() {
        $model = new RegistrationForm([
            'username' => 'some_username',
            'email' => 'some_email@example.com',
            'password' => 'some_password',
            'rePassword' => 'some_password',
            'rulesAgreement' => true,
            'lang' => 'ru',
        ]);

        $account = $model->signup();

        $this->expectSuccessRegistration($account);
        expect('lang is set', $account->lang)->equals('ru');
    }

    public function testSignupWithDefaultLanguage() {
        $model = new RegistrationForm([
            'username' => 'some_username',
            'email' => 'some_email@example.com',
            'password' => 'some_password',
            'rePassword' => 'some_password',
            'rulesAgreement' => true,
        ]);

        $account = $model->signup();

        $this->expectSuccessRegistration($account);
        expect('lang is set', $account->lang)->equals('en');
    }

    /**
     * @param Account|null $account
     */
    private function expectSuccessRegistration($account) {
        expect('user should be valid', $account)->isInstanceOf(Account::class);
        expect('password should be correct', $account->validatePassword('some_password'))->true();
        expect('uuid is set', $account->uuid)->notEmpty();
        expect('user model exists in database', Account::find()->andWhere([
            'username' => 'some_username',
            'email' => 'some_email@example.com',
        ])->exists())->true();
        expect('email activation code exists in database', EmailActivation::find()->andWhere([
            'account_id' => $account->id,
            'type' => EmailActivation::TYPE_REGISTRATION_EMAIL_CONFIRMATION,
        ])->exists())->true();
        expect_file('message file exists', $this->getMessageFile())->exists();
    }

    // TODO: там в самой форме есть метод sendMail(), который рано или поздно должен переехать. К нему нужны будут тоже тесты

    private function getMessageFile() {
        /** @var \yii\swiftmailer\Mailer $mailer */
        $mailer = Yii::$app->mailer;

        return Yii::getAlias($mailer->fileTransportPath) . '/testing_message.eml';
    }

}