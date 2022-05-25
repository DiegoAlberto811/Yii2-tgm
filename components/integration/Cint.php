<?php
namespace app\components\integration;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\Response;
use yii\web\ServerErrorHttpException;

class Cint extends Component
{
    public $url;
    public $apiKey;
    public $apiSecret;

    /** @var Client */
    protected $client;

    /** @var string */
    protected $authKey;

    /** @var array Panel details */
    protected $panel;

    /** @var array Links values */
    protected $links;

    const SELF = 'self';
    const PANELISTS = 'panelists';

    public function init()
    {
        parent::init();

        if (!$this->url || !$this->apiKey || !$this->apiSecret) {
            throw new InvalidConfigException();
        }

        $this->client = new Client();

        $this->authKey = base64_encode("{$this->apiKey}:{$this->apiSecret}");
        $this->links = [
            self::SELF => [
                'href' => "{$this->url}/panels/{$this->apiKey}",
                'type' => 'application/json',
            ],
        ];
    }

    /**
     * @param string $rel
     * @param string $method
     * @return Request
     */
    protected function getRequest($rel, $method = 'GET')
    {
        return $this->client->createRequest()
            ->setMethod($method)
            ->setUrl($this->links[$rel]['href'])
            ->addHeaders([
                'Authorization' => "Basic {$this->authKey}",
                'Accept' => $this->links[$rel]['type'],
                'Content-type' => $this->links[$rel]['type'],
            ]);
    }

    /**
     * @return bool
     * @throws ServerErrorHttpException
     */
    public function check()
    {
        $response = $this->getRequest(self::SELF)->send();

        if (!$response->isOk) {
            throw new ServerErrorHttpException('Cint error');
        }

        $this->panel = ArrayHelper::getValue($response->data, 'panel', []);
        $this->links = ArrayHelper::index(ArrayHelper::getValue($response->data, 'links', []), 'rel');

        return true;
    }

    /**
     * @param $attributes
     * @return bool
     */
    public function registerPanelist($attributes)
    {
        /** @var Response $response */
        $response = $this->getRequest(self::PANELISTS, 'POST')
            ->setContent(Json::encode(['panelist' => $attributes]))
            ->send();

        if (!$response->isOk) {
            \Yii::error('[Cint] Error registering panelist: ' . $response->content);
            return false;
        }

        return ArrayHelper::getValue($response->data, 'panelist.key');
    }
}