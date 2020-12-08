<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Arquivo';

YiiAsset::register($this);

?>

    <style>
        .detail1-view {
            width: 100%;
            margin: 0 auto;
        }
    </style>

    <div class="detail1-view">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'label' => 'Nome',
                    'value' => $model['name'],
                ],
                [
                    'label' => 'Descrição',
                    'value' => empty($model['description']) ? 'Não informado' : $model['description'],
                ],
                [
                    'label' => 'Tamanho',
                    'value' => number_format($model['size'] / 1024, 2, ',', '.') . ' KB',
                ],
                [
                    'label' => 'Criador por',
                    'value' => $model['created_by']['name'] . ', ' . $model['created_by']['login'],
                ],
                [
                    'label' => 'Data de Criação',
                    'value' => date('d/m/Y H:i', strtotime($model['created_at'] . '-3 hours')),
                ],
                [
                    'label' => 'Ultima Modificação por',
                    'value' => $model['modified_by']['name'] . ', ' . $model['modified_by']['login'],
                ],
                [
                    'label' => 'Data da Última Modificação',
                    'value' => date('d/m/Y H:i', strtotime($model['modified_at'] . '-3 hours')),
                ],
            ],
        ]) ?>
    </div>

<?php
$script = <<< JS

JS;
$this->registerJs($script);
?>