<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Gerar Certificados';
$id_evento = $evento['idevento'];
?>

<script>
function myFunctionCredenciado(tipousuario) {
    var keys = $('#gridview_id_credenciados').yiiGridView('getSelectedRows');
    var ids = [];
    
    var id_evento;

    if (Object.keys(keys).length > 0){

        id_evento = keys[0].evento_idevento;

        for (var i=0 ; i<Object.keys(keys).length ; i++){

                ids[i] = keys[i].usuario_idusuario;
       
        }

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && xhttp.status == 200) {

              window.open("index.php?r=certificados/pdfcredenciados&tipousuario="+tipousuario+"&evento_idevento="+id_evento+"&ids="+xhttp.responseText);
              
           }
        };
      
      xhttp.open("GET", "index.php?r=certificados/idsusuarios&ids="+ids, true);
      xhttp.send();
    }
    else{
        alert("Selecione pelo menos um Participante.");
    }

}
</script>

<div id="myformcontainer"></div>
<div class="inscritos-index">

    <!-- Importação do arquivo responsável por receber e exibir mensagens flash -->
    <?= Yii::$app->view->renderFile('@app/views/layouts/mensagemFlash.php') ?>
    
    <!-- Importação do arquivo responsável por exibir o menu lateral-->
    <?= Yii::$app->view->renderFile('@app/views/layouts/menulateral.php') ?>

   <!-- "page-wrapper" necessário para alinha com o menu lateral. Cobre todo conteudo da view. -->
   <div id="page-wrapper">
        <div id="geral" class="diviconegeral">
            <div id="titulo" style= "float: left;">
                <h1>Gerar Certificados</h1>
            </div>
            <a href=<?= Url::to(['evento/view', 'id' => $id_evento])?>>
                <div class="divicone divicone-l1">
                    <?= Html::img('@web/img/voltar.png', ['class' => 'imgicone'])?>
                    <p class="labelicone">Voltar</p>
                </div>
            </a>
            <div class="clear"></div>
        </div>
        <h2><?= $evento['descricao'] ?></h2>




    <?= GridView::widget([
        'showOnEmpty' => 'true',
        'dataProvider' => $dataProvider,
        'summary' => '',
        'options' => ['id' => 'gridview_id_credenciados'],
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn','headerOptions' => ['width' => '35'] ],
            ['attribute' => 'usuario.nome', 'value' => 'usuario.nome'],
            //'itemProgramacao.palestrante',
/*            ['class' => 'yii\grid\ActionColumn', 'header'=>'Ação', 'headerOptions' => ['width' => '100'], 
            'template' => '{imprimir} {link}','buttons' => [
                'imprimir' => function ($url,$model,$key) {

                		$id_evento = Yii::$app->request->post('evento_idevento');

                        return Html::a('<span class="glyphicon glyphicon-print"></span>', ['inscreve/pdf'], ['target' => 'blank',
                        'data'=>[
                        'method' => 'POST',
                        'params'=>['evento_idevento' => $id_evento, 'usuario_certificado' => $model->usuario->nome, 'tipousuario_certificado' => 0],
                            ]]);
                },
        ],
],*/
     ],
 ]); ?>

<?php 
        $model = $dataProvider->getModels();
        $count = $dataProvider->getCount();

        $i = 0;
        while($i<$count){
            $nome[$i] = $model[$i]->usuario->nome;
            $i++;
        }

        if ($count > 0){

            echo Html::submitButton('Gerar Certificado', ['onclick' => 'myFunctionCredenciado(0)' ,
                'class' => 'btn btn-success']); 
       }

 ?>

 </div>
</div>