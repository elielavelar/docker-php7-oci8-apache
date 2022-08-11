<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\models;

use common\models\Appointments;
use common\models\Servicecentres;
use yii\db\Query;
use common\models\Type;
use Exception;
use moonland\phpexcel\Excel;
use PHPExcel_IOFactory;
use yii\helpers\FileHelper;
use PHPExcel_Worksheet_Drawing;

/**
 * Description of AppointmentsReports
 *
 * @author avelare
 */
class AppointmentsReports extends Appointments {
    
    const EXCEL_FORMAT = 'Excel2007';
    public static $report_name = '';
    public static $report_excel;
    public static $includeBeforeMonth = TRUE;
    public static $showBeforeMonth = TRUE;
    public static $includeCitizenWithoutApp = FALSE;
    public static $year = NULL;
    public static $month = NULL;
    public static $monthName = NULL;
    public static $single_month = FALSE;
    public static $load = FALSE;

    public static function setReportName($name = NULL){
        self::$report_name = $name;
    }

    public static function getSignUpByMonth($data = []) {
        try{
            if(!self::$load){
                self::_defineDates($data);
            }
            $operator = self::$showBeforeMonth ? "<=":"=";
            $query = new Query();
            $query->select(["CAST(date_format(CreateDate,'%m') as INT) Month","GETMONTHNAME(CAST(date_format(CreateDate,'%m') AS INT)) MonthName","SignUpMethod","count(1) Quantity"]);
            $query->from('citizen');
            if(!self::$includeCitizenWithoutApp){
                $query->innerJoin('appointments b', 'citizen.Id = b.IdCitizen');
            }
            $query->where("date_format(CreateDate,'%Y') = :year", [':year'=> self::$year]);
            if(isset(self::$month)){
                if(self::$month > 0) {
                    $query->andWhere("CAST(date_format(CreateDate,'%m') as INT) $operator :month", [':month'=> self::$month]);
                } 
            }
            $query->groupBy(["date_format(CreateDate,'%m'), MONTHNAME(date_format(CreateDate,'%m')), SignUpMethod"]);
            $query->orderBy(["CAST(date_format(CreateDate,'%m') as INT)"=>"ASC"]);
            $result = $query->all();
            
            $months = [0,0,0,0,0,0,0,0,0,0,0,0];
            $records = [];
            $records[0] = ['name'=>'Aplicación en Línea','data'=>$months];
            $records[1] = ['name'=>'Call Center','data'=>$months];
            $records[2] = ['name'=>'No Definida','data'=>$months];
            
            foreach ($result as $r){
                $t = NULL;
                switch ($r["SignUpMethod"]){
                    case 'frontend':
                        $t = 0;
                        break;
                    case 'backend':
                        $t = 1;
                        break;
                    default :
                        $t = 2;
                        break;
                }
                $records[$t]['data'][($r["Month"] -1)] = (int) $r["Quantity"];
            }
            return $records;
        } catch (Exception $exc){
            throw $exc;
        }
    }
    public static function getAppointmentByMonth($data = []) {
        try{
            if(!self::$load){
                self::_defineDates($data);
            }
            $operator = self::$showBeforeMonth? "<=":"=";
            $query = new Query();
            $query->select(["CAST(date_format(AppointmentDate,'%m') as INT) Month","GETMONTHNAME(CAST(date_format(AppointmentDate,'%m') AS INT)) MonthName","RegistrationMethod","count(1) Quantity"]);
            $query->from('appointments');
            $query->where("date_format(AppointmentDate,'%Y') = :year", [':year'=> self::$year]);
            if(isset(self::$month)){
                if(self::$month > 0) {
                    $query->andWhere("CAST(date_format(AppointmentDate,'%m') as INT) $operator :month", [':month'=> self::$month]);
                } 
            }
            $query->groupBy(["date_format(AppointmentDate,'%m'), MONTHNAME(date_format(AppointmentDate,'%m')), RegistrationMethod"]);
            $query->orderBy(["CAST(date_format(AppointmentDate,'%m') as INT)"=>"ASC"]);
            $result = $query->all();
            
            $months = [0,0,0,0,0,0,0,0,0,0,0,0];
            $records = [];
            $records[0] = ['name'=>'Aplicación en Línea','data'=>$months];
            $records[1] = ['name'=>'Call Center','data'=>$months];
            $records[2] = ['name'=>'No Definida','data'=>$months];
            
            foreach ($result as $r){
                $t = NULL;
                switch ($r["RegistrationMethod"]){
                    case 'frontend':
                        $t = 0;
                        break;
                    case 'backend':
                        $t = 1;
                        break;
                    default :
                        $t = 2;
                        break;
                }
                $records[$t]['data'][($r["Month"] -1)] = (int) $r["Quantity"];
            }
            return $records;
        } catch (Exception $exc){
            throw $exc;
        }
    }
    
    public static function getSignUpByCentre($data = []){
        try {
            if(!self::$load){
                self::_defineDates($data);
            }
            $operator = self::$includeBeforeMonth ? "<=":"=";
            
            $leftCondition = "AND date_format(c.AppointmentDate,'%Y') = :year";
            $leftParams = [':year'=> self::$year];
            
            if(!empty(self::$month)){
                $_month = (strlen(self::$month) < 2 ? '0':''). self::$month;
                $_date = self::$year."-".$_month;
                switch (self::$month){
                    case 0:
                        $leftCondition = "AND date_format(c.AppointmentDate,'%Y') = :year";
                        $leftParams = [':year'=> self::$year];
                        break;
                    default :
                        $leftCondition = "AND date_format(c.AppointmentDate,'%Y') = :year"
                            . " AND date_format(c.AppointmentDate,'%m') $operator :month ";
                        $leftParams = [':year'=> self::$year,':month'=> $_month];
                        break;
                }
            }
            $query = new Query();
            $query->select(["a.Id","a.Name",'a.MBCode',"count(c.IdServiceCentre) Quantity"]); //
            $query->from('servicecentres a');
            $query->innerJoin('type b','b.Id = a.IdType');
            $query->leftJoin('appointments c',"c.IdServiceCentre = a.Id ".$leftCondition, $leftParams);
            $query->where('b.Code = :type',[':type'=> Servicecentres::TYPE_DUISITE]);
            $query->groupBy(["a.Id","a.Name",'a.MBCode']);
            $query->orderBy(['a.MBCode'=>'ASC']);
            $result = $query->all();
            
            $response = [];
            $dataset = [];
            $drilldown = ["series"=>[]];
            
            foreach ($result as $r){
                
                $params = [':id'=> $r["Id"]];
                $params = array_merge($params, $leftParams);
                
                $query_operations = new Query();
                $query_operations->select(["a.Id","a.Name",'count(c.IdType) Quantity']);
                $query_operations->from('type a');
                $query_operations->leftJoin('appointments c', "c.IdType = a.Id AND c.IdServiceCentre = :id ".$leftCondition, $params);
                $query_operations->where(['a.KeyWord' => 'Process']);
                $query_operations->groupBy(["a.Id","a.Name"]);
                $query_operations->orderBy(["a.Id"=>"ASC"]);
                $appointments = $query_operations->all();
                
                $dataApp = [];
                foreach ($appointments as $app){
                    $dataApp[] = [$app["Name"], (int) $app["Quantity"]];
                }
                
                $drilldown["series"][] = [
                    'name'=> $r["Name"],
                    'id'=> $r["Name"],
                    'colorByPoint'=>TRUE,
                    'data'=> $dataApp,
                ];
                $dataset[] = ["name"=>$r["Name"],"y"=>(int)$r["Quantity"],"drilldown"=>$r["Name"]];
            }
            
            
            $response["dataset"] = $dataset;
            $response["drilldown"] = $drilldown;
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    
    public static function getAppointmentByType($data = []){
        try {
            if(!self::$load){
                self::_defineDates($data);
            }
            $operator = self::$includeBeforeMonth ? "<=":"=";
            
            $leftCondition = "AND date_format(c.AppointmentDate,'%Y') = :year";
            $leftParams = [':year'=> self::$year];
            
            if(!empty(self::$month)){
                $_month = (strlen(self::$month) < 2 ? '0':''). self::$month;
                $_date = self::$year."-".$_month;
                switch (self::$month){
                    case 0:
                        $leftCondition = "AND date_format(c.AppointmentDate,'%Y') = :year";
                        $leftParams = [':year'=> self::$year];
                        break;
                    default :
                        $leftCondition = "AND date_format(c.AppointmentDate,'%Y') = :year"
                            . " AND date_format(c.AppointmentDate,'%m') $operator :month ";
                        $leftParams = [':year'=> self::$year,':month'=> $_month];
                        break;
                }
                
            }
            
            $response =[];
            $query = new Query();
            $query->select(['a.Name','count(c.IdType) Quantity']);
            $query->from('type a')
                    ->innerJoin('state b', 'b.Id = a.IdState')
                    ->leftJoin('appointments c', "c.IdType = a.Id ".$leftCondition, $leftParams)
                    ->where(['b.Code'=> Type::STATUS_ACTIVE,'a.KeyWord'=>'Process'])
                    ->groupBy(['a.Name']);
            
            if(!empty($data)){
                \Yii::$app->customFunctions->applyQueryCriteria($query, $data);
            }
            $result = $query->all();
            $_data = [];
            $total = 0;
            foreach ($result as $r){
                $_data[] = [$r["Name"],(int)$r["Quantity"]];
                $total += (int)$r["Quantity"];
            }
            $response[] = ["name"=>"Trámites",'colorByPoint'=>TRUE, "data" => $_data,'dataSum'=> $total];
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public static function exportSignUpByMonth($data = []){
        try {
            $date = $data['AppointmentDate'];
            $result_data = self::_prepareSignUpByMonth($data);
            self::$report_name = "Reporte de Registro de Ciudadadanos ".$data["AppointmentDate"];
            $objReader = \PHPExcel_IOFactory::createReader(self::EXCEL_FORMAT);
            \PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);
            $objReader->setIncludeCharts(TRUE);
            $pFileName = \Yii::getAlias("@backend/attachments/CitizenSignUp.xlsx");
            $excel = $objReader->load($pFileName);
            $excel->setActiveSheetIndex(0);
            $sheet = $excel->getActiveSheet();
            
            /*INSERT IMAGE*/
            $objDrawing = new \PHPExcel_Worksheet_HeaderFooterDrawing();
            $objDrawing->setName('Logo');
            $objDrawing->setDescription('Logo');
            $logo = \Yii::getAlias('@backend/web/img/logo.png');
            $objDrawing->setPath($logo);
            #$objDrawing->setOffsetX(8);    // setOffsetX works properly
            #$objDrawing->setOffsetY(10);  //setOffsetY has no effect
//            $objDrawing->setCoordinates('BG1');
            #$objDrawing->setHeight(75); // logo height
            #$objDrawing->setWorksheet($excel->getActiveSheet());
            
            $sheet->getHeaderFooter()->setOddHeader(self::$report_name)
                    ->addImage($objDrawing, \PHPExcel_Worksheet_HeaderFooter::IMAGE_FOOTER_LEFT);
//            $sheet->getHeaderFooter()->setOddHeader(self::$report_name)->setOddHeader('&L&G&');
            $startCell = 'M2';
            $sheet->fromArray($result_data, NULL, $startCell);

/*            
            $chart = new \PHPExcel_Chart('Chart1');
            
            echo $chart->getTitle(); die();
  */          
            $objWriter = \PHPExcel_IOFactory::createWriter($excel, self::EXCEL_FORMAT);
            $objWriter->setIncludeCharts(TRUE);
            $tempName = "CitizenSignUp-$date.xlsx";
            $tempFile =  \Yii::getAlias("@backend/web/temp/$tempName");
            if(file_exists($tempFile)){
                chmod($tempFile, 0777);
                unlink($tempFile);
            }
            $objWriter->save($tempFile);
            $url = \yii\helpers\Url::to("@web/temp/$tempName");
            return $url;
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public static function exportAppointmentByMonth($data = []){
        try {
            $date = $data['AppointmentDate'];
            $result_data = self::_prepareDataAppointmentByMonth($data);
            self::$report_name = "Reporte de Citas Registradas ".$data["AppointmentDate"];
            $objReader = \PHPExcel_IOFactory::createReader(self::EXCEL_FORMAT);
            \PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);
            $objReader->setIncludeCharts(TRUE);
            $pFileName = \Yii::getAlias("@backend/attachments/AppointmentByMonth.xlsx");
            $excel = $objReader->load($pFileName);
            $excel->setActiveSheetIndex(0);
            $sheet = $excel->getActiveSheet();
            
            /*INSERT IMAGE*/
            $objDrawing = new \PHPExcel_Worksheet_HeaderFooterDrawing();
            $objDrawing->setName('Logo');
            $objDrawing->setDescription('Logo');
            $logo = \Yii::getAlias('@backend/web/img/logo.png');
            $objDrawing->setPath($logo);
            #$objDrawing->setOffsetX(8);    // setOffsetX works properly
            #$objDrawing->setOffsetY(10);  //setOffsetY has no effect
//            $objDrawing->setCoordinates('BG1');
            #$objDrawing->setHeight(75); // logo height
            #$objDrawing->setWorksheet($excel->getActiveSheet());
            
            $sheet->getHeaderFooter()->setOddHeader(self::$report_name)
                    ->addImage($objDrawing, \PHPExcel_Worksheet_HeaderFooter::IMAGE_FOOTER_LEFT);
//            $sheet->getHeaderFooter()->setOddHeader(self::$report_name)->setOddHeader('&L&G&');
            $startCell = 'M2';
            $sheet->fromArray($result_data, NULL, $startCell);

/*            
            $chart = new \PHPExcel_Chart('Chart1');
            
            echo $chart->getTitle(); die();
  */          
            $objWriter = \PHPExcel_IOFactory::createWriter($excel, self::EXCEL_FORMAT);
            $objWriter->setIncludeCharts(TRUE);
            $tempName = "AppointmentByMonth-$date.xlsx";
            $tempFile =  \Yii::getAlias("@backend/web/temp/$tempName");
            if(file_exists($tempFile)){
                chmod($tempFile, 0777);
                unlink($tempFile);
            }
            $objWriter->save($tempFile);
            $url = \yii\helpers\Url::to("@web/temp/$tempName");
            return $url;
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public static function exportDataByCentre($data = []){
        try {
            $date = $data['AppointmentDate'];
            $result_data = self::_prepareDataByCentre($data);
            $month = isset($data["AppointmentMonth"]) ? $data["AppointmentMonth"]:NULL;
            $name = ($month ? \Yii::$app->customFunctions->getMonthName(($month))."-":"").$data["AppointmentDate"];
            self::$report_name = "Reporte de Citas por Duicentro ".$name;
            
            $objReader = \PHPExcel_IOFactory::createReader(self::EXCEL_FORMAT);
            \PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);
            $objReader->setIncludeCharts(TRUE);
            $pFileName = \Yii::getAlias("@backend/attachments/AppointmentServicecentres.xlsx");
            $excel = $objReader->load($pFileName);
            $excel->setActiveSheetIndex(0);
            $sheet = $excel->getActiveSheet();
            
            $sheet->getHeaderFooter()->setOddHeader(self::$report_name);
            $startCell = 'M5';
            
            $sheet->setCellValue('A1', self::$report_name)->getStyle()->getFont()->setBold(TRUE);

            $sheet->fromArray($result_data, NULL, $startCell);

            $objWriter = \PHPExcel_IOFactory::createWriter($excel, self::EXCEL_FORMAT);
            $objWriter->setIncludeCharts(TRUE);
            $tempName = "AppointmentServicecentres-$name.xlsx";
            $tempFile =  \Yii::getAlias("@backend/web/temp/$tempName");
            if(file_exists($tempFile)){
                chmod($tempFile, 0777);
                unlink($tempFile);
            }
            $objWriter->save($tempFile);
            $url = \yii\helpers\Url::to("@web/temp/$tempName");
            return $url;
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private static function _prepareSignUpByMonth(&$data = []){
        try {
            $result = self::getSignUpByMonth($data);
            $result_data = [];
            
            foreach ($result as $key => $categories){
                $months_values = $categories["data"];
                foreach ($months_values as $month => $val){
                    $month_name = \Yii::$app->customFunctions->getMonthName(($month+1));
                    $result_data[$month_name][$key] = $val;
                }
            }
            foreach ($result_data as $month => $values){
                $sum = 0;
                foreach ($values  as $key => $val){
                    $sum += $val;
                }
                $l = count($values) - 1;
                #$result_data[$month][$l] = $sum;
                unset($result_data[$month][$l]);
            }
            return $result_data;
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    private static function _prepareDataAppointmentByMonth(&$data = []){
        try {
            $result = self::getAppointmentByMonth($data);
            $result_data = [];
            
            foreach ($result as $key => $categories){
                $months_values = $categories["data"];
                foreach ($months_values as $month => $val){
                    $month_name = \Yii::$app->customFunctions->getMonthName(($month+1));
                    $result_data[$month_name][$key] = $val;
                }
            }
            foreach ($result_data as $month => $values){
                $sum = 0;
                foreach ($values  as $key => $val){
                    $sum += $val;
                }
                $l = count($values) - 1;
                unset($result_data[$month][$l]);
            }
            return $result_data;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private static function _prepareDataByCentre(&$data = []){
        try {
            $result = self::getSignUpByCentre($data);
            $result_data = [];
            $dataset = $result["dataset"];
            $drilldown = $result["drilldown"]["series"];
            
            foreach ($dataset as $key => $centre){
                $result_data[$centre["name"]] = [];
            }
            
            foreach ($drilldown as $key => $values){
                $series = $values["data"];
                foreach ($series as $skey => $val){
                    array_push($result_data[$values["name"]], $val[1]);
                }
            }
            return $result_data;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public static function exportSummary($data = []){
        try {
            self::_defineDates($data);
            $date = self::$year;
            
            $objReader = \PHPExcel_IOFactory::createReader(self::EXCEL_FORMAT);
            \PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);
            $objReader->setIncludeCharts(TRUE);
            $report = "SummaryReport".(self::$single_month ? "_SingleMonth":"").".xlsx";
            $pFileName = \Yii::getAlias("@backend/attachments/$report");
            self::$report_excel = $objReader->load($pFileName);
            self::_prepareSummarySignUp($data);
            self::_prepareSummaryAppointmentByMonth($data);
            self::_prepareSummaryAppointmentCentre($data);
            self::$report_excel->setActiveSheetIndex(0);
            
            $objWriter = \PHPExcel_IOFactory::createWriter(self::$report_excel, self::EXCEL_FORMAT);
            $objWriter->setIncludeCharts(TRUE);
            $tempName = "SummaryReport-$date.xlsx";
            $tempFile =  \Yii::getAlias("@backend/web/temp/$tempName");
            if(file_exists($tempFile)){
                chmod($tempFile, 0777);
                unlink($tempFile);
            }
            $objWriter->save($tempFile);
            $url = \yii\helpers\Url::to("@web/temp/$tempName");
            return $url;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private static function _prepareSummarySignUp(&$data = []){
        try {
            $date = self::$year;
            $result_data = self::_prepareSignUpByMonth($data);
            self::$report_name = "Reporte de Registro de Ciudadadanos ".$date;
            self::$report_excel->setActiveSheetIndex(0);
            $sheet = self::$report_excel->getActiveSheet();
            
            $sheet->getHeaderFooter()->setOddHeader(self::$report_name);
            
            #$sheet->setCellValue('A1', self::$report_name)->getStyle()->getFont()->setBold(TRUE);
            $startCell = 'L';
            $cellNum = "5";
            if(self::$single_month){
                $sheet->setCellValue('K'.$cellNum, self::$monthName)->getStyle()->getFont()->setBold(TRUE);
                $result_data = isset($result_data[self::$monthName]) ? $result_data[self::$monthName]:[];
            } 
            $sheet->fromArray($result_data, NULL, $startCell.$cellNum);
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private static function _prepareSummaryAppointmentByMonth(&$data = []) {
        try {
            $date = self::$year;
            $result_data = self::_prepareDataAppointmentByMonth($data);
            self::$report_name = "Reporte de Citas Registradas ".$date;
            self::$report_excel->setActiveSheetIndex(1);
            $sheet = self::$report_excel->getActiveSheet();
            
            $sheet->getHeaderFooter()->setOddHeader(self::$report_name);
            #$sheet->setCellValue('A1', self::$report_name)->getStyle()->getFont()->setBold(TRUE);
            $startCell = 'L';
            $cellNum = "5";
            if(self::$single_month){
                $sheet->setCellValue('K'.$cellNum, self::$monthName)->getStyle()->getFont()->setBold(TRUE);
                $result_data = isset($result_data[self::$monthName]) ? $result_data[self::$monthName]:[];
            } 
            $sheet->fromArray($result_data, NULL, $startCell.$cellNum);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private static function _prepareSummaryAppointmentCentre(&$data = []) {
        try {
            $date = self::$year;
            $result_data = self::_prepareDataByCentre($data);
            $month = self::$month;
            $name = ($month ? \Yii::$app->customFunctions->getMonthName(($month))."-":"").$date;
            self::$report_name = "Reporte de Citas por Duicentro ".$name;
            
            self::$report_excel->setActiveSheetIndex(2);
            $sheet = self::$report_excel->getActiveSheet();
            
            $sheet->getHeaderFooter()->setOddHeader(self::$report_name);
            #$sheet->setCellValue('A1', self::$report_name)->getStyle()->getFont()->setBold(TRUE);
            $startCell = 'M4';
            $sheet->fromArray($result_data, NULL, $startCell);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private static function _defineDates(&$data = []){
        try {
            self::$year = $data['AppointmentDate'];
            self::$month = $data['AppointmentMonth'];
            self::$showBeforeMonth = isset($data["showBeforeMonth"]) ? ($data["showBeforeMonth"] == 1) :self::$showBeforeMonth;
            self::$includeBeforeMonth = self::$showBeforeMonth && (isset($data["includeBeforeMonth"]) ? ($data["includeBeforeMonth"] == 1) :self::$includeBeforeMonth);
            self::$includeCitizenWithoutApp = isset($data["includeCitizenWithoutApp"]) ? ($data["includeCitizenWithoutApp"] == 1) :self::$includeCitizenWithoutApp;
            self::$single_month = (self::$month > 0 && !(self::$showBeforeMonth || self::$includeBeforeMonth));
            self::$monthName = (self::$month > 0) ? \Yii::$app->customFunctions->getMonthName(self::$month):NULL;
            self::$load = TRUE;
            unset($data["includeBeforeMonth"], $data['showBeforeMonth'], $data["includeCitizenWithoutApp"], $data['AppointmentDate'], $data['AppointmentMonth']);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}