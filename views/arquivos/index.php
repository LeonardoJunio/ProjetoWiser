<?php

use fedemotta\datatables\DataTables;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ArquivosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Arquivos';
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
Modal::begin(['id' => 'modalUploadArquivo', 'options' => ['tabindex' => false, 'style' => 'z-index: 99999'], 'size' => 'modal-sm', 'header' => '<h4>Adicionar arquivo<h4>']);
echo "<div id = 'modalUploadArquivoContent'></div>";
Modal::end();
?>

<?php
Modal::begin(['id' => 'modalAtualizarArquivo', 'options' => ['tabindex' => false, 'style' => 'z-index: 99999'], 'size' => 'modal-sm', 'header' => '<h4>Nova versão do arquivo<h4>']);
echo "<div id = 'modalAtualizarArquivoContent'></div>";
Modal::end();
?>

<?php
Modal::begin(['id' => 'modalEditarInfoArquivo', 'options' => ['tabindex' => false, 'style' => 'z-index: 99999'], 'size' => 'modal-sm', 'header' => '<h4>Editar dados do arquivo<h4>']);
echo "<div id = 'modalEditarInfoArquivoContent'></div>";
Modal::end();
?>

<?php
Modal::begin(['id' => 'modalViewInfoArquivo', 'options' => ['tabindex' => false, 'style' => 'z-index: 99999'], 'size' => 'modal-lg', 'header' => '<h4>Informações do arquivo<h4>']);
echo "<div id = 'modalViewInfoArquivoContent'></div>";
Modal::end();
?>

<?php
Modal::begin(['id' => 'modalViewVersionsArquivo', 'options' => ['tabindex' => false, 'style' => 'z-index: 99999'], 'size' => 'modal-lg', 'header' => '<h4>Versões do arquivo<h4>']);
echo "<div id = 'modalViewVersionsArquivoContent'></div>";
Modal::end();
?>

<?php
Modal::begin(['id' => 'modalNovaPasta', 'options' => ['tabindex' => false, 'style' => 'z-index: 99999'], 'size' => 'modal-sm', 'header' => '<h4>Nova pasta<h4>']);
echo "<div id = 'modalNovaPastaContent'></div>";
Modal::end();
?>

<?php
Modal::begin(['id' => 'modalEditarInfoFolder', 'options' => ['tabindex' => false, 'style' => 'z-index: 99999'], 'size' => 'modal-sm', 'header' => '<h4>Editar dados da pasta<h4>']);
echo "<div id = 'modalEditarInfoFolderContent'></div>";
Modal::end();
?>

<?php
Modal::begin(['id' => 'modalViewInfoFolder', 'options' => ['tabindex' => false, 'style' => 'z-index: 99999'], 'size' => 'modal-lg', 'header' => '<h4>Informações da pasta<h4>']);
echo "<div id = 'modalViewInfoFolderContent'></div>";
Modal::end();
?>

<style>
    .center {
        width:120px;
        margin:0 auto;
    }
</style>

<div class="arquivos-index">
    <div style="margin: 0 auto; width: 656px; text-align: center;">
        <span><?= Html::a('Adicionar Arquivo', ['/arquivos/create', 'idPastaAtual' => base64_encode($idPastaAtual)], ['class' => 'btn btn-primary uploadArquivo']) ?></span>
        <span><?= Html::a('Nova Pasta', ['/arquivos/create-folder', 'idPastaAtual' => base64_encode($idPastaAtual)], ['class' => 'btn btn-primary createFolder']) ?></span>
        <?php if($idPastaAtual != '0' && $idPastaAnterior != '') { ?>
            <span><?= Html::a('Pasta Anterior', ['/arquivos/index', 'idPastaAtual' => base64_encode($idPastaAnterior)], ['class' => 'btn btn-primary']) ?></span>
        <?php } ?>
    </div>
    <br>

    <?php Pjax::begin(); ?>
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
                    if($model['type'] == 'folder'){
                        $icone = '<i class="glyphicon glyphicon-folder-close"></i>';
                    } else if ($model['type'] == 'file'){
                        $icone = '<i class="glyphicon glyphicon-file"></i>';
                    }
                    return $icone;
                }
            ],
            [
                'label' => 'Nome',
                'value' => function ($model) {
                    return $model['name'];
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['class' => 'text-right'],
                'template' => '{openFolder}{viewFolder}{updateFolder}{deleteFolder}{versions}{view}{edit}{download}{update}{delete}',
                'buttons' => [
                    'openFolder' => function ($url, $model) {
                        if($model['type'] == 'folder') {
                            $btn = Html::a("<i class='glyphicon glyphicon-folder-open'></i>",
                                $url,
                                [
                                    'title' => 'Abrir pasta',
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-secondary',
                                ]
                            );
                            return $btn;
                        }
                    },
                    'viewFolder' => function ($url, $model) {
                        if($model['type'] == 'folder') {
                            $btn = Html::a("<i class='glyphicon glyphicon-eye-open'></i>",
                                $url,
                                [
                                    'title' => 'Visualizar dados da pasta',
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-success viewInfoFolder',
                                ]
                            );
                            return $btn;
                        }
                    },
                    'updateFolder' => function ($url, $model) {
                        if($model['type'] == 'folder') {
                            $btn = Html::a("<i class='glyphicon glyphicon-edit'></i>",
                                $url,
                                [
                                    'title' => 'Editar dados da Pasta',
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-warning editarInfoFolder',
                                ]
                            );
                            return $btn;
                        }
                    },
                    'deleteFolder' => function ($url, $model) {
                        if($model['type'] == 'folder') {
                            $btn = Html::a("<i class='glyphicon glyphicon-trash'></i>",
                                $url,
                                [
                                    'title' => 'Excluir pasta',
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => 'Deseja realmente excluir esta pasta e seus arquivos?',
                                    ],
                                ]
                            );
                            return $btn;
                        }
                    },
                    'versions' => function ($url, $model) {
                        if($model['type'] != 'folder') {
                            $btn = Html::a("<i class='glyphicon glyphicon-list'></i>",
                                $url,
                                [
                                    'title' => 'Visualizar versões do arquivo',
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-secondary viewVersions',
                                ]
                            );
                            return $btn;
                        }
                    },
                    'view' => function ($url, $model) {
                        if($model['type'] != 'folder') {
                            $btn = Html::a("<i class='glyphicon glyphicon-eye-open'></i>",
                                $url,
                                [
                                    'title' => 'Visualizar dados do arquivo',
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-success viewInfo',
                                ]
                            );
                            return $btn;
                        }
                    },
                    'edit' => function ($url, $model) {
                        if($model['type'] != 'folder') {
                            $btn = Html::a("<i class='glyphicon glyphicon-edit'></i>",
                                $url,
                                [
                                    'title' => 'Editar dados do arquivo',
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-warning editarInfo',
                                ]
                            );
                            return $btn;
                        }
                    },
                    'download' => function ($url, $model) {
                        if($model['type'] != 'folder') {
                            $btn = Html::a("<i class='glyphicon glyphicon-download-alt'></i>",
                                $url,
                                [
                                    'title' => 'Download do arquivo',
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-primary',
                                ]
                            );
                            return $btn;
                        }
                    },
                    'update' => function ($url, $model) {
                        if($model['type'] != 'folder') {
                            $btn = Html::a("<i class='glyphicon glyphicon-upload'></i>",
                                $url,
                                [
                                    'title' => 'Nova versão do arquivo',
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-info novaVersao',
                                ]
                            );
                            return $btn;
                        }
                    },
                    'delete' => function ($url, $model) {
                        if($model['type'] != 'folder') {
                            $btn = Html::a("<i class='glyphicon glyphicon-trash'></i>",
                                $url,
                                [
                                    'title' => 'Excluir arquivo',
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => 'Deseja realmente excluir este arquivo?',
                                    ],
                                ]
                            );
                            return $btn;
                        }
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) use ($idPastaAtual) {
                    if ($action === 'openFolder') {
                        $url = 'index.php?r=arquivos/index&idPastaAtual=' . base64_encode($model['id']);
                        return $url;
                    }
                    if ($action === 'viewFolder') {
                        $url = 'index.php?r=arquivos/view-info-folder&idPasta=' . base64_encode($model['id']) . '&idPastaAtual=' . base64_encode($idPastaAtual);
                        return $url;
                    }
                    if ($action === 'deleteFolder') {
                        $url = 'index.php?r=arquivos/delete-folder&idPasta=' . base64_encode($model['id']) . '&idPastaAtual=' . base64_encode($idPastaAtual);
                        return $url;
                    }
                    if ($action === 'updateFolder') {
                        $url = 'index.php?r=arquivos/update-info-folder&idPasta=' . base64_encode($model['id']) . '&idPastaAtual=' . base64_encode($idPastaAtual);
                        return $url;
                    }
                    if ($action === 'versions') {
                        $url = 'index.php?r=arquivos/view-versions&idArquivo=' . base64_encode($model['id']) . '&idPastaAtual=' . base64_encode($idPastaAtual);
                        return $url;
                    }
                    if ($action === 'view') {
                        $url = 'index.php?r=arquivos/view-info&idArquivo=' . base64_encode($model['id']) . '&idPastaAtual=' . base64_encode($idPastaAtual);
                        return $url;
                    }
                    if ($action === 'edit') {
                        $url = 'index.php?r=arquivos/editar-info&idArquivo=' . base64_encode($model['id']) . '&idPastaAtual=' . base64_encode($idPastaAtual);
                        return $url;
                    }
                    if ($action === 'download') {
                        $url = 'index.php?r=arquivos/download&idArquivo=' . base64_encode($model['id']) . '&idPastaAtual=' . base64_encode($idPastaAtual);
                        return $url;
                    }
                    if ($action === 'update') {
                        $url = 'index.php?r=arquivos/update&idArquivo=' . base64_encode($model['id']) . '&idPastaAtual=' . base64_encode($idPastaAtual);
                        return $url;
                    }
                    if ($action === 'delete') {
                        $url = 'index.php?r=arquivos/delete&idArquivo=' . base64_encode($model['id']) . '&idPastaAtual=' . base64_encode($idPastaAtual);
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

$('.uploadArquivo').on('click', function (e) {
	e.preventDefault();

	$.get($(this).attr('href'), function(data) {
		$('#modalUploadArquivo').find('#modalUploadArquivoContent').html(data);
		$('#modalUploadArquivo').modal('show');
	});
});

$('.novaVersao').on('click', function (e) {
	e.preventDefault();

	$.get($(this).attr('href'), function(data) {
		$('#modalAtualizarArquivo').find('#modalAtualizarArquivoContent').html(data);
		$('#modalAtualizarArquivo').modal('show');
	});
});

$('.editarInfo').on('click', function (e) {
	e.preventDefault();

	$.get($(this).attr('href'), function(data) {
		$('#modalEditarInfoArquivo').find('#modalEditarInfoArquivoContent').html(data);
		$('#modalEditarInfoArquivo').modal('show');
	});
});

$('.viewInfo').on('click', function (e) {
	e.preventDefault();

	$.get($(this).attr('href'), function(data) {
		$('#modalViewInfoArquivo').find('#modalViewInfoArquivoContent').html(data);
		$('#modalViewInfoArquivo').modal('show');
	});
});

$('.viewVersions').on('click', function (e) {
	e.preventDefault();

	$.get($(this).attr('href'), function(data) {
		$('#modalViewVersionsArquivo').find('#modalViewVersionsArquivoContent').html(data);
		$('#modalViewVersionsArquivo').modal('show');
	});
});

$('.createFolder').on('click', function (e) {
	e.preventDefault();
	
	$.get($(this).attr('href'), function(data) {
		$('#modalNovaPasta').find('#modalNovaPastaContent').html(data);
		$('#modalNovaPasta').modal('show');
	});
});

$('.editarInfoFolder').on('click', function (e) {
	e.preventDefault();

	$.get($(this).attr('href'), function(data) {
		$('#modalEditarInfoFolder').find('#modalEditarInfoFolderContent').html(data);
		$('#modalEditarInfoFolder').modal('show');
	});
});

$('.viewInfoFolder').on('click', function (e) {
	e.preventDefault();

	$.get($(this).attr('href'), function(data) {
		$('#modalViewInfoFolder').find('#modalViewInfoFolderContent').html(data);
		$('#modalViewInfoFolder').modal('show');
	});
});

JS;
$this->registerJs($script);
?>