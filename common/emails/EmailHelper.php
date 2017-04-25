<?php
namespace common\emails;

use common\emails\templates\ChangeEmailConfirmCurrentEmail;
use common\emails\templates\ChangeEmailConfirmNewEmail;
use common\emails\templates\ForgotPasswordEmail;
use common\emails\templates\ForgotPasswordParams;
use common\emails\templates\RegistrationEmail;
use common\emails\templates\RegistrationEmailParams;
use common\models\Account;
use common\models\confirmations\CurrentEmailConfirmation;
use common\models\confirmations\ForgotPassword;
use common\models\confirmations\NewEmailConfirmation;
use common\models\confirmations\RegistrationConfirmation;
use Yii;

class EmailHelper {

    public static function registration(RegistrationConfirmation $emailActivation): void {
        $account = $emailActivation->account;
        $locale = $account->lang;
        $params = new RegistrationEmailParams(
            $account->username,
            $emailActivation->key,
            Yii::$app->request->getHostInfo() . '/activation/' . $emailActivation->key
        );

        (new RegistrationEmail(self::buildTo($account), $locale, $params))->send();
    }

    public static function forgotPassword(ForgotPassword $emailActivation): void {
        $account = $emailActivation->account;
        $locale = $account->lang;
        $params = new ForgotPasswordParams(
            $account->username,
            $emailActivation->key,
            Yii::$app->request->getHostInfo() . '/recover-password/' . $emailActivation->key
        );

        (new ForgotPasswordEmail(self::buildTo($account), $locale, $params))->send();
    }

    public static function changeEmailConfirmCurrent(CurrentEmailConfirmation $emailActivation): void {
        (new ChangeEmailConfirmCurrentEmail(self::buildTo($emailActivation->account), $emailActivation->key))->send();
    }

    public static function changeEmailConfirmNew(NewEmailConfirmation $emailActivation): void {
        $account = $emailActivation->account;
        (new ChangeEmailConfirmNewEmail(self::buildTo($account), $account->username, $emailActivation->key))->send();
    }

    public static function buildTo(Account $account): array {
        return [$account->email => $account->username];
    }

}
