<?php
namespace app\components;

use app\models\translation\SourceMessage;
use yii\base\Exception;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

class TgmMigration extends Migration
{
    /**
     * Loads translations
     * @param string $category
     * @param array $translations
     * @throws Exception
     */
    protected function addTranslations($category, array $translations)
    {
        foreach($translations as $messages) {
            $sourceMessage = ArrayHelper::getValue($messages, 'en');
            if (!$sourceMessage) {
                throw new Exception('Source message not found');
            }

            $sourceModel = $this->addSourceMessage($category, $sourceMessage);

            foreach($messages as $lang => $message) {
                $sourceModel->addTranslation($lang, $message);
            }
        }
    }

    /**
     * Adds translation source message in $category
     * @param $category
     * @param $message
     * @return SourceMessage
     * @throws Exception
     */
    protected function addSourceMessage($category, $message)
    {
        $sourceMessage = new SourceMessage([
            'category' => $category,
            'message' => $message,
        ]);

        if (!$sourceMessage->save()) {
            throw new Exception('Error add translastion for: ' . $message);
        }

        return $sourceMessage;
    }

    /**
     * Removes translations, for rollback purpose
     * @param $category
     * @param array $translations
     * @throws
     */
    protected function removeTranslations($category, array $translations)
    {
        foreach($translations as $messages) {
            $sourceMessage = ArrayHelper::getValue($messages, 'en');
            if (!$sourceMessage) {
                throw new Exception('Source message not found');
            }

            $sourceModel = SourceMessage::findOne([
                'category' => $category,
                'message' => $sourceMessage,
            ]);

            if ($sourceModel) {
                $sourceModel->delete();
            }
        }
    }
}