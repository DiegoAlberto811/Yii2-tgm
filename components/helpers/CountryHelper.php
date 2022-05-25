<?php
namespace app\components\helpers;

use app\components\IpChecker;
use app\models\Country;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

/**
 * Class CountryHelper
 * Simplifies work with country stuff
 * @package app\components\helpers
 */
class CountryHelper extends BaseObject
{
    const PHONECODE_DEFAULT = '91';

    /**
     * Returns the phone code for the user location country
     * @return string
     */
    public static function getUserLocationCode()
    {
        $ip = ArrayHelper::getValue(\Yii::$app->params, 'localIp.ip', false);
        $ip = $ip ?: \Yii::$app->request->userIP;

        $countryCode = ArrayHelper::getValue(IpChecker::getDetails($ip), 'code', null);

        $phoneCode = Country::find()->select('phone_code')->where(['code' => $countryCode])->scalar();

        return $phoneCode ?: self::PHONECODE_DEFAULT;
    }

    /**
     * Returns list of country phone codes
     * @return array key is a country phone code, value is a country name
     */
    public static function getCodes()
    {
        $countries = Country::find()->select(['name', 'phone_code'])->asArray()->all();

        return ArrayHelper::map($countries, 'phone_code', 'name');
    }
}