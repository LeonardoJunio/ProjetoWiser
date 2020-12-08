<?php

use fedemotta\datatables\DataTables;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ArquivosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Versões do Arquivo';

//pjax para ao mudar de pagina nao atualizar a view
?>

    <style>
        .center {
            width:120px;
            margin:0 auto;
        }
    </style>

    <div class="arquivos-index">
        <?php Pjax::begin(['id' => 'list-version', 'timeout' => false, 'scrollTo' => true, 'enablePushState' => true]); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'table-responsive mt-2'],
            'tableOptions' => ['class' => 'table table-hover', 'id' => 'files-table'],
            'summaryOptions' => ['class' => 'text-right mr-1'],
            'columns' => [
                [
                    'label' => '',
                    'format' => 'html',
                    'value' => function ($model) {
                        $icone = '<i class="glyphicon glyphicon-file"></i>';

                        return $icone;
                    }
                ],
                [
                    'label' => 'Nome',
                    'format' => 'html',
                    'value' => function ($model) {
                        $statusDeletado = '';
                        if(!empty($model['trashed_at']) && !empty($model['purged_at'])){
                            $statusDeletado = ' <span style="color: red; "> (Excluido) </span>';
                        }

                        return $model['name'] . $statusDeletado;
                    }
                ],
                [
                    'label' => 'size',
                    'value' => function ($model) {
                        return number_format($model['size'] / 1024, 2, ',', '.') . ' KB';
                    }
                ],
                [
                    'label' => 'Modificado em',
                    'value' => function ($model) {
                        $date = $model['modified_at'] . ' -3 hours';

                        if(!empty($model['trashed_at']) && !empty($model['purged_at'])){
                            $date = $model['trashed_at'] . ' -3 hours';
                        }

                        return date('d/m/Y H:i', strtotime($date));
                    }
                ],
                [
                    'label' => 'Modificado por',
                    'value' => function ($model) {
                        return $model['modified_by']['name'] . ', ' . $model['modified_by']['login'];
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['class' => 'text-right'],
                    'template' => '{promote}{delete}',
                    'buttons' => [
                        'promote' => function ($url, $model) {
                            if(empty($model['trashed_at']) && empty($model['purged_at'])) {
                                $btn = Html::a("<i class='glyphicon glyphicon-arrow-up'></i>",
                                    $url,
                                    [
                                        'title' => 'Promover a versão do arquivo',
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-primary',
                                    ]
                                );
                                return $btn;
                            }
                        },
                        'delete' => function ($url, $model) {
                            if(empty($model['trashed_at']) && empty($model['purged_at'])) {
                                $btn = Html::a("<i class='glyphicon glyphicon-trash'></i>",
                                    $url,
                                    [
                                        'title' => 'Excluir versão do arquivo',
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-danger',
                                        'data' => [
                                            'confirm' => 'Deseja realmente excluir esta versão do arquivo?',
                                        ],
                                    ]
                                );
                                return $btn;
                            }
                        }
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) use ($idArquivo, $idPastaAtual) {
                        if ($action === 'promote') {
                            $url = 'index.php?r=arquivos/promote-version&idArquivo=' . base64_encode($idArquivo) . '&idArquivoVersion=' . base64_encode($model['id']) . '&idPastaAtual=' . base64_encode($idPastaAtual);
                            return $url;
                        }
                        if ($action === 'delete') {
                            $url = 'index.php?r=arquivos/delete-version&idArquivo=' . base64_encode($idArquivo) . '&idArquivoVersion=' . base64_encode($model['id']) . '&idPastaAtual=' . base64_encode($idPastaAtual);
                            return $url;
                        }
                    },
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>

<?php
$script = <<< JS

JS;
$this->registerJs($script);
?>