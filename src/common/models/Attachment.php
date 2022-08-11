<?php

namespace common\models;

use hail812\adminlte\widgets\FlashAlert;
use Yii;
use common\models\User;
use common\models\Catalogdetail;
use common\models\Catalogversion;
use common\models\Catalogdetailvalue;
use common\models\Catalog;
use common\models\CustomActiveRecord;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use Exception;

/**
 * This is the model class for table "attachments".
 *
 * @property int $Id
 * @property string $KeyWord
 * @property string $AttributeName
 * @property string $AttributeValue
 * @property string $FileName
 * @property int $IdCatalogDetail
 * @property string $FileExtension
 * @property string $Description
 * @property string $CreationDate
 * @property int $IdUser
 *
 * @property User $user
 * @property Catalogdetail $catalogDetail
 */
class Attachment extends CustomActiveRecord
{
    private $transaction = NULL;
    public $disabled = false;

    const CATALOG_EXTENSION_CODE = 'EXT';
    const UNKNOWN_MIMETYPE = 'unknown';
    const OVERWRITE_ENABLED = true;
    const OVERWRITE_DISABLED = false;

    public $fileattachment = NULL;
    private $type = NULL;
    const FILE_PATH = '@backend/web/attachments';
    const PATH_ATTACHMENTS = 'attachments';
    public $filePath = NULL;
    public $basePath = NULL;
    public $path = NULL;
    public $route = null;

    public $renameFile = FALSE;
    public $newName = NULL;
    public $overwriteFile = FALSE;
    public $oldFileName = FALSE;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attachment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['KeyWord', 'AttributeName', 'AttributeValue','FileName','IdUser','IdCatalogDetail','FileExtension'], 'required'],
            [['IdUser','IdCatalogDetail'], 'integer'],
            [['KeyWord'], 'string','max'=> 100],
            [['Description'], 'string'],
            [['CreationDate'], 'safe'],
            [['AttributeName', 'AttributeValue'], 'string', 'max' => 50],
            [['FileName'], 'string', 'max' => 100],
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['IdUser' => 'Id']],
            [['IdCatalogDetail'], 'exist', 'skipOnError' => true, 'targetClass' => Catalogdetail::class, 'targetAttribute' => ['IdCatalogDetail' => 'Id']],
            //[['attachments'], 'file', 'skipOnEmpty' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'KeyWord' => 'Llave',
            'AttributeName' => 'Nombre Atributo',
            'AttributeValue' => 'Valor Atributo',
            'FileName' => 'Nombre',
            'FileExtension' => 'Extensión de Archivo',
            'Description' => 'Descripción',
            'CreationDate' => 'Fecha Creación',
            'IdUser' => 'Usuario',
            'IdCatalogDetail' => 'Tipo',
            'fileattachment' => 'Archivo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['Id' => 'IdUser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogDetail(){
        return $this->hasOne(Catalogdetail::class, ['Id' => 'IdCatalogDetail']);
    }

    public function getCatalogdetails(){
        try {
            $droptions = Catalogdetail::find()
                ->joinWith(State::tableName().' a')
                ->innerJoin(Catalogdetail::tableName().' b', Catalogdetail::tableName().'.IdCatalogVersion = b.Id')
                ->innerJoin(State::tableName().' c', 'b.IdState = c.Id')
                ->innerJoin(Catalog::tableName().' d', 'b.IdCatalog = d.Id')
                ->innerJoin(State::tableName().' e', 'd.IdState = e.Id')
                ->where([
                    'a.KeyWord' => StringHelper::basename(Catalogdetail::class)
                    ,'a.Code' => Catalogdetail::STATE_ACTIVE
                    ,'c.KeyWord' => StringHelper::basename(Catalogversion::class)
                    ,'c.Code' => Catalogversion::STATE_ACTIVE
                    , 'd.KeyWord' => StringHelper::basename(self::class)
                    , 'd.Code' => self::CATALOG_EXTENSION_CODE
                    , 'e.KeyWord' => StringHelper::basename(Catalog::class)
                    , 'e.Code' => Catalog::STATUS_ACTIVE
                ])
                ->all();
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function afterFind() {
        $this->_setPath();
        $this->path = Yii::$app->urlManager->createAbsoluteUrl($this->filePath);
        return parent::afterFind();
    }

    public function beforeValidate() {
        try {
            $this->IdUser = Yii::$app->user->getIdentity()->getId();
            $this->oldFileName = $this->FileName;
            if($this->fileattachment){
                $this->type = $this->fileattachment->type;
                $this->FileName = $this->fileattachment->name;
                $this->setFileExtension();
                $this->getMimeType();
                $this->_setFileName();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::beforeValidate();
    }

    private function _setFileName(){
        try {
            if($this->renameFile && !empty($this->newName)){
                $this->FileName = $this->newName;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function beforeSave($insert) {
        $this->transaction = Yii::$app->db->beginTransaction();
        if($this->overwriteFile){
            $this->_deleteOldFile();
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes) {
        try {
            $this->saveFile();
            $this->transaction->commit();
        } catch (Exception $ex) {
            $this->transaction->rollBack();
            throw $ex;
        }
        return parent::afterSave($insert, $changedAttributes);
    }


    private function setFileExtension(){
        try {
            $fileName = $this->fileattachment->name;
            $file = explode('.', $fileName);
            $this->FileExtension = $file[count($file)-1];
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function beforeDelete() {
        return parent::beforeDelete();
    }

    public function afterDelete() {
        try {
            $_path = self::FILE_PATH."/".strtolower($this->KeyWord)."/".strtolower($this->AttributeValue)."/".$this->FileName;
            $path =  \Yii::getAlias($_path);
            if(file_exists($path)){
                unlink($path);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterDelete();
    }

    private function getMimeType(){
        try {
            $this->getByFileExtension();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function getByFileExtension(){
        try {
            $catalogdetailvalue = Catalogdetailvalue::find()
                ->joinWith('catalogDetail b')
                ->innerJoin(State::tableName().' c', 'c.Id = b.IdState')
                ->innerJoin( Catalogversion::tableName().' d', 'd.Id = b.IdCatalogVersion')
                ->innerJoin(State::tableName().' e', 'e.Id = d.IdState')
                ->innerJoin(Catalog::tableName().' f', 'f.Id = d.IdCatalog')
                ->innerJoin(State::tableName().' g', 'g.Id = f.IdState')
                ->where([
                    Catalogdetailvalue::tableName().'.Value' => $this->FileExtension
                    , 'c.KeyWord'=> StringHelper::basename(Catalogdetail::class)
                    , 'c.Code'=> Catalogdetail::STATE_ACTIVE
                    , 'e.KeyWord' => StringHelper::basename(Catalogversion::class)
                    , 'e.Code' => Catalogversion::STATE_ACTIVE
                    , 'g.KeyWord' => StringHelper::basename(Catalog::class)
                    , 'g.Code' => Catalog::STATUS_ACTIVE
                ])->one();
            if(!empty($catalogdetailvalue)){
                $this->IdCatalogDetail = $catalogdetailvalue->IdCatalogDetail;
            } else {
                $this->getByMimeType();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function getByMimeType(){
        try {
            $catalogdetail = Catalogdetail::find()
                ->joinWith(State::tableName().' a')
                ->innerJoin(Catalogversion::tableName().' b', Catalogdetail::tableName().'.IdCatalogVersion = b.Id')
                ->innerJoin(State::tableName().' c', 'b.IdState = c.Id')
                ->innerJoin(Catalog::tableName().' d', 'b.IdCatalog = d.Id')
                ->innerJoin(State::tableName().' e', 'd.IdState = e.Id')
                ->where([
                    'a.KeyWord' => StringHelper::basename(Catalogdetail::class)
                    , 'a.Code' => Catalogdetail::STATE_ACTIVE
                    , 'c.KeyWord' => StringHelper::basename(Catalogversion::class)
                    , 'c.Code' => Catalogversion::STATE_ACTIVE
                    , 'd.KeyWord' => StringHelper::basename(self::class)
                    , 'd.Code' => self::CATALOG_EXTENSION_CODE
                    , 'e.KeyWord' => StringHelper::basename(Catalog::class)
                    , 'e.Code' => Catalog::STATUS_ACTIVE
                    , Catalogdetail::tableName().'.KeyWord' => $this->type
                ])
                ->one();
            if(!empty($catalogdetail)){
                $this->IdCatalogDetail = $catalogdetail->Id;
            } elseif($this->type != self::UNKNOWN_MIMETYPE) {
                $this->type = self::UNKNOWN_MIMETYPE;
                $this->getMimeType();
            } else {
                $this->addError('IdCatalogDetail', 'Mime Type no encontrado');
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function saveFile(){
        try {
            if($this->fileattachment){
                $_path = self::FILE_PATH."/".strtolower($this->KeyWord)."/".strtolower($this->AttributeValue);
                $path =  \Yii::getAlias($_path);
                if(!file_exists($path)){
                    mkdir($path, 0777, TRUE);
                }
                $fileName = $_path."/".$this->FileName;
                $path =  \Yii::getAlias($fileName);
                if(file_exists($path)){
                    unlink($path);
                }
                $this->fileattachment->saveAs($path, TRUE);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function _deleteOldFile(){
        try {
            $model = self::findOne(['KeyWord' => $this->KeyWord, 'AttributeName' => $this->AttributeName, 'AttributeValue' => $this->AttributeValue]);
            if($model){
                $model->delete();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function saveFiles(){
        try {
            foreach ($this->fileattachment as $file){
                $attch = new Attachment();
                $attch->KeyWord = $this->KeyWord;
                $attch->AttributeName = $this->AttributeName;
                $attch->AttributeValue = $this->AttributeValue;
                $attch->fileattachment = $file;
                if(!$attch->save()){
                    $this->addErrors($attch->errors);
                    $message = \Yii::$app->customFunctions->getErrors($attch->errors);
                    \Yii::$app->customFunctions->setFlashMessage('ERROR', $message, 'error');
                }
            }
            return !$this->hasErrors();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function _setPath(){
        $this->filePath = self::PATH_ATTACHMENTS."/".strtolower($this->KeyWord)
            ."/".strtolower($this->AttributeValue)."/"
            .($this->FileName ?: '');
        $this->basePath = self::FILE_PATH."/".strtolower($this->KeyWord)."/".strtolower($this->AttributeValue)."/";
        $this->route = $this->basePath.$this->FileName;
    }

    public function setPath(){
        try {
            if(empty($this->KeyWord)){
                throw new Exception(
                    Yii::t('app', '{attribute} is required', [
                        'attribute' => $this->getAttributeLabel('KeyWord'),
                    ])
                );
            }
            if(empty($this->AttributeValue)){
                throw new Exception(
                    Yii::t('app', '{attribute} is required', [
                        'attribute' => $this->getAttributeLabel('AttributeValue'),
                    ])
                );
            }
            $this->_setPath();
        } catch (Exception $exception){
            throw $exception;
        }
    }

    public function copy($newPath = null): bool {
        try {
            $currentPath =  \Yii::getAlias($this->basePath.$this->FileName);
            if(file_exists($currentPath) && !empty($newPath)){
                $basePath =  \Yii::getAlias($newPath);
                if(!file_exists($basePath)){
                    mkdir($basePath, 0777, true);
                }
                $fileName = $newPath."/".$this->FileName;
                $path =  \Yii::getAlias($fileName);
                if(file_exists($path)){
                    unlink($path);
                }
                return copy($currentPath, $path);
            }
            return false;
        } catch (Exception $exception){
            throw $exception;
        }
    }
}
