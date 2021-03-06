<?php

namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\Evento;
use app\models\Palestrante;
use app\models\Local;
use app\models\Tipo;
use app\models\EventoSearch;
use app\models\Inscreve;
use app\models\InscreveSearch;
use app\models\PacoteSearch;
use app\models\CoordenadorHasEvento;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * EventoController implements the CRUD actions for Evento model.
 */
class EventoController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lista Todos os eventos cadastrados. Apenas visualização
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EventoSearch();
        $dataProvider = $searchModel->searchEventos(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lista Eventos para CRUD para o usuário autenticado.
     * @return mixed
     */
    public function actionGerenciareventos()
    {
        $this->autorizaUsuario();
        $params['status'] = filter_input(INPUT_GET, 'status');
        $params['inscricoes'] = filter_input(INPUT_GET, 'inscricoes');

        $searchModel = new EventoSearch();
        $dataProvider = $searchModel->searchEventosResponsavel($params);
        $dataProvider2 = $searchModel->searchEventosCoodenadores($params);

        if($params['status'] == 'passado'){

            return $this->render('passados', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'dataProvider2' => $dataProvider2,
            ]);

        }else if($params['inscricoes'] == 'fechada'){
            
            return $this->render('inscricoesFechadas', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'dataProvider2' => $dataProvider2,
            ]);

        }else if($params['inscricoes'] == 'naoiniciada'){
            
            return $this->render('inscricoesNaoIniciadas', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'dataProvider2' => $dataProvider2,
            ]);

        }else{

            return $this->render('gerenciarEventos', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'dataProvider2' => $dataProvider2,
            ]);
        }
    }


    /**
     * Displays a single Evento model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id){

        $dataProvider = null;
        $this->findModel($id);
        
        Yii::$app->user->isGuest ? $verificaInscrito = 0 : 
        
        $verificaInscrito = (new Inscreve())->verificaInscrito
                (Yii::$app->request->queryParams);
        
        $verificaEncerramento = (new Inscreve())->verificaEncerramento
                (Yii::$app->request->queryParams);

        $verificaCredenciamento = (new Inscreve())->verificaCredenciamento
                (Yii::$app->request->queryParams);

        $pacote = (new Inscreve())->possuiPacote
                (Yii::$app->request->queryParams);

        if ($pacote == 0) {
            $verificaVagas = (new Inscreve())->possuiVagasEvento
                (Yii::$app->request->queryParams);
        }
        else{

            $searchModel = new PacoteSearch();
            $verificaVagas = $searchModel->searchEventoPacoteDisponivel($id);
            $verificaVagas = $verificaVagas->getCount();

            $dataProvider = $searchModel->searchEventoPacoteDisponivel($id);
        }

        $model = $this->findModel($id);

        if (Yii::$app->user->isGuest){
            return $this->render('view', [
                'model' => $model,
                'allow'=> $model->allow,
                'inscrito' => $verificaInscrito,
                'encerrado' => $verificaEncerramento,
                'credenciamento' => $verificaCredenciamento,
                'existeVagas' => $verificaVagas,
                'dataProvider' => $dataProvider,
            ]);    
        }else{
        $responsavel = (CoordenadorHasEvento::find()->where(['usuario_idusuario' => Yii::$app->user->identity->idusuario])
            ->andWhere(['evento_idevento' => $model->idevento])->count());
        return $this->render('view', [
            'model' => $model,
            'allow'=> $model->allow,
            'inscrito' => $verificaInscrito,
            'encerrado' => $verificaEncerramento,
            'credenciamento' => $verificaCredenciamento,
            'existeVagas' => $verificaVagas,
            'responsavel' => $responsavel,
            'dataProvider' => $dataProvider,
        ]);
        }
    }

    /**
     * Creates a new Evento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate(){
        
        /*Controle de Permissão Início*/
        $this->autorizaUsuario();
        Yii::$app->user->identity->tipoUsuario == 2 ? $this->redirect(['index']) : 
        /*Controle de Permissão Fim*/

        $model = new Evento();
        $model->responsavel = Yii::$app->user->identity->idusuario;
        $model->allow = null;
        
        $arrayPalestrante = ArrayHelper::map(Palestrante::find()->all(), 'idPalestrante', 'nome');
        $arrayLocal = ArrayHelper::map(Local::find()->all(), 'idlocal', 'descricao');
        $arrayTipo = ArrayHelper::map(Tipo::find()->all(), 'idtipo', 'titulo');
        
        
        if ($model->load(Yii::$app->request->post())) {
            
            $model->imagem2 = $model->upload(UploadedFile::getInstance($model, 'imagem2'),'uploads/identidade/');
            if(!$model->save(true))
                return $this->render('create', [
                    'model' => $model,
                    'arrayTipo' => $arrayTipo,
                    'arrayLocal' => $arrayLocal,
                    'arrayPalestrante' => $arrayPalestrante,
                ]);
            $this->mensagens('success', 'Evento Criado', 'Evento criado com Sucesso');
            return $this->redirect(['evento/identidade', 'idevento' => $model->idevento]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'arrayTipo' => $arrayTipo,
                'arrayLocal' => $arrayLocal,
                'arrayPalestrante' => $arrayPalestrante,
            ]);
        }
    }

    /**
     * Updates an existing Evento model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->autorizaUsuario();
        $model = $this->findModel($id);

        !$model->canAccess() ? $this->redirect(['evento/index']) :
        
        $imagem = $model->imagem2;
        $arrayPalestrante = ArrayHelper::map(Palestrante::find()->all(), 'idPalestrante', 'nome');
        $arrayLocal = ArrayHelper::map(Local::find()->all(), 'idlocal', 'descricao');
        $arrayTipo = ArrayHelper::map(Tipo::find()->all(), 'idtipo', 'titulo');

        if ($model->load(Yii::$app->request->post())) {
            $model->imagem2 = $model->upload(UploadedFile::getInstance($model, 'imagem2'),'uploads/identidade/');
            if($model->imagem2 == null)
                $model->imagem2 = $imagem;
            
            if(!$model->save(true))
                return $this->render('update', [
                    'model' => $model,
                    'arrayTipo' => $arrayTipo,
                    'arrayLocal' => $arrayLocal,
                    'arrayPalestrante' => $arrayPalestrante,
                ]);

            $this->mensagens('success', 'Evento Atualizado', 'Evento atualizado com Sucesso');
            return $this->redirect(['evento/identidade', 'idevento' => $model->idevento]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'arrayTipo' => $arrayTipo,
                'arrayLocal' => $arrayLocal,
                'arrayPalestrante' => $arrayPalestrante,
            ]);
        }
    }

    /**
     * Deletes an existing Evento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->autorizaUsuario();

        $model = $this->findModel($id);
        
        if($model->delete())
            $this->mensagens('success', 'Evento Removido', 'Evento removido com Sucesso');
        else
            $this->mensagens('danger', 'Evento Não Removido', 'Evento pode ser Removido');

        return $this->redirect(['gerenciareventos']);
    }

    public function actionIdentidade($idevento){
        $this->autorizaUsuario();
        
        $model = $this->findModel($idevento);

        !$model->canAccess() ? $this->redirect(['evento/index']) :

        $redicionamento = 'certificados/previsualizacao';

        if (isset($_POST['frag'])){
            $redicionamento = 'gerenciareventos';
        }
        else if (isset($_POST['frag2'])){
            $model = $this->findModel($idevento);
            $model->imagem = $model->upload(UploadedFile::getInstance($model, 'imagem'),'uploads/');
            return $this->redirect([$redicionamento,
                'imagem' => $model->imagem,
            ]);
        }
        
        $imagem = $model->imagem;
        if ($model->load(Yii::$app->request->post())) {
            $model->imagem = $model->upload(UploadedFile::getInstance($model, 'imagem'),'uploads/');
            if($model->imagem == null)
                $model->imagem = $imagem;
            $model->save();

            if($model->imagem != null)
                $this->mensagens('success', 'Imagem Atualizada', "A identidade visual foi atualizada com sucesso.");

            return $this->redirect([$redicionamento,
                'imagem' => $model->imagem,
                'inscricoes' => 'naoiniciada',
            ]);
        }

        return $this->render('identidade', [
                'model' => $model,
            ]);
    }

    /**
     * Finds the Evento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Evento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Evento::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Página Solicitada não Encontrada.');
        }
    }

    protected function autorizaUsuario(){
        if(Yii::$app->user->isGuest || Yii::$app->user->identity->tipoUsuario == 3){
            return $this->redirect(['index']);
        }
    }

    /* Envio de mensagens para views
       Tipo: success, danger, warning*/
    protected function mensagens($tipo, $titulo, $mensagem){
        Yii::$app->session->setFlash($tipo, [
            'type' => $tipo,
            'duration' => 5000,
            'icon' => 'home',
            'message' => $mensagem,
            'title' => $titulo,
            'positonY' => 'bottom',
            'positonX' => 'right'
        ]);
    }

    public function actionAbrir()
    {   
        if (isset($_GET['id'])) {
            $id = filter_input(INPUT_GET, 'id');    
        }
        else{
            return $this->redirect(['gerenciareventos', 'aberta']);            
        }
        
        $model = $this->findModel($id);

        $model->allow = 1;
        
        if ($model->save()){

            $this->mensagens('success', 'Abertura das Inscrições', 'As inscrições deste evento foram abertas com sucesso.');

            return $this->redirect(['gerenciareventos', 'aberta']);            
        }

        else{

            //inserir messsagem de erro, $model->getErrors();
            $this->mensagens('danger', 'Abertura das Inscrições', 'Não foi possível abrir as inscrições deste evento.');

            return $this->redirect(['gerenciareventos', 'inscricoes' => 'fechada']);            

        }

    }

    public function actionFechar()
    {   
        if (isset($_GET['id'])) {
            $id = filter_input(INPUT_GET, 'id');    
        }
        else{
            return $this->redirect(['gerenciareventos']);            
        }
        
        $model = $this->findModel($id);

        $model->allow = 0;
        
        if ($model->save()){

            $this->mensagens('success', 'Encerramento das Inscrições', 'As inscrições deste evento foram encerradas com sucesso.');

            return $this->redirect(['gerenciareventos', 'inscricoes' => 'fechada']);            
        }

        else{

            $this->mensagens('danger', 'Encerramento das Inscrições', 'As inscrições deste evento NÃO foram encerradas com sucesso.');

            return $this->redirect(['gerenciareventos', 'inscricoes' => 'aberta']);            

        }

    }
}
