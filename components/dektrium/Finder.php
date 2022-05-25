<?php
namespace app\components\dektrium;

class Finder extends \dektrium\user\Finder
{
    /**
     * @param $phone
     * @return array|null|\yii\db\ActiveRecord
     */
    public function findUserByPhone($phone)
    {
        return $this->findUser(['full_phone' => $phone])->one() ?:
            $this->findUser(['short_phone' => $phone])->one();
    }
}