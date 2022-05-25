<?php
namespace app\components;

use app\models\EmailCache;
use yii\base\Component;
use yii\helpers\Json;

class EmailChecker extends Component
{
    public static function getDetails($email)
    {
        $cached = EmailCache::findOne(['email' => $email]);

        if (!is_null($cached)) {
            return static::cacheToResult($cached);
        }

        $cached = static::checkEmail($email);

        return static::cacheToResult($cached);
    }

    protected static function cacheToResult(EmailCache $cache)
    {
        return [
            'email' => $cache->email,
            'valid' => $cache->valid,
            'score' => $cache->score,
        ];
    }

    protected static function checkEmail($email)
    {
        $emailCheck = static::sendRequestMailboxlayer($email);

        $emailCache = new EmailCache();
        $emailCache->load($emailCheck);
        $emailCache->save();

        return $emailCache;
    }

    protected static function sendRequestMailboxlayer($email)
    {
        $accessKey = \Yii::$app->params['mailboxlayer']['key'];
        $url = 'http://apilayer.net/api/check?access_key=' . $accessKey . '&email=' . $email;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);

        return Json::decode($json);
    }
}
