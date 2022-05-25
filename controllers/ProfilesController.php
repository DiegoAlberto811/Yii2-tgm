<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 22/03/2018
 * Time: 10:11
 */

namespace app\controllers;

use yii\rest\ActiveController;

class ProfilesController extends ActiveController
{
	public $modelClass = 'app\models\Profile';
}