<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Arquivos */

$this->title = 'Editar Dados';
?>

<div class="arquivos-create">
    <?php $form = ActiveForm::begin() ?>

    <?= $form->field($model, 'Nome')->textInput(['placeholder' => 'Nome'])->label(false) ?>
    <?= $form->field($model, 'Descricao')->textInput(['placeholder' => 'Descrição'])->label(false) ?>

    <div class="text-right">
        <?= Html::submitButton('Salvar', ['class'=>'btn btn-primary', 'name' => 'buttonEditarInfo', 'id' => 'buttonEditarInfo']); ?>
    </div>

    <?php ActiveForm::end() ?>
</div>
