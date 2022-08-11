<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use kartik\helpers\Html;
use Exception;

/**
 * This is the model class for table "person".
 *
 * @property int $Id
 * @property string $FirstName
 * @property string|null $SecondName
 * @property string|null $ThirdName
 * @property string $LastName
 * @property string|null $SecondLastName
 * @property string|null $MarriedName
 * @property string $Code
 * @property int $IdGenderType
 * @property int $IdState
 *
 * @property Employee $employee
 * @property Type $genderType
 * @property State $state
 * @property Personaldocument[] $personaldocuments
 * @property Student $student
 * @property Visitor $visitor
 */
class Person extends CustomActiveRecord
{
    public $DocumentNumber = null;
    public $IdDocumentType = null;
    public $completeName = null;
    private $documents = [];
    public $profiles = null;
    const CODE_LENGTH = 7;
    public $activeemployeee = 0;
    public $activestudent = 0;
    public $activevisitor = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'person';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['FirstName', 'LastName', 'IdGenderType', 'IdState'], 'required'],
            [['IdGenderType', 'IdState'], 'integer'],
            [['FirstName', 'SecondName', 'ThirdName', 'LastName', 'SecondLastName', 'MarriedName', 'Code'], 'string', 'max' => 25],
            [['Code'], 'unique'],
            [['IdGenderType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdGenderType' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'FirstName' => 'Primer Nombre',
            'SecondName' => 'Segundo Nombre',
            'ThirdName' => 'Tercer Nombre',
            'LastName' => 'Primer Apellido',
            'SecondLastName' => 'Segundo Apellido',
            'MarriedName' => 'Apellido Casada',
            'Code' => 'Código',
            'IdGenderType' => 'Sexo',
            'IdState' => 'Estado',
            'documents' => 'Documentos',
            'completeName' => 'Nombre Completo',
            'IdDocumentType' => 'Tipo de Documento',
            'DocumentNumber' => 'Número Documento',
            'profiles' => 'Perfiles',
            'activeteemployeee' => 'Empleado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['IdPerson' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGenderType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdGenderType']);
    }

    public function getGenderTypes(){
        $types = Type::find()
            ->select([Type::tableName().'.Id', Type::tableName().'.Name'])
            ->joinWith('state b', false)
            ->where([
                Type::tableName().'.KeyWord' => 'Gender',
                'b.Code' => Type::STATUS_ACTIVE,
            ])
            ->asArray()
            ->all();
        return ArrayHelper::map($types, 'Id','Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }

    public function getStates(){
        return ArrayHelper::map(State::find()->select(['Id','Name'])->where([
            'KeyWord' => StringHelper::basename(self::class),
        ])->asArray()->all(), 'Id','Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonaldocuments()
    {
        return $this->hasMany(Personaldocument::class, ['IdPerson' => 'Id']);
    }
    
    public function getDocuments(){
        $list = '';
        foreach ($this->personaldocuments as $doc){
            $list .= "<div class='row'>"
            . "<div class='col-12'>"
            .($doc->IdDocumentType ? $doc->documentType->Name : '')
            .": "
            . $doc->DocumentNumber
            ."</div>
            </div>";
        }
        return $list;
    }

    public function getProfiles(){
        try {
            $list = '';
            $list .= !empty($this->employee) ? Html::tag('div',Html::tag('div','Empleado',['class' => 'col-12']),['class' => 'row']) : '';
            $list .= !empty($this->student) ? Html::tag('div',Html::tag('div','Estudiante',['class' => 'col-12']),['class' => 'row']) : '';
            $list .= !empty($this->visitor) ? Html::tag('div',Html::tag('div','Visitante',['class' => 'col-12']),['class' => 'row']) : '';
            return $list;
        } catch (Exception $exc){
            throw $exc;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::class, ['IdPerson' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVisitor()
    {
        return $this->hasOne(Visitor::class, ['IdPerson' => 'Id']);
    }

    public function getTempDocumentField(){
        try {
            $response = null;
            if($this->isNewRecord){
                $document = new Personaldocument();
                $feedback = Html::tag('div',null, ['class' => 'invalid-feedback']);
                $labelSelect = Html::label($document->getAttributeLabel('IdDocumentType'),null, ['class' => 'control-label']);
                $select = Html::dropDownList(StringHelper::basename(self::class).'['.StringHelper::basename(Personaldocument::class).'][IdDocumentType][]', null, $document->getDocumentTypes(),['class' => 'form-control']);
                $groupSelect = Html::tag('div', $labelSelect.$select.$feedback, ['class' => 'form-group highlight-addon required']);
                $divSelect = Html::tag('div', $groupSelect, ['class' => 'col-5']);
                $labelInput = Html::label($document->getAttributeLabel('DocumentNumber'),null, ['class' => 'control-label']);
                $input = Html::textInput(StringHelper::basename(self::class).'['.StringHelper::basename(Personaldocument::class).'][DocumentNumber][]',null, ['class' => 'form-control']);
                $groupInput = Html::tag('div', $labelInput.$input.$feedback, ['class' => 'form-group highlight-addon required']);
                $divInput = Html::tag('div', $groupInput, ['class' => 'col-6']);
                $labelButton = Html::label('&nbsp', null,['style' => 'display:block']);
                $button = Html::button('<i class="fas fa-times fa-1x"></i>', ['type'=> 'button','class' => 'btn btn-default btn-remove-field', 'title' => 'Eliminar Campo', 'onClick' => 'javascript:removeField(this);']);
                $divButton = Html::tag('div', $labelButton.$button, ['class' => 'col-1 condition']);
                return Html::tag('div', $divSelect. $divInput.$divButton, ['class' => 'row row-field']);
            }
        } catch (\Throwable $th){
            throw $th;
        }
    }
    
    private function _generateCode(){
        try {
            $code = ((int) $this->_getLastCode()) +1 ;
            $this->Code = str_pad($code, self::CODE_LENGTH, '0', STR_PAD_LEFT);
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }
    
    private function _getLastCode(){
        try {
            return self::find()->max('Code');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function afterValidate() {
        return parent::afterValidate();
    }
    
    public function afterFind() {
        try {
            $this->completeName = $this->FirstName.(!empty($this->SecondName) ? ' '.$this->SecondName:'')
                .(!empty($this->ThirdName) ? ' '.$this->ThirdName:'')
                .' '.$this->LastName.(!empty($this->SecondLastName) ? ' '.$this->SecondLastName:'')
                .(!empty($this->MarriedName) ? ' '.$this->MarriedName:'');
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterFind();
    }
    
    public function beforeSave($insert) {
        if($this->isNewRecord){
            $this->_generateCode();
        }
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes) {
        try {
            !empty($this->documents) ? $this->_saveDocuments() : null;
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    public function setDocuments($documents = []){
        $this->documents = $documents;
    }
    
    public function _saveDocuments(){
        try {
            $this->refresh();
            $i = 0;
            foreach ($this->documents['DocumentNumber'] as $doc){
                $document = new Personaldocument();
                $document->IdPerson = $this->Id;
                $document->DocumentNumber= $doc;
                $document->IdDocumentType = $this->documents['IdDocumentType'][$i];
                $document->save();
                $i++;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
