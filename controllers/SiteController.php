<?php

namespace app\controllers;

use app\models\Procedures;
use app\models\User;
use app\models\Usuarios;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(){
        if (Yii::$app->user->isGuest) {
            return $this->render('homeStart');
        } else {
            return $this->render('about');
        }
    }

    //cadastrar novo usuario
    public function actionCreate(){
        $this->layout = 'main-login';
        $usuario = new LoginForm();

        if(isset(Yii::$app->request->post()['buttonCreate'])){
            $nome =  str_replace("'", "''", Yii::$app->request->post()['LoginForm']['username']);
            $senha = Yii::$app->request->post()['LoginForm']['password'];
            $confirmarSenha = Yii::$app->request->post()['LoginForm']['passwordConfirm'];

            if (!empty($nome) && !empty($senha) && !empty($confirmarSenha)) {
                $select = Yii::$app->getDb()->createCommand("Select Usuario from Usuarios")->queryAll();
                $checkNovoUsuario = true;

                //verifica se tem algum registro no banco com aquele usuario
                foreach ($select as $item) {
                    if ($nome == $item['Usuario']) {
                        $checkNovoUsuario = false;
                    }
                }

                if ($senha == $confirmarSenha && $usuario->load(Yii::$app->request->post()) && $checkNovoUsuario) {
                    $senha = md5($senha);

                    date_default_timezone_set('America/Recife');

                    Procedures::insertSQL('Usuarios', [
                        'Usuario' => $nome,
                        'Senha' => $senha,
                        'DataCadastro' => date("Y-m-d H:i:s"),
                        'Status' => 1
                    ]);

                    if ($usuario->login()) {
                        return $this->goHome();
                    }
                } else {
                    if(!$checkNovoUsuario){
                        Yii::$app->getSession()->setFlash('error', 'Usuário já cadastrado no sistema.');
                    } else if ($senha != $confirmarSenha){
                        Yii::$app->getSession()->setFlash('error', 'Senhas diferentes.');
                    }
                }
            } else {
                Yii::$app->getSession()->setFlash('error', 'Possui campos não preenchidos.');
            }

            $usuario = new LoginForm();

            return $this->render('cadastrar', ['usuario' => $usuario]);
        }

        return $this->render('cadastrar', ['usuario' => $usuario]);
    }

    public function actionView($id){
        $usuario = Usuarios::findOne($id);
        return $this->render('view', ['usuario' => $usuario]);
    }


    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = 'main-login';
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        }

        $model->password = '';
        return $this->render('login', ['model' => $model]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
