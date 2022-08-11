<?php

namespace client\controllers;

use Yii;
use common\models\prddui\Anexoacta;
use client\models\AnexoactaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use Exception;
use yii\web\Response;
use kartik\mpdf\Pdf;
/**
 * AnexoactaController implements the CRUD actions for Anexoacta model.
 */
class AnexoactaController extends Controller
{
    private $model;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Anexoacta models.
     * @return mixed
     */
    public function actionFilter($id)
    {
        $model = new Anexoacta();
        $model->COD_CTRO_SERV = $id;
        return $this->render('filter', [
            'model' => $model,
        ]);
    }
    
    public function actionGet(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $response = [];
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post(StringHelper::basename(Anexoacta::class));
                $this->model = Anexoacta::getReport($data);
                $response = [
                    'success' => true,
                    'path' => $this->_getReport()
                ] ;
            }
        } catch (Exception $exc) {
            $response = [
                'success'=>FALSE,
                'message'=>$exc->getMessage(),
                'code'=>$exc->getCode(),
            ];
        }
        return $response;
    }

    /**
     * Displays a single Anexoacta model.
     * @param string $COD_CTRO_SERV
     * @param string $FEC_FACTURACION
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($COD_CTRO_SERV, $FEC_FACTURACION)
    {
        return $this->render('view', [
            'model' => $this->findModel($COD_CTRO_SERV, $FEC_FACTURACION),
        ]);
    }

    /**
     * Creates a new Anexoacta model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Anexoacta();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'COD_CTRO_SERV' => $model->COD_CTRO_SERV, 'FEC_FACTURACION' => $model->FEC_FACTURACION]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    
    public function actionPreview()
    {
        $data = [
            'COD_CTRO_SERV' => 49,
            'FEC_FACTURACION' => '18-03-2021',
        ];
        $this->model = Anexoacta::getReport($data);
        return $this->renderPartial('_form/_content', ['model' => $this->model]);
    }

    /**
     * Updates an existing Anexoacta model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $COD_CTRO_SERV
     * @param string $FEC_FACTURACION
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($COD_CTRO_SERV, $FEC_FACTURACION)
    {
        $model = $this->findModel($COD_CTRO_SERV, $FEC_FACTURACION);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'COD_CTRO_SERV' => $model->COD_CTRO_SERV, 'FEC_FACTURACION' => $model->FEC_FACTURACION]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Anexoacta model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $COD_CTRO_SERV
     * @param string $FEC_FACTURACION
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($COD_CTRO_SERV, $FEC_FACTURACION)
    {
        $this->findModel($COD_CTRO_SERV, $FEC_FACTURACION)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Anexoacta model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $COD_CTRO_SERV
     * @param string $FEC_FACTURACION
     * @return Anexoacta the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($COD_CTRO_SERV, $FEC_FACTURACION)
    {
        if (($model = Anexoacta::findOne(['COD_CTRO_SERV' => $COD_CTRO_SERV, 'FEC_FACTURACION' => $FEC_FACTURACION])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    private function _getReport(){
        try {
            $content = $this->renderPartial('_form/_content', ['model' => $this->model]);
            $urlPath = 'attachments/'.StringHelper::basename(Anexoacta::class);
            $_path = '@client/web/attachments/'.StringHelper::basename(Anexoacta::class);
            $fileName = StringHelper::basename(Anexoacta::class).'_'.str_replace(' ', '_', $this->model['HEADER']['DESC_CTRO_SERV']).'_'.$this->model['HEADER']['NUM_CORR_ACTA'].'.pdf';
            $path =  \Yii::getAlias($_path);
            if(!file_exists($path)){
                mkdir($path, 0777, TRUE);
            }
            $filePath = $_path."/".$fileName;
            $path =  \Yii::getAlias($filePath);
            if(file_exists($path)){
                unlink($path);
            }
            $pdf = new Pdf([
                // set to use core fonts only
                'mode' => Pdf::MODE_CORE, 
                // A4 paper format
                'format' => Pdf::FORMAT_LETTER, 
                // portrait orientation
                'orientation' => Pdf::ORIENT_PORTRAIT, 
                'destination' => Pdf::DEST_FILE, 
                'filename' => $path,
                'defaultFont' => 'Arial',
                'defaultFontSize' => 8,
                'marginLeft' => 7,
                'marginRight' => 11,
                'marginTop' => 10,
                'marginBottom' => 5,
                // your html content input
                // format content from your own css file if needed or use the
                // enhanced bootstrap css built by Krajee for mPDF formatting 
                'cssFile' => '@client/web/css/pdfformat.css',
                // any css to be embedded if required
                'cssInline' => '.kv-heading-1{font-size:18px}', 
                 // set mPDF properties on the fly
                'options' => ['title' => 'Anexo de Acta'],
            ]);
            
            // return the pdf output as per the destination setting
            $pdf->output($content, $path, Pdf::DEST_FILE);
            $url = Yii::$app->urlManager->createAbsoluteUrl($urlPath."/".$fileName);
            return $url;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
