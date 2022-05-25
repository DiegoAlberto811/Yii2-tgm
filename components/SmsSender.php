<?php
namespace app\components;

use Clx\Xms\Api\MtBatchTextSmsCreate;
use Clx\Xms\Client;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class SmsSender extends Component
{
    public static function send($recipient, $message)
    {
        $yiiParams = \Yii::$app->params;

        if (!ArrayHelper::getValue($yiiParams, 'clx')) {
            \Yii::info('[CLX] SMS sender is not configured.');
            return;
        }

        $servicePlanId = ArrayHelper::getValue($yiiParams, 'clx.id');
        $token = ArrayHelper::getValue($yiiParams, 'clx.token');
        $sender = ArrayHelper::getValue($yiiParams, 'clx.sender');

        $client = new Client($servicePlanId, $token);
        $batchParams = new MtBatchTextSmsCreate();
        $batchParams->setSender($sender);
        $batchParams->setRecipients([(string)$recipient]);
        $batchParams->setBody($message);

        $client->createTextBatch($batchParams);
    }
}