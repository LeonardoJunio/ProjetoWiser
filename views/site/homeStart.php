<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = '';
?>

<style>
    .centerLogin {
        width:15px;
        margin:0 auto;
    }
    .centerCadastrar {
        width:60px;
        margin:0 auto;
    }
</style>

<div class="site-index">

    <div class="jumbotron">
        <h1>Desafio - Wiser</h1>

        <p class="lead">Por favor, inicie sua sessão realizando o Login, caso nao tenha ainda, realize o seu cadastro.</p>
    </div>

    <div class="body-content">
        <div class="centerLogin">
            <span><?= Html::a('Login', ['/site/login'], ['data-method' => 'post', 'class' => 'btn btn-primary']) ?></span>
        </div>
            <br>
        <div class="centerCadastrar">
            <span><?= Html::a('Cadastrar-se', ['/site/create'], ['data-method' => 'post', 'class' => 'btn btn-primary']) ?></span>
        </div>
    </div>
</div>
