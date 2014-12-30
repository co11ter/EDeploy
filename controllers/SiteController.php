<?php

namespace app\controllers;

use Yii;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\models\LoginForm;
use app\models\Category;
use app\models\Project;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function actionIndex()
    {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect(['site/login'], 302);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => Category::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest)
            return $this->goHome();

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Выкладка из админки.
     *
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionDeploy($id)
    {
        $model = Project::findOne($id);

        $result = '';
        $ssh = Yii::$app->sshConnector->connect(
            $model->host,
            $model->username,
            Yii::$app->getSecurity()->decryptByKey($model->password, $model->key)
        );
        if($ssh)
        {
            $result = Yii::$app->sshConnector->run($model->command);
            $model->setDeployDate();
        }
        return $this->render('deploy', [
            'message' => $result
        ]);
    }
}
