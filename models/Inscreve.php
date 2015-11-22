<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inscreve".
 *
 * @property integer $usuario_idusuario
 * @property integer $evento_idevento
 * @property string $credenciado
 * @property integer $pacote_idpacote
 *
 * @property Evento $eventoIdevento
 * @property Pacote $pacoteIdpacote
 * @property User $usuarioIdusuario
 */
class Inscreve extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inscreve';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usuario_idusuario', 'evento_idevento', ], 'required'],
            [['usuario_idusuario', 'evento_idevento', 'pacote_idpacote', 'credenciado'], 'integer'],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'usuario_idusuario' => 'Usuario Idusuario',
            'evento_idevento' => 'Nome do Evento',
            'pacote_idpacote' => 'Pacote Idpacote',
            'credenciado' => 'Credenciado',
            'usuario.nome' => 'Nome',
            'pacote.titulo' => 'Pacote',
            'evento.tipo.titulo' => 'Tipo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventoIdevento()
    {
        return $this->hasOne(Evento::className(), ['idevento' => 'evento_idevento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacoteIdpacote()
    {
        return $this->hasOne(Pacote::className(), ['idpacote' => 'pacote_idpacote']);
    }

        public function getPacote()
    {
        return $this->hasOne(Pacote::className(), ['idpacote' => 'pacote_idpacote']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioIdusuario()
    {
        return $this->hasOne(User::className(), ['idusuario' => 'usuario_idusuario']);
    }

    public function getEvento()
    {
        return $this->hasOne(Evento::className(), ['idEvento' => 'evento_idevento']);
    }

    public function getUsuario()
    {
        return $this->hasOne(User::className(), ['idusuario' => 'usuario_idusuario']);
    }

    public function getDescricaoCredenciado()
    {
                if ($this->credenciado){
                    return 'Sim';
                }else{
                    return 'Não';
                }
            
    }

    public function getpacoteTitulo (){
                if($this->pacote == NULL){
                    return "--";
                }
                else{

                    return $this->pacote->titulo;
                }

    }

public function cancelar($id_evento)
    {
        $id_usuario = Yii::$app->user->identity->idusuario;

        $sql = "DELETE FROM inscreve WHERE usuario_idusuario = '$id_usuario' AND evento_idevento = '$id_evento'";
        
        try{
            
            $resultado = Yii::$app->db->createCommand($sql)->execute();

        }catch(IntegrityException $e){

            return 0;

        }catch(Exception $e){

                return 0;

        }

        return $resultado;
    }


public function inscrever($id_evento)
    {
        $id_usuario = Yii::$app->user->identity->idusuario;

        $sql = "INSERT INTO inscreve VALUES ('$id_usuario','$id_evento',0,NULL)";

        try{
            
            $resultado = Yii::$app->db->createCommand($sql)->execute();

        }catch(IntegrityException $e){

            return 0;

        }catch(Exception $e){

                return 0;

        }

        return $resultado;
    }

public function inscreverComPacote($id_evento,$id_pacote)
    {

        //verificar se há vagas para o referido pacote !!




        $id_usuario = Yii::$app->user->identity->idusuario;

        $sql = "INSERT INTO inscreve VALUES ('$id_usuario','$id_evento',0,'$id_pacote')";
        
    try{
            
            $resultado = Yii::$app->db->createCommand($sql)->execute();

        }catch(\IntegrityException $e){

            return 0;

        }catch(\Exception $e){

            return 0;

        }

        return $resultado;
    }

//reduzir vagas da tabela evento
public function reduzir_vagas_evento($id_evento,$resultado){

    $sql = "update evento set
        vagas = vagas -".$resultado."
        where idevento = $id_evento";

        $resultado = Yii::$app->db->createCommand($sql)->execute();

        return $resultado;
}

//funcao abaixo usada para reduzir as vagas da tabela item de programacao
//opcao 1 -> quando nao existe pacote, opcao 2 -> há pacotes
public function reduzirVagas($id_pacote , $id_evento,$opcao)
    {

        if($opcao == 1) {
        //reduzir vaga da tabela itens-programacao e TAMBÉM da tabela evento
        $sql = "update itemProgramacao as i, evento as e 
            set i.vagas = i.vagas -1 , e.vagas = e.vagas -1 
            where i.evento_idevento = $id_evento AND e.idevento = $id_evento";
            $resultado = Yii::$app->db->createCommand($sql)->execute();
        }

        else{
            //reduzir vagas da tabela item-programacao (relacionados a um pacote)
        $sql = "update itemProgramacao set vagas = vagas -1 where iditemProgramacao in (
                select itemProgramacao_iditemProgramacao from itemProgramacao_has_pacote 
                as item join pacote as p on item.pacote_idpacote = p.idpacote 
                where idpacote =".$id_pacote.")";

            $resultado = Yii::$app->db->createCommand($sql)->execute();
            $result = new Inscreve;

            //linha abaixo para reduzir vagas da tabela EVENTO !
            $resultado = $resultado + $result->reduzir_vagas_evento($id_evento,$resultado);
        }
        
        return $resultado;
    }


public function aumentar_vagas_evento($id_evento,$resultado){

    $sql = "update evento set
        vagas = vagas +".$resultado." 
        where idevento = $id_evento";

        $resultado = Yii::$app->db->createCommand($sql)->execute();

        return $resultado;
}


public function aumentarVagas($id_pacote , $id_evento,$opcao)
    {

        if($opcao == 1) {
        //reduzir vaga da tabela itens-programacao e TAMBÉM da tabela evento
        $sql = "update itemProgramacao as i, evento as e 
            set i.vagas = i.vagas +1 , e.vagas = e.vagas +1 
            where i.evento_idevento = $id_evento AND e.idevento = $id_evento";
            $resultado = Yii::$app->db->createCommand($sql)->execute();
        }

        else{
            //reduzir vagas da tabela item-programacao (relacionados a um pacote)
        $sql = "update itemProgramacao set vagas = vagas +1 where iditemProgramacao in (
                select itemProgramacao_iditemProgramacao from itemProgramacao_has_pacote 
                as item join pacote as p on item.pacote_idpacote = p.idpacote 
                where idpacote =".$id_pacote.")";


            $resultado = Yii::$app->db->createCommand($sql)->execute();
            $result = new Inscreve;

            //linha abaixo para reduzir vagas da tabela EVENTO !
            $resultado = $resultado + $result->aumentar_vagas_evento($id_evento,$resultado);
        }
        
        return $resultado;
    }

    public function possuiItemProgramacao(){

        $id_evento = Yii::$app->request->post('evento_idevento'); 

        $results = ItemProgramacao::find()->where(['evento_idevento' => $id_evento])->count();
        return $results;
    }

    public function possuiVagasEvento(){

        //$qtd_item_programacao = $this->possuiItemProgramacao();
        $id_evento = Yii::$app->request->post('evento_idevento'); 
        
        return $results = Evento::findOne(['idevento' => $id_evento])->vagas;
    }


    public function possuiVagasPacote($idpacote){

       $id_evento = Yii::$app->request->post('evento_idevento'); 
       $id_pacote = $idpacote;

       $query = ItemProgramacaoHasPacote::find()->Where('itemProgramacao.vagas <= 0')
       ->andWhere(['pacote_idpacote' => $id_pacote])->joinWith('itemProgramacao')->count();
       
       // se resultado for ZERO, então HÁ VAGAS !!!!!
       return $query;

    }


//POSSIBILITA que a view do Evento, apresente apenas 1 dos botões: Inscrever-se ou Cancelar
public function VerificaInscrito($params)
    {

        $user = Yii::$app->user->identity->idusuario;

        if (!Yii::$app->user->isGuest) {
            $sql = "SELECT COUNT(*) FROM inscreve WHERE evento_idevento = '$params[id]' 
                    AND usuario_idusuario = '$user'";

                    $cont = Yii::$app->db->createCommand($sql)
                 ->queryScalar();

                return $cont;
        }    
    }

//POSSIBILITA que a view do Evento não apresente o botão de cancelar inscricao, qndo o evento
    // já estiver encerrado
public function VerificaEncerramento($params)
    {

        $data_atual = strtotime(date('Y/m/d'));

        $data_fim = strtotime(Evento::findOne(['idevento' => $params['id']])->dataFim);

        if($data_atual >= $data_fim){

            return 1;
        }

        return 0;

    }
}
