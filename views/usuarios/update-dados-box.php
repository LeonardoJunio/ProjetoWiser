<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Credenciais do Usuário';
?>

<div class="login-box">
    <div class="login-logo">

        <p style="font-size: x-large; font-weight: bold"> Cadastre suas credenciais para continuar:</p>
    </div>

    <div class="login-box-body">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ClientId')->textInput(['placeholder' => 'Client ID'])->label(false) ?>

        <?= $form->field($model, 'ClientSecret')->passwordInput(['placeholder' => 'Client Secret'])->label(false) ?>

        <?= $form->field($model, 'UserId')->textInput(['placeholder' => 'User ID'])->label(false) ?>

        <div class="row">
            <div class="col-xs-6">
                <a href=<?php echo yii::$app->homeUrl; ?> class="btn btn-primary">Voltar</a>
            </div>
            <div class="col-xs-6">
                <?= Html::submitButton('Salvar Credenciais', ['class' => 'btn btn-success btn-block btn-flat', 'name' => 'buttonCredenciaisBox', 'id' => 'buttonCredenciaisBox']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
