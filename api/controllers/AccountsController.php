<?php
namespace api\controllers;

use api\models\ChangePasswordForm;
use api\models\ChangeUsernameForm;
use common\models\Account;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AccountsController extends Controller {

    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['current'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['change-password', 'change-username'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function() {
                            /** @var Account $account */
                            $account = Yii::$app->user->identity;
                            return $account->status > Account::STATUS_REGISTERED;
                        },
                    ],
                ],
            ],
        ]);
    }

    public function verbs() {
        return [
            'current' => ['GET'],
            'change-password' => ['POST'],
            'change-username' => ['POST'],
        ];
    }

    public function actionCurrent() {
        /** @var Account $account */
        $account = Yii::$app->user->identity;

        return [
            'id' => $account->id,
            'uuid' => $account->uuid,
            'username' => $account->username,
            'email' => $account->email,
            'lang' => $account->lang,
            'shouldChangePassword' => $account->password_hash_strategy === Account::PASS_HASH_STRATEGY_OLD_ELY,
            'isActive' => $account->status === Account::STATUS_ACTIVE,
            'passwordChangedAt' => $account->password_changed_at,
            'hasMojangUsernameCollision' => $account->hasMojangUsernameCollision(),
        ];
    }

    public function actionChangePassword() {
        /** @var Account $account */
        $account = Yii::$app->user->identity;
        $model = new ChangePasswordForm($account);
        $model->load(Yii::$app->request->post());
        if (!$model->changePassword()) {
            return [
                'success' => false,
                'errors' => $this->normalizeModelErrors($model->getErrors()),
            ];
        }

        return [
            'success' => true,
        ];
    }

    public function actionChangeUsername() {
        $model = new ChangeUsernameForm();
        $model->load(Yii::$app->request->post());
        if (!$model->change()) {
            return [
                'success' => false,
                'errors' => $this->normalizeModelErrors($model->getErrors()),
            ];
        }

        return [
            'success' => true,
        ];
    }

}
