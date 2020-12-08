<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Arquivos */

$this->title = 'Adicionar Arquivos';
?>

<div class="arquivos-create">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'Diretorio')->fileInput()->label('Selecione um arquivo (até 50 MB):') ?>

    <div class="text-right">
        <?= Html::submitButton('Upload', ['class'=>'btn btn-primary', 'name' => 'buttonCreate', 'id' => 'buttonCreate']); ?>
    </div>

    <?php ActiveForm::end() ?>
</div>
