<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Local */

$this->title = 'Novo Local';
$this->params['breadcrumbs'][] = ['label' => 'Locais', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="local-create">

	<!-- Importação do arquivo responsável por receber e exibir mensagens flash -->
    <?= Yii::$app->view->renderFile('@app/views/layouts/mensagemFlash.php') ?>
    
    <!-- Importação do arquivo responsável por exibir o menu lateral-->
    <?= Yii::$app->view->renderFile('@app/views/layouts/menulateral.php') ?>

   <!-- "page-wrapper" necessário para alinha com o menu lateral. Cobre todo conteudo da view. -->
   <div id="page-wrapper">
    <h1><?= Html::encode($this->title) ?></h1>
    <p align="right">Campos marcados com * são obrigatórios</p><div></div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
</div>