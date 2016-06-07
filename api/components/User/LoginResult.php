<?php
namespace api\components\User;

use common\models\AccountSession;
use Yii;
use yii\web\IdentityInterface;

class LoginResult {
    /**
     * @var IdentityInterface
     */
    private $identity;

    /**
     * @var string
     */
    private $jwt;

    /**
     * @var AccountSession|null
     */
    private $session;

    public function __construct(IdentityInterface $identity, string $jwt, AccountSession $session = null) {
        $this->identity = $identity;
        $this->jwt = $jwt;
        $this->session = $session;
    }

    public function getIdentity() : IdentityInterface {
        return $this->identity;
    }

    public function getJwt() : string {
        return $this->jwt;
    }

    /**
     * @return AccountSession|null
     */
    public function getSession() {
        return $this->session;
    }

    public function getAsResponse() {
        /** @var Component $component */
        $component = Yii::$app->user;
        $response = [
            'access_token' => $this->getJwt(),
            'expires_in' => $component->expirationTimeout,
        ];

        $session = $this->getSession();
        if ($session !== null) {
            $response['refresh_token'] = $session->refresh_token;
        }

        return $response;
    }

}