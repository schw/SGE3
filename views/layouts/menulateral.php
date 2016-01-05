<?php use kartik\widgets\SideNav; ?>

	<div class="navbar-default sidebar">
        <?php
        	$heading = 'Opções';
         	echo SideNav::widget([
            'type' => SideNav::TYPE_DEFAULT,
            'encodeLabels' => false,
            //'heading' => $heading,
            'items' => [
                ['label' => 'Home', 'icon' => 'home', 'url' => ['site/index']],
	            ['label' => 'Recuperar Senha',  'icon' => 'info-sign', 'url' => ['site/recuperar'], 'visible' => Yii::$app->user->isGuest],
	            ['label' => 'Eventos', 'icon' => 'tags', 'visible' => (!Yii::$app->user->isGuest &&  Yii::$app->user->identity->tipoUsuario != 3),'items' => [
	                ['label' => 'Eventos Ativos - Abertos', 'url' => ['evento/gerenciareventos', 'inscricoes' => 'aberta']],
	                ['label' => 'Eventos Ativos - Fechados', 'url' => ['evento/gerenciareventos', 'inscricoes' => 'fechada']],
	                ['label' => 'Eventos Passados', 'url' => ['evento/gerenciareventos', 'status' => 'passado']],],], 
	            ['label' => 'Eventos Ativos', 'icon' => 'tags', 'visible' => (!Yii::$app->user->isGuest &&  Yii::$app->user->identity->tipoUsuario == 3), 'url' => ['evento/index']],
	            ['label' => 'Cadastre-se', 'icon' => 'info-sign', 'url' => ['/user/create'], 'visible' => Yii::$app->user->isGuest],
	            ['label' => 'Voluntários', 'icon' => 'user', 'url' => ['voluntario/index'], 'visible' => (!Yii::$app->user->isGuest &&  Yii::$app->user->identity->tipoUsuario != 3)],
	            ['label' => 'Palestrantes', 'icon' => 'blackboard', 'url' => ['palestrante/index'], 'visible' => (!Yii::$app->user->isGuest &&  Yii::$app->user->identity->tipoUsuario != 3)],
	            ['label' => 'Relatórios', 'icon' => 'stats', 'url' => ['relatorios/index'], 'visible' => (!Yii::$app->user->isGuest &&  Yii::$app->user->identity->tipoUsuario != 3)],
	            ['label' => 'Minhas Inscrições', 'icon' => 'flag', 'url' => ['inscreve/index'], 'visible' => 
	            (!Yii::$app->user->isGuest &&  Yii::$app->user->identity->tipoUsuario == 3),'items' => [
	                ['label' => 'Inscrições Atuais', 'url' => ['inscreve/index']],
	                ['label' => 'Inscrições Passadas', 'url' => ['inscreve/passadas']],],], 
	            ['label' => 'Perfil', 'icon' => 'user', 'url' => ['user/view'], 'visible' => (!Yii::$app->user->isGuest)],
                ],
            ]);        
        ?>
	</div>