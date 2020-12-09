<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Alterar Senha';
?>

<div class="senha-update">
    <div class="senha-update-body">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Nova senha', 'required' => true])->label(false) ?>

        <?= $form->field($model, 'passwordConfirm')->passwordInput(['placeholder' => 'Confirmar nova senha', 'required' => true])->label(false) ?>

        <div class="text-right">
            <?= Html::submitButton('Salvar Senha', ['class'=>'btn btn-success', 'name' => 'buttonAlterarSenha', 'id' => 'buttonAlterarSenha']); ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
