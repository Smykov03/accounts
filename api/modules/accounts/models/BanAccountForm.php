<?php
namespace api\modules\accounts\models;

use api\exceptions\ThisShouldNotHappenException;
use api\modules\internal\helpers\Error as E;
use common\models\Account;
use common\tasks\ClearAccountSessions;
use Yii;

class BanAccountForm extends AccountActionForm {

    public const DURATION_FOREVER = -1;

    /**
     * Нереализованный функционал блокировки аккаунта на определённый период времени.
     * Сейчас установка этого параметра ничего не даст, аккаунт будет заблокирован навечно,
     * но, по задумке, здесь можно передать количество секунд, на которое будет
     * заблокирован аккаунт пользователя.
     *
     * @var int
     */
    public $duration = self::DURATION_FOREVER;

    /**
     * Нереализованный функционал указания причины блокировки аккаунта.
     *
     * @var string
     */
    public $message = '';

    public function rules(): array {
        return [
            [['duration'], 'integer', 'min' => self::DURATION_FOREVER],
            [['message'], 'string'],
            [['account'], 'validateAccountActivity'],
        ];
    }

    public function validateAccountActivity(): void {
        if ($this->getAccount()->status === Account::STATUS_BANNED) {
            $this->addError('account', E::ACCOUNT_ALREADY_BANNED);
        }
    }

    public function performAction(): bool {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        $account = $this->getAccount();
        $account->status = Account::STATUS_BANNED;
        if (!$account->save()) {
            throw new ThisShouldNotHappenException('Cannot ban account');
        }

        Yii::$app->queue->push(ClearAccountSessions::createFromAccount($account));

        $transaction->commit();

        return true;
    }

}
