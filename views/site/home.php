<?php

/* @var $this yii\web\View */

use app\models\Usuarios;
use yii\helpers\Html;
use fedemotta\datatables\DataTables;


$this->title = '';
?>

<div class="site-index">


    <div class="jumbotron">
        <p style="font-size: xx-large">YII</p>

        <?php if (Yii::$app->user->isGuest) { ?>
            <p style="font-size: medium">Para realizar o Login, clicar na imagem do lado direito superior e em 'Login'.</p>
        <?php } else { ?>
            <p style="font-size: medium">Para realizar o Sign Out, clicar na imagem do lado direito superior e em 'Logout'.</p>
        <?php } ?>

    </div>

</div>
