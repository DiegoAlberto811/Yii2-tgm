<?php
namespace app\components\validators;

use app\components\helpers\TranslateMessage;
use app\components\NBEmailChecker;
use NeverBounce\Object\VerificationObject;

class NeverBounceEmailValidator extends \yii\validators\EmailValidator
{
    public static $blocked = [VerificationObject::INVALID, VerificationObject::DISPOSABLE];

    public function validateAttribute($model, $attribute)
    {
        parent::validateAttribute($model, $attribute);

        if ($model->hasErrors()) {
            return;
        }

        $checking = NBEmailChecker::checkEmail($model->$attribute);

        if ($checking->is(static::$blocked)) {
            $this->addError($model, $attribute, TranslateMessage::t('user', 'Please enter correct email'));
        }
    }
}