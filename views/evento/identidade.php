<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
use kartik\datecontrol\Module;
use kartik\datecontrol\DateControl;
use kartik\widgets\Select2;

$this->title = "Imagem Certificado";

/* @var $this yii\web\View */
/* @var $model app\models\Evento */
/* @var $form yii\widgets\ActiveForm */

?>

<script type="text/javascript">
	
	function retirarBlank(){
		document.getElementById('certificadosform').target = '_self';
	}

</script>


<div class="evento-identidade">

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
            <div class="clear"></div>
        </div>



	    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data','id' => 'certificadosform','target' => '_blank']]); ?>


	   	<h3> Selecione uma imagem para o certificado </h3>	    
	    <div style="position: relative; border: 3px solid #000000; padding: 10px;"> 
	    	<?= $form->field($model, 'imagem')->fileInput() ?>
	    </div>
		<p> <br>
				Recomendações para a imagem: <br>
					<b>
					<ul> 
						<li> A imagem deve ser composta de Cabeçalho, Área de Assinatura e Rodapé.</li>  <br>
						<li> O cabeçalho deve ter aproximadamente 30% da altura da imagem.</li> <br>
						<li> A Área de Assinatura juntamente com o Cabeçalho deve ter aproximadamente 25% da altura da imagem.</li> <br>
						<li> A Largura e Altura da imagem deve ter aproximadamente 3450 pixels x 2400 pixels.</li> <br>
					</ul>
					Para informações mais detalhadas, consulte o manual do usuário. <br>
					</b>
				<br>



	    	Obs.: A não seleção de uma imagem acarretará que 
	    		os certificados serão gerados em um formato padrão.
	    		Clicando em <b> Visualizar Certificado </b> é possível observar a prévia de como ficará o certificado.
	    </p>

	    <div class="form-group">
	    <div style = "text-align:center;"> 
	    	<?= "<br>" ?>
			<?= Html::submitButton('Visualizar Certificado', ['class' => 'btn btn-warning', 'name' => 'frag2']); ?>
			<?= Html::submitButton('Salvar Imagem', ['onclick' => 'retirarBlank()' ,'name' => 'frag','class' => 'btn btn-success']); ?>
		</div>

	    </div>
		
		<?php ActiveForm::end(); ?>
	</div>


<br><br><br>

</div>