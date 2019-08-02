<?php
declare(strict_types=1);

namespace codeception\api\unit\modules\authserver\validators;

use api\modules\authserver\exceptions\IllegalArgumentException;
use api\modules\authserver\validators\RequiredValidator;
use api\tests\unit\TestCase;
use common\tests\_support\ProtectedCaller;

class RequiredValidatorTest extends TestCase {
    use ProtectedCaller;

    public function testValidateValueNormal() {
        $validator = new RequiredValidator();
        $this->assertNull($this->callProtected($validator, 'validateValue', 'dummy'));
    }

    public function testValidateValueEmpty() {
        $this->expectException(IllegalArgumentException::class);

        $validator = new RequiredValidator();
        $this->assertNull($this->callProtected($validator, 'validateValue', ''));
    }

}
