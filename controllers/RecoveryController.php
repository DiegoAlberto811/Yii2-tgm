<?php
namespace app\controllers;

use app\components\helpers\CountryHelper;
use app\models\forms\RecoveryForm;
use yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

class RecoveryController extends Controller
{
    public $layout = 'index';

    public function actionIndex()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect(['/']);
        }

        return $this->render('index', [
            'countries' => CountryHelper::getCodes(),
            'code' => CountryHelper::getUserLocationCode(),
        ]);
    }

    /**
     * @throws
     */
    public function actionSendCode()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $scenarios = [
            'phone' => RecoveryForm::SCENARIO_REQUEST_BY_PHONE,
            'email' => RecoveryForm::SCENARIO_REQUEST_BY_EMAIL,
        ];
        $model = $this->loadModel($this->getScenario($scenarios));

        if (!$model->sendRecoveryMessage()) {
            if ($model->hasErrors()) {
                $errors = $model->getFirstErrors();
                throw new BadRequestHttpException(reset($errors));
            } else {
                throw new yii\web\ServerErrorHttpException('Internal server error');
            }
        }

        return ['result' => true];
    }

    /**
     * @throws
     */
    public function actionCheckCode()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $scenarios = [
            'phone' => RecoveryForm::SCENARIO_CHECK_BY_PHONE,
            'email' => RecoveryForm::SCENARIO_CHECK_BY_EMAIL,
        ];
        $model = $this->loadModel($this->getScenario($scenarios));

        if (!$model->checkTokenCode()) {
            if ($model->hasErrors()) {
                $error = $model->getFirstErrors();
                throw new yii\web\UnprocessableEntityHttpException(reset($error));
            } else {
                throw new yii\web\ServerErrorHttpException('Internal server error');
            }
        }

        return ['result' => true];
    }

    /**
     * @throws
     */
    public function actionResetPassword()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $scenarios = [
            'phone' => RecoveryForm::SCENARIO_RESET_BY_PHONE,
            'email' => RecoveryForm::SCENARIO_RESET_BY_EMAIL,
        ];
        $model = $this->loadModel($this->getScenario($scenarios));

        if (!$model->setNewPassword()) {
            if ($model->hasErrors()) {
                $error = $model->getFirstErrors();
                throw new yii\web\UnprocessableEntityHttpException(reset($error));
            } else {
                throw new yii\web\ServerErrorHttpException('Internal server error');
            }
        }

        return ['result' => true];
    }

    /**
     * @param array $scenarios
     * @return mixed
     * @throws string
     */
    protected function getScenario($scenarios = [])
    {
        $scenario = ArrayHelper::getValue($scenarios, Yii::$app->request->post('type'));
        if (!$scenario || !Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

        return $scenario;
    }

    /**
     * @param string $scenario
     * @return RecoveryForm
     * @throws \Throwable
     */
    protected function loadModel($scenario)
    {
        try {
            /** @var RecoveryForm $model */
            $model = \Yii::createObject([
                'class' => RecoveryForm::class,
                'scenario' => $scenario,
            ]);
        } catch (\Throwable $e) {
            Yii::error($e->getMessage());

            throw $e;
        }

        if (!$model->load(Yii::$app->request->post())) {
            throw new BadRequestHttpException();
        }

        return $model;
    }
}