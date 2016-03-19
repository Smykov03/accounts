<?php
namespace common\models;

use common\components\UserPass;
use damirka\JWT\UserTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Поля модели:
 * @property integer $id
 * @property string  $uuid
 * @property string  $username
 * @property string  $email
 * @property string  $password_hash
 * @property integer $password_hash_strategy
 * @property string  $password_reset_token
 * @property string  $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Геттеры-сеттеры:
 * @property string  $password пароль пользователя (только для записи)
 *
 * Отношения:
 * @property EmailActivation[] $emailActivations
 * @property OauthSession[]    $sessions
 *
 * Поведения:
 * @mixin TimestampBehavior
 */
class Account extends ActiveRecord implements IdentityInterface {
    use UserTrait;

    const STATUS_DELETED = -10;
    const STATUS_REGISTERED = 0;
    const STATUS_ACTIVE = 10;

    const PASS_HASH_STRATEGY_OLD_ELY = 0;
    const PASS_HASH_STRATEGY_YII2 = 1;

    public static function tableName() {
        return '{{%accounts}}';
    }

    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules() {
        return [
            [['username'], 'filter', 'filter' => 'trim'],
            [['username'], 'required', 'message' => 'error.username_required'],
            [['username'], 'string', 'min' => 3, 'max' => 21,
                'tooShort' => 'error.username_too_short',
                'tooLong' => 'error.username_too_long',
            ],
            [['username'], 'match', 'pattern' => '/^[\p{L}\d-_\.!?#$%^&*()\[\]:;]+$/u',
                'message' => 'error.username_invalid',
            ],
            [['username'], 'unique', 'message' => 'error.username_not_available'],

            [['email'], 'filter', 'filter' => 'trim'],
            [['email'], 'required', 'message' => 'error.email_required'],
            [['email'], 'string', 'max' => 255, 'tooLong' => 'error.email_too_long'],
            [['email'], 'email', 'checkDNS' => true, 'enableIDN' => true, 'message' => 'error.email_invalid'],
            [['email'], 'unique', 'message' => 'error.email_not_available'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::findOne(['id' => $id]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     *
     * @return static|null
     *
     * TODO: этот метод нужно убрать из базовой модели
     */
    public static function findByPasswordResetToken($token) {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     *
     * @return boolean
     *
     * TODO: этот метод нужно убрать из базовой модели
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string  $password password to validate
     * @param integer $passwordHashStrategy
     *
     * @return bool if password provided is valid for current user
     * @throws InvalidConfigException
     */
    public function validatePassword($password, $passwordHashStrategy = NULL) {
        if ($passwordHashStrategy === NULL) {
            $passwordHashStrategy = $this->password_hash_strategy;
        }

        switch($passwordHashStrategy) {
            case self::PASS_HASH_STRATEGY_OLD_ELY:
                $hashedPass = UserPass::make($this->email, $password);
                return $hashedPass === $this->password_hash;

            case self::PASS_HASH_STRATEGY_YII2:
                return Yii::$app->security->validatePassword($password, $this->password_hash);

            default:
                throw new InvalidConfigException('You must set valid password_hash_strategy before you can validate password');
        }
    }

    /**
     * @param string $password
     * @throws InvalidConfigException
     */
    public function setPassword($password) {
        $this->password_hash_strategy = self::PASS_HASH_STRATEGY_YII2;
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     *
     * TODO: этот метод нужно отсюда убрать
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     *
     * TODO: этот метод нужно отсюда убрать
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    public function getEmailActivations() {
        return $this->hasMany(EmailActivation::class, ['account_id' => 'id']);
    }

    public function getSessions() {
        return $this->hasMany(OauthSession::class, ['owner_id' => 'id']);
    }

    /**
     * Метод проверяет, может ли текщий пользователь быть автоматически авторизован
     * для указанного клиента без запроса доступа к необходимому списку прав
     *
     * @param OauthClient $client
     * @param \League\OAuth2\Server\Entity\ScopeEntity[] $scopes
     *
     * @return bool
     */
    public function canAutoApprove(OauthClient $client, array $scopes = []) {
        if ($client->is_trusted) {
            return true;
        }

        /** @var OauthSession|null $session */
        $session = $this->getSessions()->andWhere(['client_id' => $client->id])->one();
        if ($session !== null) {
            $existScopes = $session->getScopes()->members();
            if (empty(array_diff(array_keys($scopes), $existScopes))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    protected static function getSecretKey() {
        return Yii::$app->params['jwtSecret'];
    }

    /**
     * Getter for "header" array that's used for generation of JWT
     * @return array JWT Header Token param, see http://jwt.io/ for details
     */
    protected static function getHeaderToken() {
        return [
            'iss' => Yii::$app->request->hostInfo,
            'aud' => Yii::$app->request->hostInfo,
        ];
    }

}
