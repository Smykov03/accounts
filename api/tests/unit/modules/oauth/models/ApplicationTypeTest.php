<?php
namespace api\tests\unit\modules\oauth\models;

use api\modules\oauth\models\ApplicationType;
use api\tests\unit\TestCase;
use common\models\OauthClient;

class ApplicationTypeTest extends TestCase {

    public function testApplyToClient(): void {
        $model = new ApplicationType();
        $model->name = 'Application name';
        $model->websiteUrl = 'http://example.com';
        $model->redirectUri = 'http://example.com/oauth/ely';
        $model->description = 'Application description.';

        $client = new OauthClient();

        $model->applyToClient($client);

        $this->assertSame('Application name', $client->name);
        $this->assertSame('Application description.', $client->description);
        $this->assertSame('http://example.com/oauth/ely', $client->redirect_uri);
        $this->assertSame('http://example.com', $client->website_url);
    }

}
