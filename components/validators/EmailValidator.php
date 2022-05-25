<?php
namespace app\components\validators;

use app\components\EmailChecker;

class EmailValidator extends \yii\validators\EmailValidator
{
    public function validateAttribute($model, $attribute)
    {
        parent::validateAttribute($model, $attribute);

        if (!EmailChecker::getDetails($model->{$attribute})['valid']) {
            $this->addError($model, $attribute, 'Please enter correct email');
        }
    }
}