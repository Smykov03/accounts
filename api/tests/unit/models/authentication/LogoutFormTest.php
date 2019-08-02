<?php
declare(strict_types=1);

namespace api\tests\_support\models\authentication;

use api\components\User\Component;
use api\models\authentication\LogoutForm;
use api\tests\unit\TestCase;
use Codeception\Specify;
use common\models\AccountSession;
use Yii;

class LogoutFormTest extends TestCase {
    use Specify;

    public function testValidateLogout() {
        $this->specify('No actions if active session is not exists', function() {
            $userComp = $this
                ->getMockBuilder(Component::class)
                ->setMethods(['getActiveSession'])
                ->getMock();
            $userComp
                ->expects($this->any())
                ->method('getActiveSession')
                ->will($this->returnValue(null));

            Yii::$app->set('user', $userComp);

            $model = new LogoutForm();
            $this->assertTrue($model->logout());
        });

        $this->specify('if active session is presented, then delete should be called', function() {
            $session = $this
                ->getMockBuilder(AccountSession::class)
                ->setMethods(['delete'])
                ->getMock();
            $session
                ->expects($this->once())
                ->method('delete')
                ->willReturn(true);

            $userComp = $this
                ->getMockBuilder(Component::class)
                ->setMethods(['getActiveSession'])
                ->getMock();
            $userComp
                ->expects($this->any())
                ->method('getActiveSession')
                ->will($this->returnValue($session));

            Yii::$app->set('user', $userComp);

            $model = new LogoutForm();
            $model->logout();
        });
    }

}
