<?php
namespace api\modules\mojang\controllers;

use api\controllers\Controller;
use common\models\Account;
use common\models\UsernameHistory;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\web\Response;

class ApiController extends Controller {

    public function behaviors(): array {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        return $behaviors;
    }

    public function actionUuidByUsername($username, $at = null) {
        if ($at !== null) {
            /** @var UsernameHistory|null $record */
            $record = UsernameHistory::find()
                ->andWhere(['username' => $username])
                ->orderBy(['applied_in' => SORT_DESC])
                ->andWhere(['<=', 'applied_in', $at])
                ->one();

            // The query above simply finds the latest case of usage, without taking into account the fact
            // that the nickname may have been changed since then. Therefore, we additionally check
            // that the nickname is in some period (i.e. there is a subsequent entry) or that the last user
            // who used the nickname has not changed it to something else
            $account = null;
            if ($record !== null) {
                if ($record->account->username === $record->username || $record->findNext($at) !== null) {
                    $account = $record->account;
                }
            }
        } else {
            /** @var Account|null $record */
            $account = Account::findOne(['username' => $username]);
        }

        if ($account === null) {
            return $this->noContentResponse();
        }

        return [
            'id' => str_replace('-', '', $account->uuid),
            'name' => $account->username,
        ];
    }

    public function actionUsernamesByUuid($uuid) {
        try {
            $uuid = Uuid::fromString($uuid)->toString();
        } catch (\InvalidArgumentException $e) {
            return $this->illegalArgumentResponse('Invalid uuid format.');
        }

        $account = Account::findOne(['uuid' => $uuid]);
        if ($account === null) {
            return $this->noContentResponse();
        }

        /** @var UsernameHistory[] $usernameHistory */
        $usernameHistory = $account->getUsernameHistory()
            ->orderBy(['applied_in' => SORT_ASC])
            ->all();

        $data = [];
        foreach ($usernameHistory as $record) {
            $data[] = [
                'name' => $record->username,
                'changedToAt' => $record->applied_in * 1000,
            ];
        }

        // The first element shouldn't have time when it was applied.
        // Although we know this information in fact. But Mojang probably doesn't.
        unset($data[0]['changedToAt']);

        return $data;
    }

    public function actionUuidsByUsernames() {
        $usernames = Yii::$app->request->post();
        if (empty($usernames)) {
            return $this->illegalArgumentResponse('Passed array of profile names is an invalid JSON string.');
        }

        $usernames = array_unique($usernames);
        if (count($usernames) > 100) {
            return $this->illegalArgumentResponse('Not more that 100 profile name per call is allowed.');
        }

        foreach ($usernames as $username) {
            if (empty($username) || is_array($username)) {
                return $this->illegalArgumentResponse('profileName can not be null, empty or array key.');
            }
        }

        /** @var Account[] $accounts */
        $accounts = Account::find()
            ->andWhere(['username' => $usernames])
            ->orderBy(['username' => $usernames])
            ->limit(count($usernames))
            ->all();

        $responseData = [];
        foreach ($accounts as $account) {
            $responseData[] = [
                'id' => str_replace('-', '', $account->uuid),
                'name' => $account->username,
            ];
        }

        return $responseData;
    }

    private function noContentResponse() {
        $response = Yii::$app->getResponse();
        $response->setStatusCode(204);
        $response->format = Response::FORMAT_RAW;
        $response->content = '';

        return $response;
    }

    private function illegalArgumentResponse(string $errorMessage) {
        $response = Yii::$app->getResponse();
        $response->setStatusCode(400);
        $response->format = Response::FORMAT_JSON;
        $response->data = [
            'error' => 'IllegalArgumentException',
            'errorMessage' => $errorMessage,
        ];

        return $response;
    }

}
