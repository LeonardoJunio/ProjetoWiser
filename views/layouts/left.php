<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <?php if (!Yii::$app->user->isGuest) { ?>
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?= $directoryAsset ?>/img/user8-128x128.jpg" class="user-image" alt="User Image"/>
                </div>
                <div class="pull-left info">
                    <p><?= $nome ?></p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
        <?php } ?>

        <?php if (!Yii::$app->user->isGuest) { ?>
            <?=
                dmstr\widgets\Menu::widget([
                    'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                    'items' => [
                        ['label' => 'Menu', 'options' => ['class' => 'header']],
                        ['label' => 'Usuário', 'url' => ['usuarios/view']],
                        ['label' => 'Arquivos', 'url' =>['arquivos/index', 'idPastaAtual' => base64_encode('0')]],
                        ['label' => 'About', 'url' => ['site/about']],
                    ],
                ])
            ?>
        <?php } else { ?>
            <?=
                dmstr\widgets\Menu::widget([
                    'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                    'items' => [
                        ['label' => 'Menu', 'options' => ['class' => 'header']],
                        ['label' => 'About', 'url' => ['site/about']],
                        ['label' => 'Login', 'url' => ['site/login']],
                        ['label' => 'Cadastrar-se', 'url' => ['site/create']],
                    ],
                ])
            ?>
        <?php } ?>

    </section>

</aside>
