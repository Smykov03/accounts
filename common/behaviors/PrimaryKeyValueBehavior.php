<?php
namespace common\behaviors;

use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * @property ActiveRecord $owner
 */
class PrimaryKeyValueBehavior extends Behavior {

    /**
     * @var callable Функция, что будет вызвана для генерации ключа.
     * Должна возвращать случайное значение, подходящее для логики модели. Функция будет вызываться
     * в цикле do-while с целью избежания дубликатов строк по первичному ключу, так что если функция
     * станет возвращать статичное значение, то программа зациклится и что-нибудь здохнет. Не делайте так.
     */
    public $value;

    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'setPrimaryKeyValue',
        ];
    }

    public function setPrimaryKeyValue() : bool {
        $owner = $this->owner;
        if ($owner->getPrimaryKey() === null) {
            do {
                $key = $this->generateValue();
            } while ($this->isValueExists($key));

            $owner->{$this->getPrimaryKeyName()} = $key;
        }

        return true;
    }

    protected function generateValue() : string {
        return (string)call_user_func($this->value);
    }

    protected function isValueExists(string $key) : bool {
        return $this->owner->find()->andWhere([$this->getPrimaryKeyName() => $key])->exists();
    }

    protected function getPrimaryKeyName() : string {
        $owner = $this->owner;
        $primaryKeys = $owner->primaryKey();
        if (!isset($primaryKeys[0])) {
            throw new InvalidConfigException('"' . get_class($owner) . '" must have a primary key.');
        } elseif (count($primaryKeys) > 1) {
            throw new InvalidConfigException('Current behavior don\'t support models with more then one primary key.');
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection да как бы оно всё нормально, но шторм мне не верит */
        return $primaryKeys[0];
    }

}