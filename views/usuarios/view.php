<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Usuário';

YiiAsset::register($this);

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

<?php
Modal::begin(['id' => 'modalAlterarSenha', 'options' => ['tabindex' => false, 'style' => 'z-index: 99999'], 'size' => 'modal-sm', 'header' => '<h4>Alterar Senha<h4>']);
echo "<div id = 'modalAlterarSenhaContent'></div>";
Modal::end();
?>

<?php
Modal::begin(['id' => 'modalAlterarCredenciais', 'options' => ['tabindex' => false, 'style' => 'z-index: 99999'], 'size' => 'modal-md', 'header' => '<h4>Alterar Credenciais<h4>']);
echo "<div id = 'modalAlterarCredenciaisContent'></div>";
Modal::end();
?>

<style>
    .detail1-view {
        width: 90%;
        margin: 0 auto;
    }
</style>

<div class="detail1-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Usuário',
                'value' => $model->Usuario,
            ],
            [
                'label' => 'Client Id',
                'value' => empty($model->ClientId) ? 'Não cadastrado' : $model->ClientId,
            ],
            [
                'label' => 'Client Secret',
                'value' => empty($model->ClientSecret) ? 'Não cadastrado' : $model->ClientSecret,
            ],
            [
                'label' => 'User Id',
                'value' => empty($model->UserId) ? 'Não cadastrado' : $model->UserId,
            ],
            [
                'label' => 'Data de Cadastro',
                'value' => date('d/m/Y H:i', strtotime($model->DataCadastro)),
            ],
            [
                'label' => 'Status',
                'value' => ($model->Status) ? 'Ativo' : 'Inativo',
            ],
        ],
    ]) ?>

    <div class="text-right">
        <span><?= Html::a('Alterar senha', ['/usuarios/alterar-senha'], ['class' => 'btn btn-primary alterarSenha']) ?></span>
        <span><?= Html::a('Alterar credenciais', ['/usuarios/alterar-credenciais'], ['class' => 'btn btn-primary alterarCredenciais']) ?></span>
    </div>
</div>

<?php
$script = <<< JS

$('.alterarSenha').on('click', function (e) {
	e.preventDefault();

	$.get($(this).attr('href'), function(data) {
		$('#modalAlterarSenha').find('#modalAlterarSenhaContent').html(data);
		$('#modalAlterarSenha').modal('show');
	});
});

$('.alterarCredenciais').on('click', function (e) {
	e.preventDefault();

	$.get($(this).attr('href'), function(data) {
		$('#modalAlterarCredenciais').find('#modalAlterarCredenciaisContent').html(data);
		$('#modalAlterarCredenciais').modal('show');
	});
});

JS;
$this->registerJs($script);
?>