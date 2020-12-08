<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">App</span><span class="logo-lg">Desafio - Wiser</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php if (!Yii::$app->user->isGuest) { ?>
                            <img src="<?= $directoryAsset ?>/img/user8-128x128.jpg" class="user-image" alt="User Image"/>
                        <?php } else { ?>
                            <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="user-image" alt="User Image"/>
                        <?php } ?>
                        <span class="hidden-xs"> <?= $nome ?> </span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <?php if (!Yii::$app->user->isGuest) { ?>
                                <img src="<?= $directoryAsset ?>/img/user8-128x128.jpg" class="user-image" alt="User Image"/>
                            <?php } else { ?>
                                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="user-image" alt="User Image"/>
                            <?php } ?>

                            <p>
                                <?= $nome ?>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <?php if (Yii::$app->user->isGuest) { ?>
                                <div class="pull-left">
                                    <?= Html::a(
                                        'Cadastrar-se',
                                        ['/site/create'],
                                        ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                    ) ?>
                                </div>
                                <div class="pull-right">
                                    <?= Html::a(
                                        'Login',
                                        ['/site/login'],
                                        ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                    ) ?>
                                </div>
                            <?php } else { ?>
                                <div align="center">
                                    <?= Html::a(
                                        'Logout',
                                        ['/site/logout'],
                                        ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                    ) ?>
                                </div>
                            <?php } ?>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
