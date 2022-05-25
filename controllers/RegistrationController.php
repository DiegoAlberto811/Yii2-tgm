<?php
namespace app\controllers;

use app\components\helpers\CountryHelper;
use app\components\SendVerificationSms;
use app\models\forms\RegistrationForm;
use app\models\User;
use app\models\VerificationCode;
use yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class RegistrationController
 * Extends Dekstrium module with required functionality
 * @package app\controllers
 */
class RegistrationController extends Controller
{
    public function beforeAction($action)
    {
        if ($this->action->id !== 'index') {
            \Yii::$app->response->format = Response::FORMAT_JSON;

            if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
                throw new BadRequestHttpException();
            }
        }

        return parent::beforeAction($action);
    }

    /**
     * Displays registration layout
     * @return string
     */
    public function actionIndex()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect(['/']);
        }

        return $this->render('index', [
            'model' => new User(['scenario' => 'phone']),
            'countries' => CountryHelper::getCodes(),
            'code' => CountryHelper::getUserLocationCode(),
        ]);
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function actionSendCode()
    {
        $model = new User(['scenario' => 'phone']);

        if (!$model->load(Yii::$app->request->post())) {
            throw new BadRequestHttpException();
        }

        if (!$model->validate()) {
            $errors = $model->getFirstErrors();
            throw new UnprocessableEntityHttpException(reset($errors));
        }

        $verCode = SendVerificationSms::send($model->full_phone);
        if ($verCode->hasErrors()) {
            $error = $verCode->getFirstError('phone');
            throw new UnprocessableEntityHttpException($error);
        }

        return ['success' => true];
    }

    /**
     * @return array
     * @throws
     */
    public function actionVerifyCode()
    {
        $model = new VerificationCode(['scenario' => VerificationCode::SCENARIO_VERIFY]);

        if (!$model->load(Yii::$app->request->post())) {
            throw new BadRequestHttpException();
        }

        if (!$model->verify()) {
            $error = $model->getFirstErrors();
            throw new UnprocessableEntityHttpException(reset($error));
        }

        return ['success' => true];
    }

    /**
     * @return array
     * @throws
     */
    public function actionRegister()
    {
        $model = new RegistrationForm();

        if (!$model->load(Yii::$app->request->post())) {
            throw new BadRequestHttpException();
        }

        if (!$model->register()) {
            $errors = $model->getFirstErrors();
            throw new UnprocessableEntityHttpException(reset($errors));
        }

        return ['success' => true];
    }
}