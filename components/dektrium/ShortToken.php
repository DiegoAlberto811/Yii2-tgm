<?php
namespace app\components\dektrium;

use dektrium\user\models\Token;

/**
 * Class ShortToken
 * @package app\components\dektrium
 */
class ShortToken extends Token
{
    public $tokenSize = 5;

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        /** have to call this before token setting */
        $beforeSave = parent::beforeSave($insert);

        if ($beforeSave) {
            $this->setAttribute('code', $this->renderCode());
        }

        return $beforeSave;
    }

    protected function renderCode()
    {
        $code = '';
        $map = 'qwertyupasdfghjkzxcvbnm23456789';

        for($i = 0; $i < $this->tokenSize; ++$i) {
            $code .= $map{rand(0, strlen($map))};
        }

        return $code;
    }
}