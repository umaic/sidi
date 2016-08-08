<?
session_start();

$nom = (!isset($_GET["nombre_archivo"])) ? $case."_sissh" : $_GET["nombre_archivo"];

if (isset($_GET['csv2xls'])) {
    include 'admin/lib/common/phpexcel/PHPExcel/IOFactory.php';

    $objReader = PHPExcel_IOFactory::createReader('CSV');

    // If the files uses a delimiter other than a comma (e.g. a tab), then tell the reader
    //$objReader->setDelimiter("\t");
    // If the files uses an encoding other than UTF-8 or ASCII, then tell the reader
    //$objReader->setInputEncoding('ISO-8859-1');

    $objPHPExcel = $objReader->load($_SERVER['DOCUMENT_ROOT'].$_GET['csv_path']);

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    //$objWriter->save($_SERVER['DOCUMENT_ROOT'].'/tmp/'.$nom.'.xls');
	header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"".$nom.".xls\"");
    header("Cache-Control: max-age=0");
    $objWriter->save('php://output');
}

else {

    //LIBRERIAS
    include_once($_SERVER["DOCUMENT_ROOT"]."/sissh/consulta/lib/libs_mapa_i.php");
    
    include $_SERVER["DOCUMENT_ROOT"].'/sissh/admin/lib/common/phpexcel/PHPExcel/IOFactory.php';

    $inputFileType = 'HTML';
    $outputFileType = 'Excel2007';


    $case = $_GET["case"];

    if (isset($_GET['pdf']) && $_GET['pdf'] == 1){
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"".$nom.".pdf\"");
    }
    else{
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"".$nom.".xlsx\"");
    }

    switch ($case){
        case 'org':
            //ORGS
            if ($_GET['pdf'] != ""){
                $org_dao = New OrganizacionDAO();
                //var_dump($_SESSION["id_orgs"]);
                //die;
                $org_dao->ReporteOrganizacion(implode(",",$_SESSION["id_orgs"]),$_GET['pdf'],$_GET['basico'],1);
            }

            break;

        case 'org_reporte_admin_2':
            echo $_SESSION["pdf_code"];
            break;

        case 'dato_sectorial':
            //DATO SECTORIAL
            if ($_GET['pdf'] != ""){

                $dao = New DatoSectorialDAO();

                $dao->ReporteDatoSectorial($_GET['pdf'],1,$_GET['dato_para'],1);
            }

            break;

        case 'desplazamiento':
            //DESPLAZAMIENTO
            $dao = New DesplazamientoDAO();
            $dao->ReporteDesplazamiento(implode(",",$_SESSION["id_desplazamientos"]),$_GET['pdf'],1,$_GET['dato_para'],1);

            break;

        case 'desplazamiento_gra_resumen':
            //DESPLAZAMIENTO
            if ($_GET['pdf'] != ""){

                $f = $_SERVER["DOCUMENT_ROOT"].'/sissh/static/html2phpexcel.html';
                file_put_contents($f,$_SESSION["xls_desplazamiento"]);
                
                $objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objPHPExcelReader->load($f);
                
                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                $objWriter->save('php://output');

            }
            break;

        case 'mina_gra_resumen':
                $f = $_SERVER["DOCUMENT_ROOT"].'/sissh/static/html2phpexcel.html';
                file_put_contents($f,$_SESSION["xls_mina"]);
                
                $objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objPHPExcelReader->load($f);
                
                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                $objWriter->save('php://output');
            break;

        case 'evento_c':
            //EVENTO CONFLICTO
            $f = $_SERVER["DOCUMENT_ROOT"].'/sissh/static/html2phpexcel.html';
            file_put_contents($f,$_SESSION["evento_c_xls"]);
            
            $objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objPHPExcelReader->load($f);
            
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save('php://output');

            break;
        case 'reporte_admin_org':
            //EVENTO CONFLICTO
            echo $_SESSION["reporte_admin_org"];
            break;

            //General para exportar html que esta en $_SESSION
        case 'pdf':
            //LIBRERIAS
            include_once("admin/lib/common/html2fpdf/html2fpdf.php");

            $pdf=new HTML2FPDF();
            $pdf->AddPage();

            $pdf->WriteHTML($_SESSION["pdf_code"]);

            echo $pdf->Output('','S');

            break;

        case 'mapa_export_xls':
            echo $_SESSION["xls"];
            $f = $_SERVER["DOCUMENT_ROOT"].'/sissh/static/html2phpexcel.html';
            file_put_contents($f,$_SESSION["xls"]);
            
            $objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objPHPExcelReader->load($f);
            
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save('php://output');
            break;

        case 'xls_session':
            echo $_SESSION["xls"];
            $f = $_SERVER["DOCUMENT_ROOT"].'/sissh/static/html2phpexcel.html';
            file_put_contents($f,$_SESSION["xls"]);
            
            $objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objPHPExcelReader->load($f);
            
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save('php://output');
            break;
        
        //Muestra el codigo pdf que este en sesion
        case 'pdf_session':
            echo $_SESSION["pdf"];
            break;

    }
}
?>
