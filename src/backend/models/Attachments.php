<?php

namespace backend\models;

use Yii;
use common\models\User;
use common\models\Catalogdetails;
use common\models\Catalogversions;
use common\models\Catalogdetailvalues;
use common\models\Catalogs;
use common\models\Type;
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
 * @property Catalogdetails $catalogDetail
 */
class Attachments extends \yii\db\ActiveRecord
{
    private $transaction = NULL;
    
    const CATALOG_EXTENSION_CODE = 'EXT';
    const UNKNOWN_MIMETYPE = 'unknown';
    const OVERWRITE_ENABLED = true;
    const OVERWRITE_DISABLED = false;
    
    public $fileattachment = NULL;
    private $file = null;
    private $type = NULL;
    const FILE_PATH = '@backend/web/attachments';
    const PATH_ATTACHMENTS = 'attachments';
    public $path = NULL;
    
    public $renameFile = FALSE;
    public $newName = NULL;
    public $overwriteFile = FALSE;
    public $oldFileName = FALSE;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attachments';
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
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdUser' => 'Id']],
            [['IdCatalogDetail'], 'exist', 'skipOnError' => true, 'targetClass' => Catalogdetails::className(), 'targetAttribute' => ['IdCatalogDetail' => 'Id']],
            #[['attachments'], 'file', 'skipOnEmpty' => false],
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
        return $this->hasOne(User::className(), ['Id' => 'IdUser']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogDetail(){
        return $this->hasOne(Catalogdetails::class, ['Id' => 'IdCatalogDetail']);
    }
    
    public function getCatalogdetails(){
        try {
            $droptions = $catalogdetail = Catalogdetails::find()
                    ->joinWith('state a')
                    ->innerJoin('catalogversions b', 'catalogdetails.IdCatalogVersion = b.Id')
                    ->innerJoin('state c', 'b.IdState = c.Id')
                    ->innerJoin('catalogs d', 'b.IdCatalog = d.Id')
                    ->innerJoin('state e', 'd.IdState = e.Id')
                    ->where([
                            'a.KeyWord' => StringHelper::basename(Catalogdetails::class)
                            ,'a.Code' => Catalogdetails::STATE_ACTIVE
                            ,'c.KeyWord' => StringHelper::basename(Catalogversions::class)
                            ,'c.Code' => Catalogversions::STATE_ACTIVE
                            , 'd.KeyWord' => StringHelper::basename(self::class)
                            , 'd.Code' => self::CATALOG_EXTENSION_CODE
                            , 'e.KeyWord' => StringHelper::basename(Catalogs::class)
                            , 'e.Code' => Catalogs::STATUS_ACTIVE
                            ])
                    ->all();
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function afterFind() {
        $_path = self::PATH_ATTACHMENTS."/".strtolower($this->KeyWord)."/".strtolower($this->AttributeValue)."/".$this->FileName;
        $this->path = Yii::$app->urlManager->createAbsoluteUrl($_path);
        return parent::afterFind();
    }
    
    public function beforeValidate() {
        try {
            $this->IdUser = Yii::$app->user->getIdentity()->getId();
            $this->oldFileName = $this->FileName;
            if($this->fileattachment){
                $this->file = reset($this->fileattachment);
                $this->type = $this->file->type;
                $this->FileName = $this->file->name;
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
            $fileName = $this->file->name;
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
            $catalogdetailvalue = Catalogdetailvalues::find()
                    ->joinWith('catalogDetail b')
                    ->innerJoin('state c', 'c.Id = b.IdState')
                    ->innerJoin('catalogversions d', 'd.Id = b.IdCatalogVersion')
                    ->innerJoin('state e', 'e.Id = d.IdState')
                    ->innerJoin('catalogs f', 'f.Id = d.IdCatalog')
                    ->innerJoin('state g', 'g.Id = f.IdState')
                    ->where([
                            'catalogdetailvalues.Value' => $this->FileExtension
                            , 'c.KeyWord'=> StringHelper::basename(Catalogdetails::class)
                            , 'c.Code'=> Catalogdetails::STATE_ACTIVE
                            , 'e.KeyWord' => StringHelper::basename(Catalogversions::class)
                            , 'e.Code' => Catalogversions::STATE_ACTIVE
                            , 'g.KeyWord' => StringHelper::basename(Catalogs::class)
                            , 'g.Code' => Catalogs::STATUS_ACTIVE
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
            $catalogdetail = Catalogdetails::find()
                    ->joinWith('state a')
                    ->innerJoin('catalogversions b', 'catalogdetails.IdCatalogVersion = b.Id')
                    ->innerJoin('state c', 'b.IdState = c.Id')
                    ->innerJoin('catalogs d', 'b.IdCatalog = d.Id')
                    ->innerJoin('state e', 'd.IdState = e.Id')
                    ->where([
                            'a.KeyWord' => StringHelper::basename(Catalogdetails::class)
                            ,'a.Code' => Catalogdetails::STATE_ACTIVE
                            ,'c.KeyWord' => StringHelper::basename(Catalogversions::class)
                            ,'c.Code' => Catalogversions::STATE_ACTIVE
                            , 'd.KeyWord' => StringHelper::basename(self::class)
                            , 'd.Code' => self::CATALOG_EXTENSION_CODE
                            , 'e.KeyWord' => StringHelper::basename(Catalogs::class)
                            , 'e.Code' => Catalogs::STATUS_ACTIVE
                            , 'catalogdetails.KeyWord' => $this->type
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
            $this->file->saveAs($path, TRUE);
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
}
