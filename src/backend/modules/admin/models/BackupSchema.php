<?php

namespace backend\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\helpers\FileHelper;
use yii\data\ArrayDataProvider;
use Exception;

/**
 * Description of Backup
 *
 * @author Eliel Avelar <ElielAbisai.AvelarJaimes@muehlbauer.de>
 */
class BackupSchema extends Model {
    //put your code here
    public $complete;
    public $structure;
    public $data;
    public $includeKeys;
    public $includeDBName;
    public $addcheck;
    public $enableZip;
    public $dropTable;
    
    public $file_script;
    public $file_name;
    public $file;
    private $_file = [];
    public $prefix_temp_file = 'db_backup_';
    private $ext_file = ".sql";
    private $fs;
    
    private $_supported_ext = ['.sql','.zip'];
    
    public $tables;
    private $_tables;
    public $columns;
    private $_columns;
    public $tableName;
    private $tableSchema = [];
    private $_table;
    private $_pk;
    private $_script = "";
    private $_data;
    private $_dbName = NULL;
    private $_dsn = NULL;
    private $_dsnParams = [];
    
    public $view = FALSE;
    public $create = FALSE;
    public $update = FALSE;
    public $upload = FALSE;
    public $restore = FALSE;
    public $delete = FALSE;
    public $download = FALSE;
    
    private $params;
    private $path;
    private $controller;
    private $module;
    private $urlPath;
    private $max_row_insert = 2000;
    
    private $icon = [
        'sql'=>'glyphicon glyphicon-save-file',
        'zip'=>'glyphicon glyphicon-floppy-save',
        'file'=>'glyphicon glyphicon-file',
    ];
    
    public $criteria = [];

    private $fileList = [];
    
    public $id;
    public $name;
    public $size;
    public $created_time;
    public $modified_time;
    public $uploadFile;


    public function getModel(){
        return 'backup';
    }
    
    private function setModule(){
        $this->module = Yii::$app->controller->module;
    }
    
    public function getModule(){
        return $this->module;
    }
    
    public function getParams(){
        return $this->params;
    }
    
    function __construct($config = array()) {
        
        $this->setModule();
        $this->params = $this->module->params;
        $this->path = Yii::$app->basePath.$this->params['backupPath']."/";
        $this->urlPath = $this->params['backupUrl']."/";
        Yii::setAlias('@backup', $this->urlPath."/");
        
        $this->setPath();
        $this->setExtensionFile();
        
        $this->complete = TRUE;
        $this->structure = TRUE;
        $this->data = TRUE;
        $this->includeKeys = TRUE;
        $this->addcheck = TRUE;
        $this->enableZip = TRUE;
        $this->dropTable = FALSE;
        $this->_dsn = \Yii::$app->db->dsn;
        
        $params = str_replace("mysql:", '', $this->_dsn);
        $paramsData= explode(";", $params);
        foreach ($paramsData as $p){
            $param = explode("=", $p);
            $this->_dsnParams[$param[0]] = $param[1];
        }
        $this->_loadAccess();
        $this->getTables();
        return parent::__construct($config);
    }
    
    private function _loadAccess(){
        $this->create = Yii::$app->user->can($this->getModel()."Create");
        $this->update = Yii::$app->user->can($this->getModel()."Update");
        $this->delete = Yii::$app->user->can($this->getModel()."Delete");
        $this->restore = Yii::$app->user->can($this->getModel()."Restore");
        $this->download = Yii::$app->user->can($this->getModel()."Download");
        $this->upload = Yii::$app->user->can($this->getModel()."Upload");
        $this->view = Yii::$app->user->can($this->getModel()."View");
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['complete','structure','data','includeKeys','addcheck','enableZip','dropTable','includeDBName'],'integer'],
            [['tableName','name'],'string'],
        ];
    }
    
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tableName' => 'Nombre de Tabla',
            'complete'=>'Respaldo Completo',
            'structure'=>'Estructura de Tablas',
            'includeKeys'=>'Incluir Llaves',
            'addcheck' => 'Incluir Verificaciones',
            'enableZip' => 'Comprimir Respaldo (.zip)',
            'data'=>'Datos',
            'id'=>'ID',
            'name'=>'Nombre de Archivo',
            'size'=>'Tamaño (Kb)',
            'created_time'=>'Fecha de Creación',
            'modified_time'=>'Fecha de Modificación',
            'uploadFile'=>'Archivo',
            'includeDBName'=>'Incluir Nombre DB',
        ];
    }
    
    public function getUrl(){
        return $this->urlPath;
    }
    
    public function getPath(){
        return $this->path;
    }
    
    public function setPath($path = NULL){
        try {
            $this->path = $path != NULL ? $path:$this->path;
            if (! file_exists ( $this->path )) {
                FileHelper::createDirectory($this->path, 0775, TRUE);
            }
        } catch (Exception $ex) {
            var_dump($ex);
        }
        
    }
    
    public function setExtensionFile($ext = NULL){
        try {
            $this->ext_file = ($ext != NULL && in_array($ext, $this->_supported_ext)) ? $ext:'.sql';
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getExtensionFile(){
        return $this->ext_file;
    }


    private function _setFileName(){
        try {
            $this->file = $this->prefix_temp_file.date('Y-m-d.H.i.s');
            $this->file_name = $this->path.$this->file;
            $this->file_script = $this->file_name.$this->ext_file;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getFileName(){
        return $this->file_script;
    }

    public function getTables(){
        try {
            $sql = "SHOW TABLES";
            $cmd = \Yii::$app->db->createCommand($sql);
            $tables = $cmd->queryColumn();
            $this->tables = [];
            foreach ($tables as $key => $value){
                $this->tables = array_merge($this->tables, [$value=>$value]);
            }
            $this->_tables = $this->tables;
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function setTables(){
        try {
            $this->tableName = empty($this->tableName) ? NULL:$this->tableName;
            switch(gettype($this->tableName)){
                case 'array':
                    $this->tables = $this->tableName;
                    break;
                case 'string':
                    $this->tables = [$this->tableName];
                    break;
                default :
                    $this->getTables();
                    break;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

        public function setTableName($tableName = NULL){
        $this->tableName = $tableName;
    }
    
    public function getTableName(){
        return $this->tableName;
    }
    
    public function getTableSchema(){
        return $this->tableSchema;
    }
    
    public function getColumns(){
        try {
            $this->setColums();
            return $this->columns;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function setColums(){
        try {
            switch(gettype($this->tableName)){
                case 'array':
                    $this->_iterateTables();
                    break;
                case 'string':
                    $this->_table = $this->tableName;
                    $this->_getColumns();
                    break;
                default :
                    $this->getTables();
                    $this->tableName = $this->tables;
                    $this->_iterateTables();
                    break;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _iterateTables(){
        try {
            $_columns = [];
            $this->tables = $this->tableName;
            foreach ($this->tableName as $key => $table){
                $this->_table = $table;
                $this->_getColumns();
                $_columns[$this->_table] = $this->columns;
            }
            $this->columns = $_columns;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getColumns(){
        try {
            $sql =  "SHOW COLUMNS FROM ".$this->_table;
            $cmd = Yii::$app->db->createCommand($sql);
            $this->columns = $cmd->queryColumn();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getHtmlBackupList($criteria = []){
        try {
            $_list = "<tr><td colspan='5'>No se encontraron archivos</td>";
            $this->setFileList($criteria);
            $_fileList = "";
            foreach ($this->fileList as $key => $value) {
                
            }
            $list = !empty($this->fileList) ? $_fileList : $_list;
            return $list;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getList($criteria = []){
        try {
            $this->setFileList($criteria);
            $data = $this->getDataFileList();
            $dataProvider = new ArrayDataProvider([
                'allModels'=> array_reverse($data),
                'modelClass'=> self::className(),
                'sort'=> [
                    'attributes'=>[
                        'name'=> SORT_DESC,
                    ],
                ],
            ]);
            return $dataProvider;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function getDataFileList(){
        try {
            $data = [];
            foreach ($this->fileList as $key => $value) {
                $colums = [
                    'id'=> $key,
                    'name'=> StringHelper::basename($value),
                    'url'=>  $this->getUrl().StringHelper::basename($value),
                    'size'=>  round(filesize($this->path.$value)/1000,2)." Kb",
                    'created_time' => Yii::$app->formatter->asDatetime(filectime($this->path.$value), 'php:d-m-Y H:i:s'),
                    'modified_time' => Yii::$app->formatter->asDatetime(filemtime($this->path.$value), 'php:d-m-Y H:i:s'),
                    'delete'=>  $this->delete,
                    'download'=>  $this->download,
                    'view'=>  $this->view,
                ];
                $data[] = $colums;
            }
            return $data;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function setFileList($filter = []){
        try {
            $criteria = !empty($filter) ? $filter:['*.sql','*.zip'];
            foreach ($criteria as $cr){
                $this->fileList = array_merge($this->fileList, $this->getFileList($cr));
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    private function getFileList($ext = "*.sql"){
        try {
            $file_list = glob($this->path.$ext);
            $list = $file_list ? array_map('basename', $file_list):[];
            sort($list);
            return $list;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getFile(){
        try {
            $value = $this->getPath().$this->name;#glob($this->getPath().$this->name);
            if(file_exists($value)){
                $ext = explode(".", $value);
                $this->ext_file = count($ext) > 1 ? $ext[count($ext)-1]:NULL;
                $this->_file = [
                    'name'=> StringHelper::basename($value),
                    'url'=>  $this->getUrl().StringHelper::basename($value),
                    'size'=>  round(filesize($value)/1000,2)." Kb",
                    'created_time' =>Yii::$app->formatter->asDatetime(filectime($value), 'php:d-m-Y H:i:s'),
                    'modified_time' => Yii::$app->formatter->asDatetime(filemtime($value), 'php:d-m-Y H:i:s'),
                    'delete'=>  $this->delete,
                    'download'=>  $this->download,
                    'view'=>  $this->view,
                    'extension'=>  $this->ext_file,
                    'icon'=> $this->getIcon(),
                ];
            }
            return $this->_file;
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function getIcon(){
        try {
            $ico = NULL;
            switch ($this->ext_file) {
                case 'sql':
                    $ico = $this->icon[$this->ext_file];
                    break;
                case 'zip':
                    $ico = $this->icon[$this->ext_file];
                    break;
                default:
                    $ico = $this->icon['file'];
                    break;
            }
            return $ico;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _setExtension(){
        try {
            $ext = explode(".", $this->name);
            $this->ext_file = count($ext) > 1 ? $ext[count($ext)-1]:NULL;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function delete(){
        try {
            if(file_exists($this->getPath().$this->name)){
                return unlink($this->getPath().$this->name);
            } else {
                throw new Exception('Archivo no encontrado', 92999);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function backup(){
        try {
            $this->setTables();
            $this->_start();
            $this->_end();
            $this->tables = $this->_tables;
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _start(){
        try {
            $this->_setFileName();
            $this->_openFile();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _end(){
        try {
            fwrite ( $this->fs, '-- -------------------------------------------' . PHP_EOL );
            fwrite ( $this->fs, 'SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;' . PHP_EOL );
            fwrite ( $this->fs, 'SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;' . PHP_EOL );

            if ($this->addcheck) {
                fwrite ( $this->fs, 'COMMIT;' . PHP_EOL );
            }
            fwrite ( $this->fs, '-- -------------------------------------------' . PHP_EOL );
            $this->writeComment ( 'END BACKUP' );
            fclose ( $this->fs );
            $this->fs = null;

            if ($this->enableZip || $this->complete) {
                $this->_createZip();
                $this->file = $this->file.".zip";
            } else {
                $this->file = $this->file.".sql";
            }
            
        } catch (Exception $ex) {
            
        }
    }
    
    private function _openFile(){
        try {
            $this->fs = fopen($this->file_script, 'w+');
            if($this->fs != NULL){
                fwrite($this->fs, '-- -------------------------------------------' . PHP_EOL);
                $this->_addCheckFile();
                
                fwrite( $this->fs, 'SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;' . PHP_EOL );
		fwrite( $this->fs, 'SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;' . PHP_EOL );
		fwrite( $this->fs, '-- -------------------------------------------' . PHP_EOL );
                
                $this->_iterateBackupTables();
            } else {
                throw new Exception('No se pudo acceder al archivo '.$this->file_script, 92001);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _addCheckFile(){
        try {
            if($this->addcheck){
                fwrite ( $this->fs, 'SET AUTOCOMMIT=0;' . PHP_EOL );
                fwrite ( $this->fs, 'START TRANSACTION;' . PHP_EOL );
                fwrite ( $this->fs, 'SET SQL_QUOTE_SHOW_CREATE = 1;' . PHP_EOL );
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    
    private function _iterateBackupTables(){
        try {
            foreach ($this->tables as $this->_table){
                $this->_getColumnsType();
                if($this->structure || $this->complete){
                    $this->_createTable();
                }
                if($this->data || $this->complete){
                    $this->_createInsert();
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function _createTable(){
        try {
            //$create_query = "CREATE TABLE IF NOT EXISTS";
            $sql = 'SHOW CREATE TABLE ' . $this->_table;
            $cmd = Yii::$app->db->createCommand ( $sql );
            $table = $cmd->queryOne ();

            $create_query = $table['Create Table'] . ';';

            $create_query = preg_replace ( '/^CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $create_query );
            $create_query = preg_replace ( '/AUTO_INCREMENT\s*=\s*([0-9])+/', '', $create_query );
            if ($this->fs) {
                $comment = ($this->dropTable ? '':'-- ');
                $this->writeComment ( 'TABLE `' . addslashes ( $this->_table ) . '`' );
                $final = $comment.'DROP TABLE IF EXISTS `' . addslashes ( $this->_table ) . '`;' . PHP_EOL . $create_query . PHP_EOL . PHP_EOL;
                fwrite ( $this->fs, $final );
            } else {
                #$this->tables [$tableName] ['create'] = $create_query;
                return $create_query;
            }
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _createInsert(){
        try {
            $this->_getColumnsType();
            $this->_getPrimaryKeyField();
            if(!$this->includeKeys && !$this->complete){
                unset($this->columns[array_search($this->_pk, $this->columns)]);
            }
            $this->_iterateColumns();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _iterateColumns(){
        try {
            $this->_getValues();
            if(!empty($this->_data)){
                $this->writeComment('INSERT OF DATA FROM '.($this->includeDBName ? $this->_dsnParams['dbname'].".":"").$this->_table);
                $header = "INSERT INTO ".($this->includeDBName ? "`".$this->_dsnParams['dbname']."`.":"")."`".$this->_table."` (";
                
                $l = count($this->columns);
                $i = 0;
                foreach ($this->columns as $col) {
                    $header .= "`".$col."`";
                    $i++;
                    $header .= ($i < $l ? ',':")");
                }
                $header .= " \n VALUES ";
                $ld = count($this->_data);
                $this->_script = $header;
                $i = 0;
                $k = 0;
                $_i = 0;
                foreach ($this->_data as $row) {
                    $this->_script .= ($k == 0 && $_i > 0 ? $header:"");
                    $this->_script .= "(";
                    $j = 0;
                    $lj = count($row);
                    foreach ($row as $key => $value) {
                        $j++;
                        if(!empty($value)){
                            $this->_script .= "'".$value."'";
                        } else {
                            $field = $this->_columns[$this->_table][$key];
                            //$default = $field["Default"];
                            $this->_script .= $field["Null"] == 'NO' ? "''":"NULL";
                        }
                        
                        $this->_script .= ($j < $lj ? ',':"");
                    }
                    $i++;
                    $k++;
                    $this->_script .= ")".(($i < $ld && $k < $this->max_row_insert )? ',':"; \n")."\n";
                    if($k == $this->max_row_insert){
                        $k = 0;
                        $_i++;
                    }
                }
                fwrite($this->fs, $this->_script);
                $this->_script = "";
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getValues(){
        try {
            $query = new Query();
            $query->select($this->columns);
            $query->from($this->_table);
            if(isset($this->criteria[$this->_table])){
                $query->where($this->criteria[$this->_table]);
            }
            $query->orderBy([$this->_pk => SORT_ASC]);
            $this->_data = $query->all();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    
    private function _setFilterCriteria(){
        try {
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    private function _getColumnsType(){
        try {
            $this->_getColumns();
            $sql =  "SHOW COLUMNS FROM ".$this->_table;
            $cmd = Yii::$app->db->createCommand($sql);
            $columns = $cmd->queryAll();
            foreach ($columns as $col) {
                $this->_columns[$this->_table][$col['Field']] = $col;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    
    private function _getPrimaryKeyField(){
        try {
            $sql =  "SHOW KEYS FROM ".$this->_table." WHERE Key_name = 'PRIMARY'";
            $cmd = Yii::$app->db->createCommand($sql);
            $data = $cmd->queryAll();
            $this->_pk = $data[0]["Column_name"];
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function writeComment($string) {
        try {
            fwrite ( $this->fs, '-- -------------------------------------------' . PHP_EOL );
            fwrite ( $this->fs, '-- ' . $string . PHP_EOL );
            fwrite ( $this->fs, '-- -------------------------------------------' . PHP_EOL );
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _createZip(){
        try {
            $zip = new \ZipArchive();
            $fn = $this->file_name.".zip";
            if($zip->open($fn, \ZipArchive::CREATE) === TRUE){
                $zip->addFile($this->file_script, basename($this->file_script));
                $zip->close();
                unlink($this->file_script);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function restore(){
        try {
            $model = new BackupSchema();
            $model->enableZip = TRUE;
            $model->backup();
            $this->_prepareFile();
            return $this->_execFile();
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _prepareFile(){
        try {
            if(file_exists($this->path.$this->name)){
                $this->getFile();
                if($this->_file){
                    switch ($this->_file["extension"]) {
                        case 'sql':
                            $this->file_script = $this->name;
                            break;
                        case 'zip':
                            $this->_extractFile();
                            break;
                        default:
                            $message = 'ERROR: Tipo de archivo no soportado';
                            $this->addError('name', $message);
                            throw new Exception($message, 92002);
                    }
                }
            } else {
                $message = 'ERROR: Archivo de Respaldo no Encontrado';
                $this->addError('name', $message);
                throw new Exception($message, 92003);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _extractFile(){
        try {
            $zip = new \ZipArchive();
            if($zip->open($this->path.$this->name)){
                $zip->extractTo(dirname($this->path));
                $zip->close();
                $this->file_script = str_replace( ".".$this->_file["extension"], "", $this->name);
            } else {
                $message = 'ERROR: No se pudo abrir el archivo';
                $this->addError('name', $message);
                throw new Exception($message, 92004);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _execFile(){
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(file_exists($this->file_script)){
                $sql = file_get_contents($this->path.$this->file_script);
                $cmd = Yii::$app->db->createCommand($sql);
                $cmd->execute();
                $transaction->commit();
                return 'Respaldo Restaurado Exitosamente';
            } else {
                $message = 'ERROR: Archivo de Respaldo no Encontrado';
                $this->addError('name', $message);
                throw new Exception($message, 92005);
            }
        } catch (Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        } 
    }


    public function upload(){
        
    }
    
    
}
