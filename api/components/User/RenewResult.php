<?php
namespace api\components\User;

use Yii;
use yii\web\IdentityInterface;

class RenewResult {

    /**
     * @var IdentityInterface
     */
    private $identity;

    /**
     * @var string
     */
    private $jwt;

    public function __construct(IdentityInterface $identity, string $jwt) {
        $this->identity = $identity;
        $this->jwt = $jwt;
    }

    public function getIdentity() : IdentityInterface {
        return $this->identity;
    }

    public function getJwt() : string {
        return $this->jwt;
    }

    public function getAsResponse() {
        /** @var Component $component */
        $component = Yii::$app->user;

        return [
            'access_token' => $this->getJwt(),
            'expires_in' => $component->expirationTimeout,
        ];
    }

}