<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "evento".
 *
 * @property string $idevento
 * @property string $sigla
 * @property string $descricao
 * @property string $dataIni
 * @property string $dataFim
 * @property string $horaIni
 * @property string $horaFim
 * @property integer $vagas
 * @property integer $cargaHoraria
 * @property string $imagem
 * @property string $detalhe
 * @property integer $allow
 * @property integer $responsavel
 *
 * @property Usuario $responsavel0
 * @property Inscreve[] $inscreves
 * @property Usuario[] $usuarioIdusuarios
 * @property ItemProgramacao[] $itemProgramacaos
 * @property Pacote[] $pacotes
 */
class Evento extends \yii\db\ActiveRecord
{

    public $qtd_evento; // não apagar, é necessário para o relatório
    public $nome; // não apagar, é necessário para o relatório
    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return 'evento';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sigla', 'descricao', 'dataIni', 'dataFim', 'horaIni', 'horaFim', 'cargaHoraria',
            'responsavel', 'tipo_idtipo', 'local_idlocal'], 'required', 'message' => 'Este campo é Obrigatório'],
            [['vagas', 'allow', 'responsavel','palestrante_idPalestrante', 'tipo_idtipo', 'local_idlocal'], 'integer'],
            [['vagas', 'cargaHoraria'], 'integer', 'min' => 0],
            [['dataIni', 'dataFim'], 'string',],
            [['dataIni'], 'validateDateIni'],
            [['dataFim'], 'validateDateFim'],
            [['horaIni', 'horaFim'], 'safe'],
            [['horaFim'], 'validadeHoraFim'],
            [['sigla', 'descricao'], 'string', 'max' => 45],
            [['imagem', 'imagem2'], 'string'],
            [['detalhe'], 'string', 'max' => 800],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idevento' => 'Idevento',
            'sigla' => '*Sigla',
            'descricao' => '*Título',
            'dataIni' => '*Data Inicial',
            'dataFim' => '*Data Final',
            'horaIni' => '*Hora Inicial',
            'horaFim' => '*Hora Final',
            'horaini' => '*Hora Inicial',
            'horafim' => '*Hora Final',
            'vagas' => 'Vagas',
            'cargaHoraria' => '*Carga Horária',
            'imagem' => '',
            'imagem2' => 'Identidade Visual',
            'detalhe' => 'Detalhe',
            'allow' => '*Status',
            'responsavel' => '*Responsável',
            'tipo_idtipo' => '*Tipo',
            'tipo.titulo' => 'Tipo',
            'local_idlocal' => '*Local',
            'local.descricao' => 'Local',
            'credenciado' => 'Credenciado',
            'palestrante_idPalestrante' => 'Palestrante',
            'dataini' => '*Data Inicial',
            'datafim' => '*Data Final',
            'CargaHoraria' => '*Carga Horária',
            'qtd_evento' => 'Total de inscritos',
            'inscritoseventogeral' => 'Total de inscritos',
        ];
    }

    /*Funções para validação de atributos*/
    public function validateDateIni($attribute, $params){
        if (!$this->hasErrors()) {
            if ($this->dataIni < date('Y-m-d')) {
                $this->addError($attribute, 'Informe uma data igual ou posterior a '.date('d-m-Y'));
            }
        }
    }

    public function validateDateFim($attribute, $params){
        if (!$this->hasErrors()) {
            if ($this->dataFim < $this->dataIni) {
                $this->addError($attribute, 'Informe uma data igual ou posterior a '.date("d-m-Y", strtotime($this->dataIni)));
            }
        }
    }

    public function validadeHoraFim($attribute, $params){
        if (!$this->hasErrors()) {
            if ($this->dataIni == $this->dataFim && $this->horaFim <= $this->horaIni) {
                $this->addError($attribute, 'Informe um horário acima do horário inicial');
            }
        }
    }

    public function beforeDelete(){
    if((new PacoteSearch())->searchEventoPacoteDisponivel($this->idevento)->count > 0)
        if(!Pacote::deleteAll(['evento_idevento' => $this->idevento]) && !ItemProgramacao::deleteAll(['evento_idevento' => $this->idevento]))
            return false;
    return true;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResponsavel0()
    {
        return $this->hasOne(User::className(), ['idusuario' => 'responsavel']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPalestrante()
    {
        return $this->hasOne(Palestrante::className(), ['idPalestrante' => 'palestrante_idPalestrante']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInscreve()
    {
        return $this->hasMany(Inscreve::className(), ['evento_idevento' => 'idevento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioIdusuarios()
    {
        return $this->hasMany(User::className(), ['idusuario' => 'usuario_idusuario'])->viaTable('inscreve', 
            ['evento_idevento' => 'idevento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemProgramacaos()
    {
        return $this->hasMany(ItemProgramacao::className(), ['evento_idevento' => 'idevento']);
    }
    public function getItemProgramacao()
    {
        return $this->hasMany(ItemProgramacao::className(), ['evento_idevento' => 'idevento']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacotes()
    {
        return $this->hasMany(Pacote::className(), ['evento_idevento' => 'idevento']);
    }

    public function getTipo()
    {
        return $this->hasOne(Tipo::className(), ['idtipo' => 'tipo_idtipo']);
    }


    public function getLocal()
    {
        return $this->hasOne(Local::className(), ['idlocal' => 'local_idlocal']);
    }


    /*Função para geração de nome e Salvamento da imagem na pasta "Web/uploads/" retorna o nome para ser inserido no banco */
    public function upload($imageFile,$diretorio)
    {
        if ($imageFile != null && ($imageFile->extension == 'png' || $imageFile->extension == 'jpg' || $imageFile->extension == 'jpeg')) {
            $imageName = date('dmYhms');
            if($imageFile->saveAs($diretorio . $imageName . '.' . $imageFile->extension))
                return $imageName.".".$imageFile->extension;
        }
        
        return null;
    }

    /*Verifica se o usuário autenticado possui permissão de responsável ou de coordenador ajudante*/
    public function canAccess(){
        return !Yii::$app->user->isGuest && date("Y-m-d", strtotime($this->dataFim)) >= date('Y-m-d') && (Yii::$app->user->identity->idusuario == $this->responsavel || 
            CoordenadorHasEvento::find()->where(['usuario_idusuario' => Yii::$app->user->identity->idusuario])->andWhere(['evento_idevento' => $this->idevento])->count()) ? true : false;
    }
    
    /*Verifica se o usuário autenticado possui permissão de responsável*/
    public function canAccessResponsible(){
        return !Yii::$app->user->isGuest && date("Y-m-d", strtotime($this->dataFim)) >= date('Y-m-d') && Yii::$app->user->identity->idusuario == $this->responsavel ? true : false;
    }


    /*Funções para formatação dos Atributos de maneira correta*/
    public function getDataIni(){
        return date("d-M-Y", strtotime($this->dataIni));
    }

    public function getHoraIni(){
        return date("H:i", strtotime($this->horaIni));    
    }

    public function getHoraFim(){
        return date("H:i", strtotime($this->horaFim));    
    }
    
    public function getDataFim(){
        return date("d-M-Y", strtotime($this->dataFim));
    }

    public function getCargaHoraria(){
        return $this->cargaHoraria." h";
    }
    /*****************************************************/

//usado para relatórios, não apagar !!!
    public function getInscritosEventos($datainicial, $datafinal){

        if ($datainicial != NULL && $datafinal != NULL){
            
            $datainicial = (date("Y-m-d", strtotime($datainicial)));
            $datafinal = (date("Y-m-d", strtotime($datafinal)));
            
            $where = '((dataInscricao >="'.$datainicial.'" AND dataInscricao <= "'.$datafinal.'"))';
        }
        else if ($datainicial == NULL && $datafinal == NULL){
            
            $where = '';            
        }
        else if ($datainicial != NULL){
            
            $datainicial = (date("Y-m-d", strtotime($datainicial)));

            $where = '(dataInscricao >="'.$datainicial.'")';            
        }
        else{
            
            $datafinal = (date("Y-m-d", strtotime($datafinal)));
            $where = '(dataInscricao <="'.$datafinal.'")';            
        }


         $model = Evento:: find()->select(['idevento','sigla','COUNT(inscreve.evento_idevento) AS qtd_evento'])
        ->leftJoin('inscreve', 'inscreve.evento_idevento = evento.idevento')
        ->groupBy('idevento')
        ->where($where)
        ->andWhere('evento.responsavel = '.Yii::$app->user->identity->idusuario)
        ->orderBy('qtd_evento DESC')
        ->all();

        return $model;
    }

    public function getInscritosEventosPacotes($iditemProgramacao){

        $model = Evento:: find()->select(['nome'])
        ->innerJoin('inscreve', 'inscreve.evento_idevento = evento.idevento')
        ->innerJoin('itemProgramacao_has_pacote',
                'itemProgramacao_has_pacote.pacote_idpacote = inscreve.pacote_idpacote')
        ->innerJoin('user','user.idusuario = inscreve.usuario_idusuario')
        ->where('itemProgramacao_has_pacote.itemProgramacao_idItemProgramacao ='.$iditemProgramacao)
        ->orderBy('nome')
        ->all();

        return $model;
    }

    public function getInscritosEventoGeral(){
        $qte = Inscreve::find()->where(['evento_idevento' => $this->idevento])->count();
        return $qte;
    }
}
