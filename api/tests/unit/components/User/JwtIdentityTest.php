<?php
declare(strict_types=1);

namespace codeception\api\unit\components\User;

use api\components\User\JwtIdentity;
use api\tests\unit\TestCase;
use Carbon\Carbon;
use common\tests\fixtures\AccountFixture;
use common\tests\fixtures\OauthClientFixture;
use common\tests\fixtures\OauthSessionFixture;
use yii\web\UnauthorizedHttpException;

class JwtIdentityTest extends TestCase {

    public function _fixtures(): array {
        return [
            'accounts' => AccountFixture::class,
            'oauthClients' => OauthClientFixture::class,
            'oauthSessions' => OauthSessionFixture::class,
        ];
    }

    public function testFindIdentityByAccessToken() {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiJ9.eyJlbHktc2NvcGVzIjoiYWNjb3VudHNfd2ViX3VzZXIiLCJpYXQiOjE1NjQ2MTA1NDIsImV4cCI6MTU2NDYxNDE0Miwic3ViIjoiZWx5fDEifQ.4Oidvuo4spvUf9hkpHR72eeqZUh2Zbxh_L8Od3vcgTj--0iOrcOEp6zwmEW6vF7BTHtjz2b3mXce61bqsCjXjQ';
        /** @var JwtIdentity $identity */
        $identity = JwtIdentity::findIdentityByAccessToken($token);
        $this->assertSame($token, $identity->getId());
        $this->assertSame($token, (string)$identity->getToken());
        /** @var \common\models\Account $account */
        $account = $this->tester->grabFixture('accounts', 'admin');
        $this->assertSame($account->id, $identity->getAccount()->id);
    }

    /**
     * @dataProvider getFindIdentityByAccessTokenInvalidCases
     */
    public function testFindIdentityByAccessTokenInvalidCases(string $token, string $expectedExceptionMessage) {
        $this->expectException(UnauthorizedHttpException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        JwtIdentity::findIdentityByAccessToken($token);
    }

    public function getFindIdentityByAccessTokenInvalidCases() {
        yield 'expired token' => [
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiJ9.eyJlbHktc2NvcGVzIjoiYWNjb3VudHNfd2ViX3VzZXIiLCJpYXQiOjE1NjQ2MDMzNDIsImV4cCI6MTU2NDYwNjk0Miwic3ViIjoiZWx5fDEifQ.36cDWyiXRArv-lgK_S5dyC5m_Ddytwkb78tMrxcPcbWEpoeg2VtwPC7zr6NI0cd0CuLw6InC2hZ9Ey95SSOsHw',
            'Token expired',
        ];
        yield 'iat from future' => [
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiJ9.eyJlbHktc2NvcGVzIjoiYWNjb3VudHNfd2ViX3VzZXIiLCJpYXQiOjE1NjQ2MTc3NDIsImV4cCI6MTU2NDYxNDE0Miwic3ViIjoiZWx5fDEifQ._6hj6XUSmSLibgT9ZE1Pokf4oI9r-d6tEc1z2J-fBlr1710Qiso5yNcXqb3Z_xy7Qtemyq8jOlOZA8DvmkVBrg',
            'Incorrect token',
        ];
        yield 'revoked by oauth client' => [
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiJ9.eyJlbHktc2NvcGVzIjoiYWNjb3VudF9pbmZvLG1pbmVjcmFmdF9zZXJ2ZXJfc2Vzc2lvbiIsImlhdCI6MTU2NDYxMDUwMCwic3ViIjoiZWx5fDEiLCJhdWQiOiJjbGllbnR8dGxhdW5jaGVyIn0.YzUzvnREEoQPu8CvU6WLdysUU0bC_xzigQPs2LK1su38uysSYgSbPzNOZYkQnvcmVLehHY-ON44x-oA8Os-9ZA',
            'Token has been revoked',
        ];
        yield 'revoked by unauthorized minecraft launcher' => [
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiJ9.eyJlbHktc2NvcGVzIjoibWluZWNyYWZ0X3NlcnZlcl9zZXNzaW9uIiwiZWx5LWNsaWVudC10b2tlbiI6IllBTVhneTBBcEI5Z2dUL1VYNjNJaTdKcGtNd2ZwTmxaaE8yVVVEeEZ3YTFmZ2g4dksyN0RtV25vN2xqbk1pWWJwQ1VuS09YVnR2V1YrVVg1dWRQVVFsK04xY3BBZlJBL2ErZW1BZz09IiwiaWF0IjoxNTY0NjEwNTAwLCJzdWIiOiJlbHl8MSJ9.mxFgf4M1QSG4_Zd3sGoJUx9L9_XbjHd4T8-CWIVzmSPp2_9OHjq-CIFEwSwlfoz3QGN7NV0TpC8-PfRvjd93eQ',
            'Token has been revoked',
        ];
        yield 'invalid signature' => [
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiJ9.eyJlbHktc2NvcGVzIjoiYWNjb3VudHNfd2ViX3VzZXIiLCJpYXQiOjE1NjQ2MTA1NDIsImV4cCI6MTU2NDYxNDE0Miwic3ViIjoiZWx5fDEifQ.yth31f2PyhUkYSfBlizzUXWIgOvxxk8gNP-js0z8g1OT5rig40FPTIkgsZRctAwAAlj6QoIWW7-hxLTcSb2vmw',
            'Incorrect token',
        ];
        yield 'empty token' => ['', 'Incorrect token'];
    }

    public function testGetAccount() {
        // Token with sub claim
        $identity = JwtIdentity::findIdentityByAccessToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiJ9.eyJlbHktc2NvcGVzIjoiYWNjb3VudHNfd2ViX3VzZXIiLCJpYXQiOjE1NjQ2MTA1NDIsImV4cCI6MTU2NDYxNDE0Miwic3ViIjoiZWx5fDEifQ.4Oidvuo4spvUf9hkpHR72eeqZUh2Zbxh_L8Od3vcgTj--0iOrcOEp6zwmEW6vF7BTHtjz2b3mXce61bqsCjXjQ');
        $this->assertSame(1, $identity->getAccount()->id);

        // Sub presented, but account not exists
        $identity = JwtIdentity::findIdentityByAccessToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiJ9.eyJlbHktc2NvcGVzIjoiYWNjb3VudHNfd2ViX3VzZXIiLCJpYXQiOjE1NjQ2MTA1NDIsImV4cCI6MTU2NDYxNDE0Miwic3ViIjoiZWx5fDk5OTk5In0.1pAnhkR-_ZqzjLBR-PNIMJUXRSUK3aYixrFNKZg2ynPNPiDvzh8U-iBTT6XRfMP5nvfXZucRpoPVoiXtx40CUQ');
        $this->assertNull($identity->getAccount());

        // Sub contains invalid value
        $identity = JwtIdentity::findIdentityByAccessToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiJ9.eyJlbHktc2NvcGVzIjoiYWNjb3VudHNfd2ViX3VzZXIiLCJpYXQiOjE1NjQ2MTA1NDIsImV4cCI6MTU2NDYxNDE0Miwic3ViIjoxMjM0fQ.yigP5nWFdX0ktbuZC_Unb9bWxpAVd7Nv8Fb1Vsa0t5WkVA88VbhPi2P-CenbDOy8ngwoGV9m3c3upMs2V3gqvw');
        $this->assertNull($identity->getAccount());

        // Token without sub claim
        $identity = JwtIdentity::findIdentityByAccessToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiJ9.eyJlbHktc2NvcGVzIjoiYWNjb3VudHNfd2ViX3VzZXIiLCJpYXQiOjE1NjQ2MTA1NDIsImV4cCI6MTU2NDYxNDE0Mn0.QxmYgSflZOQmhzYRr8bowU767yu4yKgTVaho0MPuyCmUfZO_0O0SQASMKVILf-wlT0ODTTG7vD753a2MTAmPmw');
        $this->assertNull($identity->getAccount());
    }

    public function testGetAssignedPermissions() {
        // Token with ely-scopes claim
        $identity = JwtIdentity::findIdentityByAccessToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiJ9.eyJlbHktc2NvcGVzIjoicGVybTEscGVybTIscGVybTMiLCJpYXQiOjE1NjQ2MTA1NDIsImV4cCI6MTU2NDYxNDE0Miwic3ViIjoiZWx5fDEifQ.MO6T92EOFcZSPIdK8VBUG0qyV-pdayzOPQmpWLPwpl1933E9ann9GdV49piX1IfLHeCHVGThm5_v7AJgyZ5Oaw');
        $this->assertSame(['perm1', 'perm2', 'perm3'], $identity->getAssignedPermissions());

        // Token without sub claim
        $identity = JwtIdentity::findIdentityByAccessToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiJ9.eyJpYXQiOjE1NjQ2MTA1NDIsImV4cCI6MTU2NDYxNDE0Miwic3ViIjoiZWx5fDEifQ.jsjv2dDetSxu4xivlHoTeDUhqsl-cxSI6SktufJhwR9wqDgQCVIONiqQCUzTzyTwyAz4Ztvel4lKjMCstdJOEw');
        $this->assertSame([], $identity->getAssignedPermissions());
    }

    protected function _before() {
        parent::_before();
        Carbon::setTestNow(Carbon::create(2019, 8, 1, 1, 2, 22, 'Europe/Minsk'));
    }

    protected function _after() {
        parent::_after();
        Carbon::setTestNow();
    }

}
