<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Arquivos */

$this->title = 'Nova Pasta';
?>

<div class="arquivos-create">
    <?php $form = ActiveForm::begin() ?>

    <?= $form->field($model, 'Nome')->textInput(['placeholder' => 'Nome da Pasta'])->label(false) ?>

    <div class="text-right">
        <?= Html::submitButton('Salvar', ['class'=>'btn btn-primary', 'name' => 'buttonCreateFolder', 'id' => 'buttonCreateFolder']); ?>
    </div>

    <?php ActiveForm::end() ?>
</div>
