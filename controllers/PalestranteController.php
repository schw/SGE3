<?php

namespace app\controllers;

use Yii;
use app\models\Palestrante;
use app\models\PalestranteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\db\IntegrityException;

/**
 * PalestranteController implements the CRUD actions for Palestrante model.
 */
class PalestranteController extends Controller
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
     * Lists all Palestrante models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->autorizaUsuario();
        $searchModel = new PalestranteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Palestrante model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $this->autorizaUsuario();
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Palestrante model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->autorizaUsuario();
        $model = new Palestrante();

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                $this->mensagens('success', 'Palestrante Cadastrado', 'Palestrante foi Cadastrado com Sucesso');
            }else{
                $this->mensagens('danger', 'Palestrante Não Cadastrado', 'Houve um erro ao adicionar o Palestrante');
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Palestrante model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $this->autorizaUsuario();
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                $this->mensagens('success', 'Palestrante Alterado', 'Palestrante foi Alterado com Sucesso');
            }else{
                $this->mensagens('danger', 'Palestrante Não Alterado', 'Houve um erro ao Alterar o Palestrante');
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Palestrante model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->autorizaUsuario();
        $model = $this->findModel($id);
        $palestrante = $model->nome;
        try{
            $model->delete();
            $this->mensagens('success', 'Palestrante Removido', 'Palestrante \''.$palestrante.'\' foi removido com sucesso');
        }catch(IntegrityException $e){
            $this->mensagens('danger', 'Palestrante Não Removido', 'O palestrante \''.$palestrante.'\' está sendo utilizado em algum evento. Por favor, Remova o evento antes');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Palestrante model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Palestrante the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Palestrante::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function autorizaUsuario(){
        if(Yii::$app->user->isGuest || Yii::$app->user->identity->tipoUsuario == 3){
            throw new ForbiddenHttpException('Acesso Negado!! Recurso disponível apenas para administradores.');
        }
    }

    /*Tipo: success, danger, warning*/
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
}
