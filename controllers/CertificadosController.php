<?php

namespace app\controllers;

use Yii;
use app\models\Inscreve;
use app\models\InscreveSearch;
use app\models\User;

use app\models\EventoSearch;
use app\models\Evento;
use app\models\ItemProgramacao;
use app\models\ItemProgramacaoSearch;
use app\models\Voluntario;
use app\models\VoluntarioSearch;
use app\models\Palestrante;
use yii\web\ForbiddenHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;

use mPDF;

class CertificadosController extends \yii\web\Controller
{

    protected function findModel($id)
    {
        if (($model = Evento::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

        protected function findModelUser($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

        protected function findModelVoluntario($id)
    {
        if (($model = Voluntario::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

        protected function findModelItem($id)
    {
        if (($model = ItemProgramacao::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

        protected function findModelPalestrante($id)
    {
        if (($model = Palestrante::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionIndex()
    {
        $this->autorizaUsuario();
        $searchModel = new InscreveSearch();
        $dataProvider = $searchModel->searchCredenciados(Yii::$app->request->queryParams);
        $dataProvider2 = $searchModel->searchPalestrantes(Yii::$app->request->queryParams);
        $dataProvider3 = $searchModel->searchVoluntarios(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'dataProvider3' => $dataProvider3,

        ]);
    }
    
    public function actionCredenciado()
    {
        $id_evento = Yii::$app->request->post('evento_idevento');
        $evento = Evento::findOne($id_evento);

        $searchModel = new InscreveSearch();
        $dataProvider = $searchModel->searchCredenciados(Yii::$app->request->queryParams);
        return $this->render('credenciado', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'evento' => $evento,
        ]);
    }

    public function actionPalestrante()
    {
        $id_evento = Yii::$app->request->post('evento_idevento');
        $evento = Evento::findOne($id_evento);
        
        $searchModel = new InscreveSearch();
        $dataProvider2 = $searchModel->searchPalestrantes(Yii::$app->request->queryParams);
        return $this->render('palestrante', [
            'searchModel' => $searchModel,
            'dataProvider2' => $dataProvider2,
            'evento' => $evento, 

        ]);
    }

    public function actionVoluntario()
    {
        $id_evento = Yii::$app->request->post('evento_idevento');
        $evento = Evento::findOne($id_evento);
        
        $searchModel = new InscreveSearch();
        $dataProvider3 = $searchModel->searchVoluntarios(Yii::$app->request->queryParams);
        return $this->render('voluntario', [
            'searchModel' => $searchModel,
            'dataProvider3' => $dataProvider3,
            'evento' => $evento,

        ]);
    }

public function actionIdsusuarios()
    {

        $ids = Yii::$app->request->get('ids');        
        $ids = explode(',', $ids);
        for($i=0 ; $i< sizeof($ids) ; $i++){
            $ids[$i] = $ids[$i] * 997;
        }
        $ids = implode(",", $ids);
        echo $ids; 
    }

public function actionPdfcredenciados()
    {   
        $id_evento = Yii::$app->request->get('evento_idevento');        
        $tipo_usuario = Yii::$app->request->get('tipousuario');
        $ids = Yii::$app->request->get('ids');        
        $ids_usuario_vetor  = explode(',', $ids);
        for($i=0 ; $i< sizeof($ids_usuario_vetor) ; $i++){
            $ids_usuario_vetor[$i] = $ids_usuario_vetor[$i] / 997;
        }
        $count = sizeof($ids_usuario_vetor);
        $i = 0;

        while($i<$count){
            $model = $this->findModelUser($ids_usuario_vetor[$i]);
            $nome[$i] = $model->nome;
            $i++;
        }

        $nome_array = implode(",", $nome);

        //print_r($ids_usuario_vetor);

        $session = Yii::$app->session;

        $session->set('tipousuario_certificado', $tipo_usuario);
        $session->set('usuario_certificado', $nome_array);
        $session->set('usuarios_id',$ids_usuario_vetor);
        $session->set('evento_idevento', $id_evento);

        Yii::$app->response->redirect(['certificados/pdf']);

    }

    public function actionPdfpalestrantes()
    {   
        $id_evento = Yii::$app->request->get('evento_idevento');        
        $tipo_usuario = Yii::$app->request->get('tipousuario');
        $ids = Yii::$app->request->get('ids');
        $ids_itemProgramacao  = explode(',', $ids);
        for($i=0 ; $i< sizeof($ids_itemProgramacao) ; $i++){
            $ids_itemProgramacao[$i] = $ids_itemProgramacao[$i] / 997;
        }
        $count = sizeof($ids_itemProgramacao);
        $i = 0;

        while($i<$count){
            //$model = $this->findModelItem($ids_itemProgramacao[$i]); //<-- apagar
            //$aux[$i] = $model->palestrante_idPalestrante;             //<-- apagar
            $model = $this->findModelPalestrante($ids_itemProgramacao[$i]);
            $nome[$i] = $model->nome;

            $i++;
        }

        $nome_array = implode(",", $nome);

        //print_r($ids_usuario_vetor);

        $session = Yii::$app->session;

        $session->set('tipousuario_certificado', $tipo_usuario);
        $session->set('usuario_certificado', $nome_array);
        $session->set('evento_idevento', $id_evento);

        Yii::$app->response->redirect(['certificados/pdf']);

    }

    public function actionPdfvoluntarios()
    {   
        $id_evento = Yii::$app->request->get('evento_idevento');        
        $tipo_usuario = Yii::$app->request->get('tipousuario');
        $ids = Yii::$app->request->get('ids');
        $ch = Yii::$app->request->get('ch');                
        $ids_usuario_vetor  = explode(',', $ids);
        for($i=0 ; $i< sizeof($ids_usuario_vetor) ; $i++){
            $ids_usuario_vetor[$i] = $ids_usuario_vetor[$i] / 997;
        }
        $count = sizeof($ids_usuario_vetor);
        $i = 0;

        while($i<$count){
            $model = $this->findModelVoluntario($ids_usuario_vetor[$i]);
            $nome[$i] = $model->nome;
            $i++;
        }

        $nome_array = implode(",", $nome);

        //print_r($ids_usuario_vetor);

        $session = Yii::$app->session;

        $session->set('tipousuario_certificado', $tipo_usuario);
        $session->set('usuario_certificado', $nome_array);
        $session->set('evento_idevento', $id_evento);
        $session->set('cargaHoraria',$ch);

        Yii::$app->response->redirect(['certificados/pdf']);

    }


//funcao que converte mes em numeral para extenso
public function converterMes($current){
        $mes_numero = (date('m',strtotime($current)));

        $mes = array ("janeiro","fevereiro","março","abril","maio","junho","julho",
        "agosto","setembro","outubro","novembro","dezembro");


        return $mes[$mes_numero-1];
    }

//função necessária p/ gerar certificados, pois delimita o período inicial e final do evento
   public function tag($dia_inicio,$mes_inicio,$ano_inicio,$dia_fim,$mes_fim,$ano_fim){
        
        if($mes_inicio == $mes_fim && $ano_inicio == $ano_fim){

            if ($dia_inicio == $dia_fim){
                $tag = '<b> '. $dia_inicio .' de '.$mes_fim.'
                    de '.$ano_fim.'</b>';
                $tag = 'no dia'.$tag;
            }
            else{
                $tag = '<b> '. $dia_inicio .' a '.$dia_fim .' de '.$mes_fim.'
                    de '.$ano_fim.'</b>';
                $tag = 'no período de'.$tag;
            }
        }
        else if($mes_inicio != $mes_fim && $ano_inicio == $ano_fim){
            $tag = '<b> '. $dia_inicio .' de '.$mes_inicio.'
                a '.$dia_fim .' de '.$mes_fim.'
                de '.$ano_fim.'</b>';
            $tag = 'no período de'.$tag;
        }

        else{

            $tag = '<b> '. $dia_inicio .' de '.$mes_inicio.'
                de '.$ano_inicio.' a '.$dia_fim .' de '.$mes_fim.'
                de '.$ano_fim.'</b>';
            $tag = 'no período de'.$tag;

        }

            return $tag;
    }


    public function actionPdf() {
 
        $session = Yii::$app->session;
        $tipo_usuario = $session->get('tipousuario_certificado');
        $usuarios_string = $session->get('usuario_certificado');
        $id_evento = $session->get('evento_idevento');
        $ids_usuario_vetor = $session->get('usuarios_id');
        $ch_voluntario = $session->get('cargaHoraria');

        $model = $this->findModel($id_evento);
        $modelUsuario = new User();

        $nomeCoordenador = $modelUsuario->findUser($model->responsavel);

        $session->close();
        $session->destroy();


        //$tipo_usuario = Yii::$app->request->get('tipousuario_certificado'); 
        //$id_evento = Yii::$app->request->get('evento_idevento'); 

        //$usuarios_string =Yii::$app->request->get('usuario_certificado');  

        $usuario_vetor  = explode(',', $usuarios_string);

        $count = sizeof($usuario_vetor);
        

            $pdf = new mPDF('utf-8', 'A4-L');

            $i = 0;

            while($i<$count){

                $usuario = $usuario_vetor[$i];//verificar a emissão de certificados na view do participante


                $cargaHoraria = new Inscreve();
                $cargaHoraria = $cargaHoraria->getSomaCargaHorariaPacote($id_evento,$ids_usuario_vetor[$i]);

                if ($cargaHoraria == null){

                    $cargaHoraria = $model->cargaHoraria;

                }                                                


                $i++;


            $pdf->WriteHTML(''); //se tirar isso, desaparece o cabeçalho

            if($model->imagem != NULL){

                $x=$pdf->WriteHTML("

                <style>
                    body {
                        
                        body{font-family:Arial;background-image: url(../web/uploads/".$model->imagem.") no-repeat;
                        background-image-resolution:300dpi;background-image-resize:6;}
                    }
                </style>



                    ");
                $pdf->Ln(15);
            }   
            else{   

                $pdf->SetFont("Helvetica",'B', 14);
                $pdf->MultiCell(0,6,"PODER EXECUTIVO",0, 'C');
                $pdf->MultiCell(0,6,("MINISTÉRIO DA EDUCAÇÃO"),0, 'C');
                $pdf->MultiCell(0,6,("INSTITUTO DE COMPUTAÇÃO"),0, 'C');
                //$pdf->MultiCell(0,5,("-----------------"),0, 'C');
                $pdf->SetDrawColor(0,0,0);
                $pdf->Line(5,42,290,42);
                $pdf->Image('../web/img/logo-brasil.jpg', 10, 7, 32.32);
                $pdf->Image('../web/img/ufam.jpg', 260, 7, 25.25);
            }
 
            $pdf->Ln(25);
            $pdf->SetFont('Arial','B',22);

            if($model->imagem == NULL){
                $pdf->MultiCell(0,4,("Certificado"),0, 'C');
            }

        if ($usuario == NULL){
            $nome= Yii::$app->user->identity->nome;
        }  
        else {
            $nome = $usuario;
        }

        $dia_inicio = (date('d',strtotime($model->dataIni)));
        $dia_inicio = ($dia_inicio == 1 ? '1º' : $dia_inicio); //convertendo o 1 em 1º
        $mes_inicio = $this->converterMes($model->dataIni);
        $ano_inicio = (date('Y',strtotime($model->dataIni)));

        $dia_fim = (date('d',strtotime($model->dataFim)));
        $dia_fim = ($dia_fim == 1 ? '1º' : $dia_fim); //convertendo o 1 em 1º
        $mes_fim = $this->converterMes($model->dataFim);
        $ano_fim = (date('Y',strtotime($model->dataFim)));

        $pdf->SetFont('Arial','',18);

        $pdf->Ln(25);

        $tag = $this->tag($dia_inicio,$mes_inicio,$ano_inicio,$dia_fim,$mes_fim,$ano_fim);
if( $tipo_usuario ==0){

        $pdf->WriteHTML('<p style="font-size: 20px; text-align: justify;  text-indent: 80px;">
            Certificamos que <b>'. $nome.'</b> participou do evento <b>"'. $model->descricao.'" 
            ('.$model->sigla.')</b>, com carga horária de <b>'.$cargaHoraria.
                ' hora(s)</b>, realizado '.$tag.', na cidade 
                de Manaus - AM.</p>');
}
else if ( $tipo_usuario == 1){
        $pdf->WriteHTML('<p style="font-size: 20px; text-align: justify;  text-indent: 80px;">
            Certificamos que <b>'. $nome.'</b> participou do evento <b>"'. $model->descricao.'" 
            ('.$model->sigla.')</b>, na qualidade de palestrante, realizado '.$tag.', na cidade 
                de Manaus - AM.</p>');
}
else{
        $pdf->WriteHTML('<p style="font-size: 20px; text-align: justify;  text-indent: 80px;">
            Certificamos que <b>'. $nome.'</b> participou do evento <b>"'. $model->descricao.'" 
            ('.$model->sigla.')</b>, na qualidade de voluntário, com carga horária de <b>'.$ch_voluntario.
                ' hora(s)</b>, realizado '.$tag.', na cidade 
                de Manaus - AM.</p>');
}
    
        $pdf->Ln(5);
        
        $current = date('Y/m/d');

        $currentTime = strtotime($current);

        $mes = $this->converterMes($current);
        
        
        $pdf->Cell(0,5,('Manaus, '. date('d', $currentTime).' de '. $mes. ' de '. 
            date('Y', $currentTime).'.             '),0,1, 'C');

        if($model->imagem == NULL){

            $pdf->Ln(15);

            $pdf->Ln(10);

            $pdf->WriteHTML(' <div style = "border: 0;
    border-bottom: 1px solid #000; margin-left: 25% ; margin-right:25%"> </div>
                <div align=center> '.$nomeCoordenador->nome.' </div>');

            $pdf->SetFont('Helvetica','I',8);
            $pdf->Line(5,185,290,185);
            $pdf->SetXY(10, 180);
            $pdf->MultiCell(0,5,"",0, 'C');
            $pdf->MultiCell(0,4,("Av. Rodrigo Otávio, 6.200 - Campus Universitário Senador Arthur Virgílio Filho - CEP 69077-000 - Manaus, AM, Brasil"),0, 'C');
            $pdf->MultiCell(0,4,(" Tel. (092) 3305-1193/2808/2809         E-mail: secretaria@icomp.ufam.edu.br          http://www.icomp.ufam.edu.br"),0, 'C');
            //$pdf->Image('components/com_portalsecretaria/images/icon_telefone.jpg', '40', '290');
            //$pdf->Image('components/com_portalsecretaria/images/icon_email.jpg', '73', '290');
            //$pdf->Image('components/com_portalsecretaria/images/icon_casa.jpg', '134', '290');

            // fim do aqui
        }
            if ($i != $count){
                $pdf->AddPage();
            }
    }
            $pdf->Output('');

    }


public function actionPrevisualizacao() {
 
        $nomeImagem = Yii::$app->request->get('imagem'); 
        
            $pdf = new mPDF('utf-8', 'A4-L');


                $x=$pdf->WriteHTML("

                <style>
                    body {
                        
                        body{font-family:Arial;background-image: url(../web/uploads/".$nomeImagem.") no-repeat;
                        background-image-resolution:300dpi;background-image-resize:6;}
                    }
                </style>



                    ");

            if($nomeImagem == NULL){
                $pdf->SetFont("Helvetica",'B', 14);
                $pdf->MultiCell(0,6,"PODER EXECUTIVO",0, 'C');
                $pdf->MultiCell(0,6,("MINISTÉRIO DA EDUCAÇÃO"),0, 'C');
                $pdf->MultiCell(0,6,("INSTITUTO DE COMPUTAÇÃO"),0, 'C');
                //$pdf->MultiCell(0,5,("-----------------"),0, 'C');
                $pdf->SetDrawColor(0,0,0);
                $pdf->Line(5,42,290,42);
                $pdf->Image('../web/img/logo-brasil.jpg', 10, 7, 32.32);
                $pdf->Image('../web/img/ufam.jpg', 260, 7, 25.25);

            }else{
                $pdf->Ln(15);
            }
            
            $pdf->Ln(25);
            $pdf->SetFont('Arial','B',22);

            if($nomeImagem == NULL){
                $pdf->MultiCell(0,4,("Certificado"),0, 'C');
            }


        $dia_inicio = (date('d',strtotime($model->dataIni)));
        $dia_inicio = ($dia_inicio == 1 ? '1º' : $dia_inicio); //convertendo o 1 em 1º
        $mes_inicio = $this->converterMes($model->dataIni);
        $ano_inicio = (date('Y',strtotime($model->dataIni)));

        $dia_fim = (date('d',strtotime($model->dataFim)));
        $dia_fim = ($dia_fim == 1 ? '1º' : $dia_fim); //convertendo o 1 em 1º
        $mes_fim = $this->converterMes($model->dataFim);
        $ano_fim = (date('Y',strtotime($model->dataFim)));

        $pdf->SetFont('Arial','',18);

        $pdf->Ln(25);

        $tag = $this->tag($dia_inicio,$mes_inicio,$ano_inicio,$dia_fim,$mes_fim,$ano_fim);

        $pdf->WriteHTML('<p style="font-size: 20px; text-align: justify;  text-indent: 80px;">
            Certificamos que <b> [Nome do Participante] </b> participou do evento <b> [Nome do Evento] 
            ([Sigla do Evento])</b>, com carga horária de <b> [Carga Horária]
                 hora(s)</b>, realizado no período de <b>[Data Inicial] a [Data Final]</b>, na cidade 
                de Manaus - AM.</p>');
    
        $pdf->Ln(5);
        
        $current = date('Y/m/d');

        $currentTime = strtotime($current);

        $mes = $this->converterMes($current);
        
        
        $pdf->Cell(0,5,('Manaus, '. date('d', $currentTime).' de '. $mes. ' de '. 
            date('Y', $currentTime).'.             '),0,1, 'C');

        if($nomeImagem == NULL){

            $pdf->Ln(15);

            $pdf->Ln(10);

            $pdf->WriteHTML(' <div style = "border: 0;
                border-bottom: 1px solid #000; margin-left: 25% ; margin-right:25%"> </div>
                <div align=center> [Nome do Coordenador do Evento] </div>');

            $pdf->SetFont('Helvetica','I',8);

            $pdf->Line(5,185,290,185);
            $pdf->SetXY(10, 180);
            $pdf->MultiCell(0,5,"",0, 'C');
            $pdf->MultiCell(0,4,("Av. Rodrigo Otávio, 6.200 - Campus Universitário Senador Arthur Virgílio Filho - CEP 69077-000 - Manaus, AM, Brasil"),0, 'C');
            $pdf->MultiCell(0,4,(" Tel. (092) 3305-1193/2808/2809         E-mail: secretaria@icomp.ufam.edu.br          http://www.icomp.ufam.edu.br"),0, 'C');
            //$pdf->Image('components/com_portalsecretaria/images/icon_telefone.jpg', '40', '290');
            //$pdf->Image('components/com_portalsecretaria/images/icon_email.jpg', '73', '290');
            //$pdf->Image('components/com_portalsecretaria/images/icon_casa.jpg', '134', '290');

            // fim do aqui
        }

            $pdf->Output('');
    }

    protected function autorizaUsuario(){
        if(Yii::$app->user->isGuest || Yii::$app->user->identity->tipoUsuario == 3){
            throw new ForbiddenHttpException('Acesso Negado!! Recurso disponível apenas para administradores.');
        }
    }

}
