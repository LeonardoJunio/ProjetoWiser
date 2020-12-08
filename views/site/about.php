<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Desafio - Wiser';
?>

<?php if (yii::$app->session->hasFlash('error')){ ?>
    <div class="alert alert-dismissible alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?=
            yii::$app->session->getFlash('error');
            yii::$app->session->removeAllFlashes();
        ?>
    </div>
<?php } ?>

<?php if (yii::$app->session->hasFlash('success')){ ?>
    <div class="alert alert-dismissible alert-success">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?=
            yii::$app->session->getFlash('success');
            yii::$app->session->removeAllFlashes();
        ?>
    </div>
<?php } ?>

<style>
    .jumbotron {
        padding-top: 20px;
        padding-bottom: 48px;
    }
</style>

<div class="site-about">
    <div class="jumbotron">
        <h1>Desafio - Wiser</h1>
        <br>
        <p style="font-size: medium"> O aplicativo possui interação com o serviço de storage da Box.com utilizando credenciais do modo
            Server Authentication (Client Credentials Grant), contendo as seguintes funçoes: </p>
        <ul style="padding-right: 40px">
            <li>Sistema de Login e Logout</li>
            <li>Cadastro de novos usuários</li>
            <li>Possibilidade de salvar/alterar as credenciais de acesso ao Box.com no sistema</li>
            <li>Visualizar informações armazenadas do usuário e alterar sua senha</li>
            <li>Listagem de arquivos e pastas</li>
            <li>Fazer upload e download de arquivos</li>
            <li>Excluir pastas e arquivos</li>
            <li>Editar dados das pastas e arquivos</li>
            <li>Inserir novas versões dos arquivos</li>
            <li>Verificar informaçoes mais detalhadas dos arquivos e das pastas</li>
            <li>Verificar as versões dos arquivos, podendo excluir a versão ou torna-la a versão primaria/oficial</li>
            <li>Criar novas pastas</li>
            <li>Navergar entre as pastas e seus arquivos</li>
        </ul>
        <br>
        <p style="font-size: medium">Ferramentas utilizadas: PHP Version 7.2.19, Framework  Yii2, SqLite3, PHP built-in web server / Laragon Full 4.0.16 + Apache 2.4.35, AdminLte2.</p>
        <br>
        <?php if (Yii::$app->user->isGuest) { ?>
            <p style="font-size: medium">Para realizar o Login, clicar na imagem do lado direito superior e em 'Login' ou no menu a esquerda.</p>
        <?php } else { ?>
            <p style="font-size: medium">Para realizar o Logout, clicar na imagem do lado direito superior e em 'Logout'.</p>
        <?php } ?>
    </div>
</div>
