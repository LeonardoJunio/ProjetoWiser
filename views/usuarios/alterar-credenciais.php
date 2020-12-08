<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Credenciais do Usuário';
?>

<div class="credenciais-update">
    <div class="credenciais-update-body">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ClientId')->textInput(['placeholder' => 'Client ID'])->label(false) ?>

        <?= $form->field($model, 'ClientSecret')->passwordInput(['placeholder' => 'Client Secret'])->label(false) ?>

        <?= $form->field($model, 'UserId')->textInput(['placeholder' => 'User ID'])->label(false) ?>

        <div class="text-right">
            <?= Html::submitButton('Salvar Credenciais', ['class'=>'btn btn-success', 'name' => 'buttonAlterarCredenciais', 'id' => 'buttonAlterarCredenciais']); ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
