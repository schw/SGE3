<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\SideNav;
use kartik\widgets\Growl;
use yii\helpers\Url;


$this->title = 'Eventos - Inscrições Abertas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="evento-index">

    <!-- Importação do arquivo responsável por receber e exibir mensagens flash -->
    <?= Yii::$app->view->renderFile('@app/views/layouts/mensagemFlash.php') ?>
    
    <!-- Importação do arquivo responsável por exibir o menu lateral-->
    <?= Yii::$app->view->renderFile('@app/views/layouts/menulateral.php') ?>

   <!-- "page-wrapper" necessário para alinha com o menu lateral. Cobre todo conteudo da view. -->
   <div id="page-wrapper">
    
    <div id="geral" style="width: 100%; text-align: center; border: solid; background-color: gray;">
        <div id="titulo" style= "float: left;">
            <label><strong><h1><?= Html::encode($this->title) ?></h1></strong></label>
        </div>
        <a href=<?= Url::to(['evento/create'])?>>
            <div class="divicone divicone-l1" style="padding: 10px;">
                <?= Html::img('@web/img/novoevento.png', ['class' => 'imgicone']) ?>
                <p>Novo Evento</p>
            </div>
        </a>
        <div class="clear"></div>
    </div>
    <p></p>

        <?= GridView::widget([
            'showOnEmpty' => 'true',
            'dataProvider' => $dataProvider,
            //'summary' => false,
            'columns' => [
                'sigla',
                'descricao',

                ['attribute' => 'Tipo', 'value' => 'tipo.titulo'],
                ['attribute' => 'Vagas', 'value' => 'vagas','headerOptions' => ['width' => '100']],
                ['attribute' => 'Total de Inscritos', 'value' => 'qtd_evento','headerOptions' => ['width' => '170']],

                ['class' => 'yii\grid\ActionColumn', 'header'=>'Action', 'headerOptions' => ['width' => '80'], 'template' => '{view} {update} {delete}{link}'],
            ],
        ]); ?>

        <h2><?= Html::encode("Compartilhado") ?></h2>

        <?= GridView::widget([
            'showOnEmpty' => 'true',
            'dataProvider' => $dataProvider2,
            'columns' => [
                'evento.sigla',
                'evento.descricao',
                ['attribute' => 'Responsável', 'value' => 'evento.responsavel0.nome'],
                
                ['attribute' => 'Tipo', 'value' => 'evento.tipo.titulo'],
                ['class' => 'yii\grid\ActionColumn', 'header'=>'Action', 'headerOptions' => ['width' => '80'], 'template' => '{view}{link}',
                'buttons' => [
                    'view' => function ($url,$model,$key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model->evento_idevento]);
                            },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
