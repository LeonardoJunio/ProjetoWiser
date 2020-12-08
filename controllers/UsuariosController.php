<?php

namespace app\controllers;

require_once('../classes/BoxAPI.class.php');

use app\classes\Box_API;
use app\models\LoginForm;
use app\models\Procedures;
use Yii;
use app\models\Usuarios;
use app\models\UsuariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UsuariosController implements the CRUD actions for Usuarios model.
 */
class UsuariosController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Displays a single Usuarios model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView()
    {
        if(isset(Yii::$app->user->identity->IdUsuario)) {
            $model = self::findModel(Yii::$app->user->identity->IdUsuario);

            return $this->render('view', ['model' => $model]);
        } else {
            return $this->redirect(['/site/login']);
        }
    }

    public function actionAlterarSenha()
    {
        if(isset(Yii::$app->user->identity->IdUsuario)) {
            $model = new Usuarios();

            if(isset(Yii::$app->request->post()['buttonAlterarSenha'])){
                $IdUsuario = Yii::$app->user->identity->IdUsuario;
                $senha = Yii::$app->request->post()['Usuarios']['password'];
                $confirmarSenha = Yii::$app->request->post()['Usuarios']['passwordConfirm'];

                if(!empty($senha) && !empty($confirmarSenha) && ($senha == $confirmarSenha)){
                    $senha = md5($senha);

                    Procedures::updateSQL('Usuarios', ['Senha' => $senha], ['IdUsuario' => $IdUsuario]);
                } else {
                    if($senha != $confirmarSenha){
                        Yii::$app->getSession()->setFlash('error', 'Senhas diferentes.');
                    } else {
                        Yii::$app->getSession()->setFlash('error', 'Algum campo não foi preenchido.');
                    }
                }

                return $this->redirect(['view']);
            }

            return $this->renderAjax('alterar-senha', ['model' => $model]);
        } else {
            return $this->redirect(['/site/login']);
        }
    }

    public function actionAlterarCredenciais()
    {
        if(isset(Yii::$app->user->identity->IdUsuario)) {
            $model = new Usuarios();

            if(isset(Yii::$app->request->post()['buttonAlterarCredenciais'])){
                $IdUsuario = Yii::$app->user->identity->IdUsuario;
                $ClientId = Yii::$app->request->post()['Usuarios']['ClientId'];
                $ClientSecret = Yii::$app->request->post()['Usuarios']['ClientSecret'];
                $UserId = Yii::$app->request->post()['Usuarios']['UserId'];

                $boxApi = new Box_API($ClientId, $ClientSecret, $UserId);
                $boxToken = $boxApi->get_token();
                //verifica se as credenciais são validas
                if(!isset($boxToken['error']) && (!empty($ClientId) && !empty($ClientSecret) && !empty($UserId))) {
                    Procedures::updateSQL('Usuarios',
                        [
                            'ClientId' => $ClientId,
                            'ClientSecret' => $ClientSecret,
                            'UserId' => $UserId
                        ],
                        ['IdUsuario' => $IdUsuario]
                    );
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Conexão com Box.com não realizada. Insira/Atualize suas Credenciais.');
                }

                return $this->redirect(['view']);
            }

            return $this->renderAjax('alterar-credenciais', ['model' => $model]);
        } else {
            return $this->redirect(['/site/login']);
        }
    }

    public function actionUpdateDadosBoxApi()
    {
        $idUsuario = Yii::$app->user->identity->IdUsuario;

        $model = $this->findModel($idUsuario);

        if(isset(Yii::$app->request->post()['buttonCredenciaisBox'])){
            $IdUsuario = Yii::$app->user->identity->IdUsuario;
            $ClientId = Yii::$app->request->post()['Usuarios']['ClientId'];
            $ClientSecret = Yii::$app->request->post()['Usuarios']['ClientSecret'];
            $UserId = Yii::$app->request->post()['Usuarios']['UserId'];

            $boxApi = new Box_API($ClientId, $ClientSecret, $UserId);
            $boxToken = $boxApi->get_token();
            //verifica se as credenciais são validas
            if(!isset($boxToken['error']) && (!empty($ClientId) && !empty($ClientSecret) && !empty($UserId))) {
                Procedures::updateSQL('Usuarios',
                    [
                        'ClientId' => $ClientId,
                        'ClientSecret' => $ClientSecret,
                        'UserId' => $UserId
                    ], ['IdUsuario' => $IdUsuario]);

                return $this->redirect(['/arquivos/index', 'idPastaAtual' => base64_encode('0')]);
            }

            Yii::$app->getSession()->setFlash('error', 'Conexão com Box.com não realizada. Insira/Atualize suas Credenciais e tente novamente.');

            return $this->redirect(['view']);
        }

        return $this->render('update-dados-box', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Usuarios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Usuarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Usuarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Usuarios::findOne($id)) !== null) {
            return $model;
        }

        return null;
    }
}
