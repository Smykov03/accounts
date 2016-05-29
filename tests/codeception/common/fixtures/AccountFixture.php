<?php
namespace tests\codeception\common\fixtures;

use common\models\Account;
use yii\test\ActiveFixture;

class AccountFixture extends ActiveFixture {

    public $modelClass = Account::class;

    public $dataFile = '@tests/codeception/common/fixtures/data/accounts.php';

}
