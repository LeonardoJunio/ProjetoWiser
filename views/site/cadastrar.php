<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Cadastro';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback', 'required' => true],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
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

<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>Cadastre-se</b></a>
    </div>

    <div class="login-box-body">
        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>
        <?= $form
            ->field($usuario, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => 'Usuário']) ?>

        <?= $form
            ->field($usuario, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => 'Senha']) ?>

        <?= $form
            ->field($usuario, 'passwordConfirm', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => 'Confirmar Senha']) ?>

        <div class="row">
            <div class="col-xs-6">
                <a href=<?php echo yii::$app->homeUrl; ?> class="btn btn-primary">Voltar</a>
            </div>
            <div class="col-xs-6">
                <?= Html::submitButton('Cadastrar', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'buttonCreate', 'id' => 'buttonCreate']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
