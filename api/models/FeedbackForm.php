<?php
namespace api\models;

use common\helpers\Error as E;
use api\models\base\ApiForm;
use Yii;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;

class FeedbackForm extends ApiForm {

    public $subject;

    public $email;

    public $type;

    public $message;

    public function rules() {
        return [
            ['subject', 'required', 'message' => E::SUBJECT_REQUIRED],
            ['email', 'required', 'message' => E::EMAIL_REQUIRED],
            ['message', 'required', 'message' => E::MESSAGE_REQUIRED],
            [['subject'], 'string', 'max' => 255],
            [['email'], 'email', 'message' => E::EMAIL_INVALID],
            [['message'], 'string', 'max' => 65535],
        ];
    }

    public function sendMessage() : bool {
        if (!$this->validate()) {
            return false;
        }

        /** @var \yii\swiftmailer\Mailer $mailer */
        $mailer = Yii::$app->mailer;
        $fromEmail = Yii::$app->params['supportEmail'];
        if (!$fromEmail) {
            throw new InvalidConfigException('Please specify supportEmail value in app params');
        }

        $account = $this->getAccount();
        /** @var \yii\swiftmailer\Message $message */
        $message = $mailer->compose('@app/mails/feedback', [
            'model' => $this,
            'account' => $account,
        ]);
        $message
            ->setTo($this->email)
            ->setFrom([$this->email => $account ? $account->username : $this->email])
            ->setSubject($this->subject);

        if (!$message->send()) {
            throw new ErrorException('Unable send feedback email.');
        }

        return true;
    }

    /**
     * @return \common\models\Account|null $account
     */
    protected function getAccount() {
        return Yii::$app->user->identity;
    }

}
