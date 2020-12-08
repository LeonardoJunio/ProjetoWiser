<?php

namespace app\controllers;

require_once('../classes/BoxAPI.class.php');

use app\classes\Box_API;
use app\models\Constants;
use app\models\Procedures;
use DateTime;
use DateTimeZone;
use Yii;
use app\models\Arquivos;
use app\models\ArquivosSearch;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ArquivosController implements the CRUD actions for Arquivos model.
 */
class ArquivosController extends Controller
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
//                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Arquivos models.
     * @param int $idPasta
     * @return mixed
     */
    //0 como default por segurança, apesar de enviar um encode(idPasta)
    public function actionIndex($idPastaAtual = '0')
    {
        $verificarDados = self::actionVerificarDados();
        if($verificarDados) {
            $box = self::actionConnectionBoxApi();

            if($box === false){
                return $this->redirect(['/site/about']);
            }

            $idPastaAnterior = '0';
            if($idPastaAtual != '0'){ //pasta diferente da origem
                $idPastaAtual = base64_decode($idPastaAtual);
                $getInfoFolder = $box->get_folder_details($idPastaAtual);
                //guarda o id da pasta anterior pra usar quando quiser voltar
                $idPastaAnterior = $getInfoFolder['parent']['id'];
            }

            $itemsUser = $box->get_folder_items($idPastaAtual);

            $searchModel = new ArquivosSearch();

            if(isset($itemsUser['total_count'], $itemsUser['entries'])) {
                $total_count = $itemsUser['total_count'];

                $dataProvider = new ArrayDataProvider([
                    'allModels' => $itemsUser['entries'],
                    'pagination' => [
                        'pageSize' => 10,
                    ],
                ]);

                return $this->render('index', [
                    'searchModel' => $searchModel,
                    'total_count' => $total_count,
                    'dataProvider' => $dataProvider,
                    'idPastaAtual' => $idPastaAtual,
                    'idPastaAnterior' => $idPastaAnterior,
                ]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Não foi possivel carregar os dados do arquivo. Tente novamente.');
                return $this->redirect(['/site/about']);
            }
        } else {
            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
                return $this->redirect(['/usuarios/update-dados-box-api']);
            } else {
                return $this->redirect(['/site/login']);
            }
        }
    }

    /**
     * Creates a new Arquivos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($idPastaAtual)
    {
        $verificarDados = self::actionVerificarDados();
        if($verificarDados) {
            $model = new Arquivos();

            if (isset(Yii::$app->request->post()['buttonCreate']) and $_FILES) {
                $model->Diretorio = UploadedFile::getInstance($model, 'Diretorio');

                $idUsuario = Yii::$app->user->identity->IdUsuario;

                $nome = $_FILES['Arquivos']['name']['Diretorio'];

                $Diretorio = Yii::$app->basePath . "/web/Arquivos/$idUsuario/";

                //sobre o arquivo pro local e depois pro box.com e insere/atualiza no sql, so pra ter um registro / segurança
                //caso o nome seja igual, ele sobe como uma atualizacao de versao do arquivo de nome semelhante
                if($_FILES['Arquivos']['size']['Diretorio'] <= 5000000) {
                    $envio = self::actionUploadArquivo($model, $Diretorio, $nome);

                    if (isset($envio) && $envio) {
                        $RELATIVE_FILE_URL = $Diretorio . $nome;

                        $box = self::actionConnectionBoxApi();

                        if ($box === false) {
                            return $this->redirect(['/site/about']);
                        }

                        $idPastaAtual = base64_decode($idPastaAtual);

                        //para enviar, ele estaria que estar em algum lugar, escolhi o lugar q salvei no local
                        $envioBox = $box->put_file($RELATIVE_FILE_URL, $nome, $idPastaAtual);

                        if (!isset($envioBox['type']) && isset($envioBox['entries'])) {
                            $IdArquivo = $envioBox['entries'][0]['id'];

                            date_default_timezone_set('America/Recife');

                            Procedures::insertSQL('Arquivos', [
                                'IdArquivo' => $IdArquivo,
                                'Diretorio' => $Diretorio,
                                'Nome' => $nome,
                                'DataCadastro' => date("Y-m-d H:i:s"),
                                'IdUsuario' => $idUsuario
                            ]);
                        } else {
                            if ($envioBox['type'] == 'error' and $envioBox['message'] == 'Item with the same name already exists') {
                                $idArquivo = $envioBox['context_info']['conflicts']['id'];

                                $envioBox = $box->put_file_version($RELATIVE_FILE_URL, $idArquivo, $nome);

                                //em put_file_version nao estava salvando o nome de forma correto, por isso o update file
                                $details['name'] = $nome;
                                $details['description'] = '';
                                $updateNameFileBox = $box->update_file($idArquivo, $details);

                                if ($envioBox && $updateNameFileBox) {
                                    $idArquivo = $envioBox['entries'][0]['id'];

                                    date_default_timezone_set('America/Recife');

                                    Procedures::updateSQL('Arquivos',
                                        [
                                            'Diretorio' => $Diretorio,
                                            'Nome' => $nome,
                                            'DataCadastro' => date("Y-m-d H:i:s")
                                        ],
                                        ['IdArquivo' => $idArquivo]
                                    );
                                }
                            }
                        }
                    }
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Tamanho do arquivo maior que o permitido (50 MB).');
                }

                return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
            }

            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        } else {
            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
                return $this->redirect(['/usuarios/update-dados-box-api']);
            } else {
                return $this->redirect(['/site/login']);
            }
        }
    }

    public function actionUpdate($idArquivo, $idPastaAtual)
    {
        $verificarDados = self::actionVerificarDados();
        if($verificarDados) {
            $idArquivo = base64_decode($idArquivo);
            $idPastaAtual = base64_decode($idPastaAtual);

            $model = new Arquivos();

            if (isset(Yii::$app->request->post()['buttonCreate']) and $_FILES) {
                $idUsuario = Yii::$app->user->identity->IdUsuario;

                if($_FILES['Arquivos']['size']['Diretorio'] <= 5000000) {
                    $deleteFile = self::actionDeleteArquivo(self::findModel($idArquivo));

                    $model->Diretorio = UploadedFile::getInstance($model, 'Diretorio');

                    $nome = $_FILES['Arquivos']['name']['Diretorio'];
                    $Diretorio = Yii::$app->basePath . "/web/Arquivos/$idUsuario/";

                    $envio = self::actionUploadArquivo($model, $Diretorio, $nome);

                    if (isset($envio) && $envio) {
                        $RELATIVE_FILE_URL = $Diretorio . $nome;

                        $box = self::actionConnectionBoxApi();

                        if ($box === false) {
                            return $this->redirect(['/site/about']);
                        }

                        //para enviar, ele estaria que estar em algum lugar, escolhi o lugar q salvei no local
                        $envioBox = $box->put_file_version($RELATIVE_FILE_URL, $idArquivo, $nome);

                        //em put_file_version nao estava salvando o nome de forma correto, por isso o update file
                        $details['name'] = $nome;
                        $details['description'] = '';
                        $updateNameFileBox = $box->update_file($idArquivo, $details);

                        if (isset($envioBox['entries']) && isset($updateNameFileBox['id'])) {
                            $idArquivo = $envioBox['entries'][0]['id'];

                            date_default_timezone_set('America/Recife');

                            Procedures::updateSQL('Arquivos',
                                [
                                    'Diretorio' => $Diretorio,
                                    'Nome' => $nome,
                                    'DataCadastro' => date("Y-m-d H:i:s")
                                ],
                                ['IdArquivo' => $idArquivo]
                            );
                        } else {
                            Yii::$app->getSession()->setFlash('error', 'Não foi possivel atualizar o arquivo. Tente novamente.');
                        }
                    }
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Tamanho do arquivo maior que o permitido (50 MB).');
                }

                return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
            }

            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        } else {
            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
                return $this->redirect(['/usuarios/update-dados-box-api']);
            } else {
                return $this->redirect(['/site/login']);
            }
        }
    }

    /**
     * Deletes an existing Arquivos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($idArquivo, $idPastaAtual)
    {
        $verificarDados = self::actionVerificarDados();
        if($verificarDados) {
            $idArquivo = base64_decode($idArquivo);
            $idPastaAtual = base64_decode($idPastaAtual);

            $box = self::actionConnectionBoxApi();

            if($box === false){
                return $this->redirect(['/site/about']);
            }

            $deleteFileBox = $box->delete_file($idArquivo);

            if($deleteFileBox == Constants::FILE_DELETED){
                $model = self::findModel($idArquivo);

                if(!empty($model)) {
                    $deleteFile = self::actionDeleteArquivo($model);

                    if (isset($deleteFile) && $deleteFile) {
                        Procedures::deleteSQL('Arquivos', ['IdArquivo' => $idArquivo]);
                    }
                }
            } else {
                Yii::$app->getSession()->setFlash('error', 'Não foi possivel excluir o arquivo. Tente novamente.');
            }

            return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
        } else {
            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
                return $this->redirect(['/usuarios/update-dados-box-api']);
            } else {
                return $this->redirect(['/site/login']);
            }
        }
    }

    //salva o arquivo no local
    public function actionUploadArquivo($model, $Diretorio, $nome)
    {
        if (file_exists($Diretorio)) {
            $envio = $model->Diretorio->saveAs($Diretorio . $nome);
        } else {
            $criarDiretorio = mkdir($Diretorio, 0777, true);

            if ($criarDiretorio) {
                $envio = $model->Diretorio->saveAs($Diretorio . $nome);
            }
        }

        return $envio;
    }

    //verifica a existencia do diretorio, nao tendo cria
    public function actionVerificarPasta($Diretorio)
    {
        $criarDiretorio = true;

        if (!file_exists($Diretorio)) {
            $criarDiretorio = mkdir($Diretorio, 0777, true);
        }

        return $criarDiretorio;
    }

    //deleta arquivo no local
    public function actionDeleteArquivo($model)
    {
        if (isset($model->Diretorio, $model->Nome)) {
            $deleteFile = unlink($model->Diretorio . $model->Nome);
        }

        return $deleteFile;
    }

    public function actionDownload($idArquivo, $idPastaAtual)
    {
        $verificarDados = self::actionVerificarDados();
        if($verificarDados) {
            $idArquivo = base64_decode($idArquivo);
            $idPastaAtual = base64_decode($idPastaAtual);

            $box = self::actionConnectionBoxApi();

            if($box === false){
                return $this->redirect(['/site/about']);
            }

            $idUsuario = Yii::$app->user->identity->IdUsuario;
            $Diretorio = Yii::$app->basePath . "/web/Arquivos/$idUsuario/";

            $criarDiretorio = self::actionVerificarPasta($Diretorio);

            if($criarDiretorio) {
                //baixa sempre do box para o diretorio local devido as versoes, pra garantir que é a versão atualizada
                $model = self::findModel($idArquivo);

                if (empty($model) || empty($model->Nome)) {
                    $nameFile = $box->get_file_details($idArquivo)['name'];

                    $downloadFile = $box->download_file($idArquivo, $Diretorio);
                } else {
                    $nameFile = $model->Nome;

                    if (file_exists($Diretorio . $nameFile)) {
                        $deleteFile = unlink($Diretorio . $nameFile);
                    }

                    $downloadFile = $box->download_file($idArquivo, $Diretorio);
                }

                //se tiver feito o download do box para o local, da seguimento
                if ((isset($downloadFile) && $downloadFile) || !isset($downloadFile)) {
                    $arquivo = $Diretorio . $nameFile;

                    if (file_exists($arquivo)) {
                        return Yii::$app->response->sendFile($arquivo, $nameFile);
                    }
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Não foi possivel baixar o arquivo. Tente novamente.');
                }
            }

            return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
        } else {
            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
                return $this->redirect(['/usuarios/update-dados-box-api']);
            } else {
                return $this->redirect(['/site/login']);
            }
        }
    }

    public function actionEditarInfo($idArquivo, $idPastaAtual)
    {
        $verificarDados = self::actionVerificarDados();
        if($verificarDados) {
            $idArquivo = base64_decode($idArquivo);
            $idPastaAtual = base64_decode($idPastaAtual);

            $box = self::actionConnectionBoxApi();

            if($box === false){
                return $this->redirect(['/site/about']);
            }

            $fileDetails = $box->get_file_details($idArquivo);

            if(isset($fileDetails['id']) && isset($fileDetails['name']) && isset($fileDetails['description'])){
                $model = new Arquivos();
                $nomeExtensao = explode('.',$fileDetails['name']);
                $model->Nome = $nomeExtensao[0];
                $extensaoArquivo = '.' . $nomeExtensao[1];
                $model->Descricao = $fileDetails['description'];

                if (isset(Yii::$app->request->post()['buttonEditarInfo'])) {
                    $nome = Yii::$app->request->post()['Arquivos']['Nome'];
                    $descricao = Yii::$app->request->post()['Arquivos']['Descricao'];

                    $details['name'] = $nome . $extensaoArquivo;
                    $details['description'] = $descricao;
                    $updateNameFileBox = $box->update_file($idArquivo, $details);

                    if (isset($updateNameFileBox['type'], $updateNameFileBox['id']) && $updateNameFileBox['type'] == 'file') { //fazer if melhorado, de acordo com o retorno
                        date_default_timezone_set('America/Recife');

                        Procedures::updateSQL('Arquivos',
                            [
                                'Nome' => $nome . $extensaoArquivo,
                                'Nome' => $descricao
                            ],
                            ['IdArquivo' => $idArquivo]
                        );
                    } else {
                        Yii::$app->getSession()->setFlash('error', 'Não foi possivel atualizar as informações do arquivo. Tente novamente.');
                    }

                    return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
                }

                return $this->renderAjax('editar-info', [
                    'model' => $model,
                ]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Não foi possivel carregar as informações do arquivo. Tente novamente.');
            }

            return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
        } else {
            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
                return $this->redirect(['/usuarios/update-dados-box-api']);
            } else {
                return $this->redirect(['/site/login']);
            }
        }
    }

    public function actionViewInfo($idArquivo, $idPastaAtual)
    {
        $verificarDados = self::actionVerificarDados();
        if($verificarDados) {
            $idArquivo = base64_decode($idArquivo);
            $idPastaAtual = base64_decode($idPastaAtual);

            $box = self::actionConnectionBoxApi();

            if($box === false){
                return $this->redirect(['/site/about']);
            }

            $fileDetails = $box->get_file_details($idArquivo);

            if(isset($fileDetails['id']) && isset($fileDetails['name']) && isset($fileDetails['description'])){
                $model = $fileDetails;

                return $this->renderAjax('view-info', [
                    'model' => $model,
                ]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Não foi possivel carregar as informações do arquivo. Tente novamente.');
            }

            return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
        } else {
            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
                return $this->redirect(['/usuarios/update-dados-box-api']);
            } else {
                return $this->redirect(['/site/login']);
            }
        }
    }

    public function actionViewVersions($idArquivo, $idPastaAtual)
    {
        $verificarDados = self::actionVerificarDados();
        if($verificarDados) {
            $idArquivo = base64_decode($idArquivo);
            $idPastaAtual = base64_decode($idPastaAtual);

            $box = self::actionConnectionBoxApi();

            if($box === false){
                return $this->redirect(['/site/about']);
            }

            $versions = $box->get_all_file_version($idArquivo);

            if(isset($versions['total_count'], $versions['entries'])) {
                $total_count = $versions['total_count'];

                $dataProvider = new ArrayDataProvider([
                    'allModels' => $versions['entries'],
                    'pagination' => [
                        'pageSize' => 10,
                    ],
                ]);

                return $this->renderAjax('list-versions', [
                    'total_count' => $total_count,
                    'dataProvider' => $dataProvider,
                    'idArquivo' => $idArquivo,
                    'idPastaAtual' => $idPastaAtual,
                ]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Não foi possivel carregar as informações das versões do arquivo. Tente novamente.');
            }

            return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
        } else {
            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
                return $this->redirect(['/usuarios/update-dados-box-api']);
            } else {
                return $this->redirect(['/site/login']);
            }
        }
    }

    public function actionDeleteVersion($idArquivo, $idArquivoVersion, $idPastaAtual)
    {
        $verificarDados = self::actionVerificarDados();
        if($verificarDados) {
            $idArquivo = base64_decode($idArquivo);
            $idArquivoVersion = base64_decode($idArquivoVersion);
            $idPastaAtual = base64_decode($idPastaAtual);

            $box = self::actionConnectionBoxApi();

            if($box === false){
                return $this->redirect(['/site/about']);
            }

            $deleteFileBox = $box->delete_file_version($idArquivo, $idArquivoVersion);

            if($deleteFileBox != Constants::FILE_DELETED){
                Yii::$app->getSession()->setFlash('error', 'Não foi possivel deletar a versão do arquivo. Tente novamente.');
            }

            return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
        } else {
            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
                return $this->redirect(['/usuarios/update-dados-box-api']);
            } else {
                return $this->redirect(['/site/login']);
            }
        }
    }

    public function actionPromoteVersion($idArquivo, $idArquivoVersion, $idPastaAtual)
    {
        $verificarDados = self::actionVerificarDados();
        if($verificarDados) {
            $idArquivo = base64_decode($idArquivo);
            $idArquivoVersion = base64_decode($idArquivoVersion);
            $idPastaAtual = base64_decode($idPastaAtual);

            $box = self::actionConnectionBoxApi();

            if($box === false){
                return $this->redirect(['/site/about']);
            }

            //deixa o arquivo escolhido como a versao principal
            $restoreFileBox = $box->promote_file_version($idArquivo, $idArquivoVersion);

            if(isset($restoreFileBox['type']) && $restoreFileBox['type'] == 'error'){
                Yii::$app->getSession()->setFlash('error', 'Não foi possivel atualizar a versão do arquivo. Tente novamente.');
            }

            return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
        } else {
            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
                return $this->redirect(['/usuarios/update-dados-box-api']);
            } else {
                return $this->redirect(['/site/login']);
            }
        }
    }

    public function actionCreateFolder($idPastaAtual)
    {
        $verificarDados = self::actionVerificarDados();
        if($verificarDados) {
            $model = new Arquivos();

            if (isset(Yii::$app->request->post()['buttonCreateFolder'])) {
                $nomeFile = Yii::$app->request->post()['Arquivos']['Nome'];
                $idPastaAtual = base64_decode($idPastaAtual);

                $box = self::actionConnectionBoxApi();

                if ($box === false) {
                    return $this->redirect(['/site/about']);
                }

                $createFolder = $box->create_folder($nomeFile, $idPastaAtual);

                if(isset($createFolder['type']) && $createFolder['type'] == 'error' && $createFolder['code'] == 'item_name_in_use'){
                    Yii::$app->getSession()->setFlash('error', 'Não foi possivel criar a pasta. Tente novamente.');
                }

                return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
            }

            return $this->renderAjax('create-folder', [
                'model' => $model,
            ]);
        } else {
            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
                return $this->redirect(['/usuarios/update-dados-box-api']);
            } else {
                return $this->redirect(['/site/login']);
            }
        }
    }

    public function actionUpdateInfoFolder($idPasta, $idPastaAtual)
    {
        $verificarDados = self::actionVerificarDados();
        if($verificarDados) {
            $model = new Arquivos();
            $idPasta = base64_decode($idPasta);
            $idPastaAtual = base64_decode($idPastaAtual);

            $box = self::actionConnectionBoxApi();

            if ($box === false) {
                return $this->redirect(['/site/about']);
            }

            $getFolder = $box->get_folder_details($idPasta);

            if(isset($getFolder['type'], $getFolder['name'], $getFolder['description']) && $getFolder['type'] == 'folder'){
                $model->Nome = $getFolder['name'];
                $model->Descricao = $getFolder['description'];

                if (isset(Yii::$app->request->post()['buttonEditarInfo'])) {
                    $nomeFile = Yii::$app->request->post()['Arquivos']['Nome'];
                    $descricaoFile = Yii::$app->request->post()['Arquivos']['Descricao'];

                    $details['name'] = $nomeFile;
                    $details['description'] = $descricaoFile;
                    $updateFolder = $box->update_folder($idPasta, $details);

                    if(isset($updateFolder['type'], $updateFolder['code']) && $updateFolder['type'] == 'error' && $updateFolder['code'] == 'item_name_in_use'){
                        Yii::$app->getSession()->setFlash('error', 'Não foi possivel atualizar as informações da pasta. Tente novamente.');
                    }

                    return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
                }

                return $this->renderAjax('editar-info', [
                    'model' => $model,
                ]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Não foi possivel carregar as informações da pasta. Tente novamente.');

                return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
            }
        } else {
            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
                return $this->redirect(['/usuarios/update-dados-box-api']);
            } else {
                return $this->redirect(['/site/login']);
            }
        }
    }

    public function actionDeleteFolder($idPasta, $idPastaAtual)
    {
        $verificarDados = self::actionVerificarDados();
        if($verificarDados) {
            $idPasta = base64_decode($idPasta);
            $idPastaAtual = base64_decode($idPastaAtual);

            $box = self::actionConnectionBoxApi();

            if($box === false){
                return $this->redirect(['/site/about']);
            }

            $deleteFolder = $box->delete_folder($idPasta, ['recursive' => true]);

            if($deleteFolder != Constants::FOLDER_DELETED){
                Yii::$app->getSession()->setFlash('error', 'Não foi possivel excluir a pasta. Tente novamente.');
            }

            return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
        } else {
            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
                return $this->redirect(['/usuarios/update-dados-box-api']);
            } else {
                return $this->redirect(['/site/login']);
            }
        }
    }

    public function actionViewInfoFolder($idPasta, $idPastaAtual)
    {
        $verificarDados = self::actionVerificarDados();
        if($verificarDados) {
            $idPasta = base64_decode($idPasta);
            $idPastaAtual = base64_decode($idPastaAtual);

            $box = self::actionConnectionBoxApi();

            if($box === false){
                return $this->redirect(['/site/about']);
            }

            $getFolder = $box->get_folder_details($idPasta);

            if(isset($getFolder['id']) && isset($getFolder['name']) && isset($getFolder['description'])){
                $model = $getFolder;

                return $this->renderAjax('view-info', [
                    'model' => $model,
                ]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Não foi possivel carregar as informações da pasta. Tente novamente.');
            }

            return $this->redirect(['index', 'idPastaAtual' => base64_encode($idPastaAtual)]);
        } else {
            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
                return $this->redirect(['/usuarios/update-dados-box-api']);
            } else {
                return $this->redirect(['/site/login']);
            }
        }
    }

//    public function actionDownloadFolder($idPasta, $nomePasta)
//    {
//        $verificarDados = self::actionVerificarDados();
//        if($verificarDados) {
//            $idPasta = base64_decode($idPasta);
//            $nomePastazip = $nomePasta . '.zip';
//
//            $box = self::actionConnectionBoxApi();
//
//            if($box === false){
//                return $this->redirect(['/site/about']);
//            }
//
//            $zipFolder = $box->zip_folder_download($idPasta, $nomePasta);
//
//            $fileContents = file_put_contents($nomePastazip ,file_get_contents($zipFolder['download_url']));
//
////            print_r('<pre>');
////            print_r($fileContents);exit();
//
//            $Diretorio = Yii::$app->basePath . "/web/Arquivos/ZipFiles/";
//
//            $criarDiretorio = self::actionVerificarPasta($Diretorio);
//
//            $ch     =   curl_init($zipFolder['download_url']);
//            $dir            =   $Diretorio;
//            $fileName       =   $nomePasta;
//            $saveFilePath   =   $dir . $fileName . '.zip';
//            $fp             =   fopen($saveFilePath, 'wb');
//            curl_setopt($ch, CURLOPT_FILE, $fp);
//            curl_setopt($ch, CURLOPT_HEADER, 0);
//            curl_exec($ch);
//            curl_close($ch);
//            fclose($fp);
//
////            $file_name  =   basename($zipFolder['download_url']);
////            //save the file by using base name
////            $fn         =   file_put_contents($file_name,file_get_contents($zipFolder['download_url']));
////            header("Expires: 0");
////            header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
////            header("Cache-Control: no-store, no-cache, must-revalidate");
////            header("Cache-Control: post-check=0, pre-check=0", false);
////            header("Pragma: no-cache");
////            header("Content-type: application/file");
////            header('Content-length: '.filesize($file_name));
////            header('Content-disposition: attachment; filename="'.basename($file_name).'"');
////            readfile($file_name);
//
//            if(isset($restoreFileBox['type'], $restoreFileBox['name'])){
//                //mensagem
//            }
//
//            return $this->redirect(['index']);
//
//        } else {
//            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
//                return $this->redirect(['/usuarios/update-dados-box-api']);
//            } else {
//                return $this->redirect(['/site/login']);
//            }
//        }
//    }

//    public function actionRestoreFile($idArquivoVersion)
//    {
//        $verificarDados = self::actionVerificarDados();
//        if($verificarDados) {
//            $idArquivoVersion = base64_decode($idArquivoVersion);
//
//            $box = self::actionConnectionBoxApi();
//
//            if($box === false){
//                return $this->redirect(['/site/about']);
//            }
//
////            $restoreFileBox = $box->restore_file($idArquivoVersion);
//            $restoreFileBox = $box->get_trashed_file($idArquivoVersion);
//
//            print_r('<pre>');
//            print_r($restoreFileBox);exit();
//
//            if(isset($restoreFileBox['type'], $restoreFileBox['name'])){
//                //mensagem
//            }
//
//            return $this->redirect(['index']);
//
//        } else {
//            if(isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null){
//                return $this->redirect(['/usuarios/update-dados-box-api']);
//            } else {
//                return $this->redirect(['/site/login']);
//            }
//        }
//    }

    //verifica se o usuario ja tem todos os dados pra fazer conexao com o Box.com
    public function actionVerificarDados()
    {
        if( (isset(Yii::$app->user->identity->IdUsuario) && Yii::$app->user->identity->IdUsuario != '' && Yii::$app->user->identity->IdUsuario != null) &&
            (isset(Yii::$app->user->identity->ClientId) && !empty(Yii::$app->user->identity->ClientId)) &&
            (isset(Yii::$app->user->identity->ClientSecret) && !empty(Yii::$app->user->identity->ClientSecret)) &&
            (isset(Yii::$app->user->identity->UserId) && !empty(Yii::$app->user->identity->UserId))
          ) {
            return true;
        } else {
            return false;
        }
    }

    //tenta fazer a conexão com o Box.com
    public function actionConnectionBoxApi()
    {
        $identity = Yii::$app->user->identity;
        $boxApi = new Box_API($identity->ClientId, $identity->ClientSecret, $identity->UserId);
        $boxToken = $boxApi->get_token();

        if(isset($boxToken['error'])){
            Yii::$app->getSession()->setFlash('error', 'Conexão com Box.com não realizada. Verifique suas Credenciais e tente novamente.');
            return false;
        } else {
            return $boxApi;
        }
    }

    /**
     * Finds the Arquivos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Arquivos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($idArquivo)
    {
        if (($model = Arquivos::findOne($idArquivo)) !== null) {
            return $model;
        }

        return null;
    }
}
