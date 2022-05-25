<?php

namespace app\components\validators;

use app\components\helpers\TranslateMessage;
use yii\validators\Validator;

class PhoneValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (!ctype_digit($model->{$attribute})) {
            $this->addError($model, $attribute, TranslateMessage::t('user', 'Phone number should contain digits only'));
            return;
        }

        if (mb_strlen((string)$model->{$attribute}) < 10) {
            $this->addError($model, $attribute, TranslateMessage::t('user', 'Phone number should contain 10 digits at least'));
            return;
        }

        $phoneDetails = \app\components\MobileChecker::getDetails($model->$attribute);

        if (!$phoneDetails['valid']) {
            $this->addError($model, $attribute, TranslateMessage::t('user', 'Please enter correct phone number'));
        }
    }
}