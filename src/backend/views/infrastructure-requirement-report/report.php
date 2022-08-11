<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require __DIR__ . "/../../../vendor/autoload.php";

/* @var $this yii\web\View */
/* @var $models backend\models\Options */
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:I1');

$styleArray = array(
    'font'  => array(
    'bold'  => true,    
    'size'  => 18,
    'name'  => 'Arial'
    )
);

$objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleArray);


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Lista de Verificación del Estado Fisico del Duicentro y Mobiliario/ Check list Status Duisite')
            ->setCellValue('A3', 'DUICENTRO')
            ->setCellValue('A4', 'FECHA')
            ->setCellValue('A6', 'ENERGIA')
            ->setCellValue('A7', 'Tomas')
            ->setCellValue('A8', 'Regletas')
            ->setCellValue('A9', 'Contador')
            ->setCellValue('A10', 'Acometida')
            ->setCellValue('A11', 'LUMINARIAS')
            ->setCellValue('A12', 'Candelas')
            ->setCellValue('A13', 'Focos')
            ->setCellValue('A14', 'Lamparas')
            ->setCellValue('A15', 'AIRE ACODICIONADO')
            ->setCellValue('A16', 'Equipo')
            ->setCellValue('A17', 'Temperatura')
            ->setCellValue('A18', 'Goteo')
            ->setCellValue('A19', 'Control')
            ->setCellValue('A20', 'Ductos')
            ->setCellValue('A21', 'Otros')
            ->setCellValue('A22', 'BAÑOS')
            ->setCellValue('A23', 'Chorros')
            ->setCellValue('A24', 'Lavamanos')
            ->setCellValue('A25', 'Inodoros')
            ->setCellValue('A26', 'Valvulas')
            ->setCellValue('A27', 'PAREDES')
            ->setCellValue('A28', 'Tabla Yeso')
            ->setCellValue('A29', 'PUERTAS')
            ->setCellValue('A30', 'Madera')
            ->setCellValue('A31', 'Vidrio')
            ->setCellValue('A32', 'Chapas')
            ->setCellValue('A33', 'Otros')
            ->setCellValue('A34', 'PINTURA')
            ->setCellValue('A35', 'Blanca')
            ->setCellValue('A36', 'Acqua')
            ->setCellValue('A37', 'Otros');

$styleArrayBordersMedium = array(
    'borders' => array(
        'allborders' => array(
        'style' => PHPExcel_Style_Border::BORDER_MEDIUM
        )
    )
);

$objPHPExcel->getActiveSheet()->setCellValue('B5', 'Reservado para Departamento o Duicentro que hace la solicitud');
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B5:D5');
$objPHPExcel->getActiveSheet()->getStyle('B5')
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B5:D5')->applyFromArray($styleArrayBordersMedium);
unset($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('B5:D5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
->getStartColor()->setRGB('E9E9E9');

$objPHPExcel->getActiveSheet()->setCellValue('E5', 'Reservado para Mantenimiento');
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('E5:I5');
$objPHPExcel->getActiveSheet()->getStyle('E5')
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E5:I5')->applyFromArray($styleArrayBordersMedium);
unset($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('E5:I5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
->getStartColor()->setRGB('E9E9E9');

$styleArray = array(
    'font'  => array(
    'bold'  => true,
    'size'  => 10,
    'name'  => 'Arial'
    )
);

$objPHPExcel->getActiveSheet()->getStyle('B5:I5')->applyFromArray($styleArray);

$objPHPExcel->getActiveSheet()->setCellValue('B6', 'Fecha del problema o solicitud');
$objPHPExcel->getActiveSheet()->setCellValue('C6', 'Problema Reportado o solicitud');
$objPHPExcel->getActiveSheet()->setCellValue('D6', 'Ubicacion');
$objPHPExcel->getActiveSheet()->setCellValue('E6', 'Responsable de solucion');
$objPHPExcel->getActiveSheet()->setCellValue('F6', 'Fecha Programada');
$objPHPExcel->getActiveSheet()->setCellValue('G6', '% Avance');
$objPHPExcel->getActiveSheet()->setCellValue('H6', 'Fecha de Solucion');
$objPHPExcel->getActiveSheet()->setCellValue('I6', 'Observacion');            

$objPHPExcel->getActiveSheet()->getStyle('B6:I6')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle('B6:I6')
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B6:I6')
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('20');	
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('15');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('35');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('15');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('15');
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('15');
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('15');
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('15');
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('25');

$styleArray = array(
        'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000062'),
        'size'  => 11,
        'name'  => 'Arial'
    )
);

$objPHPExcel->getActiveSheet()->getStyle('A6:I6')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A11')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A15')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A22')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A27')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A29')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A34')->applyFromArray($styleArray);

$cell = 7;

$styleArrayBordersThin = array(
    'borders' => array(
      'allborders' => array(
        'style' => PHPExcel_Style_Border::BORDER_THIN
      )
    )
  );

foreach ($requirements as $requirement) {
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B'.$cell, $requirement->TicketDate)
            ->setCellValue('C'.$cell, $requirement->DamageDescription)
            ->setCellValue('D'.$cell, $requirement->SpecificLocation)
            ->setCellValue('F'.$cell, $requirement->RequirementDate)
            ->setCellValue('G'.$cell, $requirement->IdState)
            ->setCellValue('H'.$cell, $requirement->SolutionDate)
            ->setCellValue('I'.$cell, $requirement->Description)
            ;
    
    $objPHPExcel->getActiveSheet()->getStyle('B'.$cell.':I'.$cell)->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$cell.':I'.$cell)
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$cell.':I'.$cell)
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->getStyle('B'.$cell.':I'.$cell)->applyFromArray($styleArrayBordersThin);
            unset($styleArray);
    
    $cell++;
};
  
$objPHPExcel->getActiveSheet()->getStyle('A6:I6')->applyFromArray($styleArrayBordersMedium);
unset($styleArray);

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('FR-INF-001');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="FR-INF-001.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>