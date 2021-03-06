<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Growl;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PacoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pacotes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pacote-index">

    <!-- Importação do arquivo responsável por receber e exibir mensagens flash -->
    <?= Yii::$app->view->renderFile('@app/views/layouts/mensagemFlash.php') ?>
    
    <!-- Importação do arquivo responsável por exibir o menu lateral-->
    <?= Yii::$app->view->renderFile('@app/views/layouts/menulateral.php') ?>

   <!-- "page-wrapper" necessário para alinha com o menu lateral. Cobre todo conteudo da view. -->
   <div id="page-wrapper">
       <div id="geral" class="diviconegeral">
            <div id="titulo" style= "float: left;">
                <h1><?= $this->title ?></h1>
            </div>
            <a href=<?= Url::to(['evento/view', 'id' => $evento->idevento])?>>
                <div class="divicone divicone-l1">
                    <?= Html::img('@web/img/voltar.png', ['class' => 'imgicone'])?>
                    <p class="labelicone">Voltar</p>
                </div>
            </a>
            <?php if(!Yii::$app->user->isGuest && $evento->canAccess()){ ?>
            <a href=<?= Url::to(['pacote/create', 'idevento' => $evento->idevento])?>>
                <div class="divicone divicone-l1">
                    <?= Html::img('@web/img/novopacote.png', ['class' => 'imgicone'])?>
                    <p>Novo Pacote</p>
                </div>
            </a>
            <?php } ?>
            <div class="clear"></div>
        </div>
    <h2><?= $evento['descricao']?></h2>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'titulo',
            'descricao',
            'valormoeda',

            !Yii::$app->user->isGuest && $evento->canAccess() ? ['class' => 'yii\grid\ActionColumn'] : 
                ['class' => 'yii\grid\ActionColumn', 'header'=>'Action', 'headerOptions' => ['width' => '80'], 'template' => '{view}'],
        ],
    ]); ?>
    </div>
</div>
