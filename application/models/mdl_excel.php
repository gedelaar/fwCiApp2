	<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mdl_excel
 *
 * @author gerard
 */
class mdl_excel extends CI_Model {
    //put your code here
    function __construct() {
	error_reporting( 4 );
        // Call the Model constructor
        parent::__construct();
        //load our new PHPExcel library
        $this->load->library('Excel');
		//$this->db->simple_query('SET NAMES \'utf-8\'');
    }

    function Maak_test_excel() {
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('test worksheet');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'This is just some text value');
        //change the font size
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        //merge cell A1 until D1
        $this->excel->getActiveSheet()->mergeCells('A1:D1');
        //set aligment to center for that merged cell (A1 to D1)
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $filename = 'just_some_random_name.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
		exit;
    }

    function Maak_excel_Overzicht_van_bar($query) {
        //activate worksheet number 1
        $title = "Overzicht van bardienst";
		//echo "<br>";
		//echo $title;
		
        $styleArray = array(
            'font' => array(
                'bold' => true
                ));
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('overzicht');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', $title);
        //change the font size
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
        //make the font become bold
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        //merge cell A1 until D1
        $this->excel->getActiveSheet()->mergeCells('A1:D1');
        //set aligment to center for that merged cell (A1 to D1)
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		//print_r($query);
		
       foreach ($query as $rows) {
            $start_rij = 2;
            $i = $start_rij; //starting row           
            foreach ($rows as $row) {
                $y = 0;
                foreach ($row as $key => $veld) {
				
                    if ($i == $start_rij) {
					//	print_r($row);
                        $this->excel->getActiveSheet()->setCellValueByColumnAndRow($y, $i, utf8_decode($key));						
                        $this->excel->getActiveSheet()->getStyle('A' . $i . ":z" . $i)->applyFromArray($styleArray);
					//	echo "<BR>".$key;
                    }
                    $this->excel->getActiveSheet()->setCellValueByColumnAndRow($y, $i + 1, trim(utf8_decode($veld)));
					$x=utf8_decode($this->excel->getActiveSheet()->setCellValueByColumnAndRow($y, $i + 1, trim($veld)));
					//echo "<BR>".$veld;
					//echo print_r($x);
					
                    $y = $y + 1;
                }
                $i = $i + 1;
            }
        }
		//die;
        $columnID = 'A';
        $lastColumn = $this->excel->getActiveSheet()->getHighestColumn();
        do {
            $this->excel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            $columnID++;
        } while ($columnID != $lastColumn);

       //die;

        $filename = trim($title) . ".xls"; //save our workbook as this file name
		//die;
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
		//$objWriter = new PHPExcel_Writer_Excel2007($this->excel);
		//print_r($this->excel);
		//die;
		

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel,'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
		exit;
    }
	
	function SimpleTest() {
	/** Error reporting */
	$this->output->enable_profiler(TRUE);
	    $this->load->library('excel');
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Europe/London');

		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');

		/** Include PHPExcel */
		//require_once '../Classes/PHPExcel.php';


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


		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'Hello')
					->setCellValue('B2', 'world!')
					->setCellValue('C1', 'Hello')
					->setCellValue('D2', 'world!');

		// Miscellaneous glyphs, UTF-8
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A4', 'Miscellaneous glyphs')
					->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Simple');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);


		// Redirect output to a client’s web browser (Excel5)
		//header('Content-Type: application/vnd.ms-excel');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="01simple.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$this->excel->stream('filename.xls',$data);	

		$objWriter->save('php://output');
		exit;

	}

    function create_bar_ovz($data) {
        //echo "hier";
        $this->load->helper('download');
        $this->load->model('leden_model');
        //$range = 100;
        //echo "hier";
        //$data['wed'] = $this->get_wedstrijd_range_asc($data, $range);
        //var_dump($data);die;
        $this->db->select('*');
        $data['bar']=$this->db->get('baroverzicht');
        $bar=$data['bar']->result();
        //var_dump($bar);die;
        //SELECT * FROM `baroverzicht` ;
        $outfile = 'bar_ovz.csv';
        $file = "datum;tijd;thuis;uit;bardienst;telefoon;mobiel\n";
        foreach ($bar as $row) {
            //echo "<pre>";
            //print_r($row);
            //echo $row->datum;
            //echo "</pre>";
            if($hlp_datum<>$row->datum){
                $file.="\n";
            }
            //$poule = $this->team_model->get_poulid_from_team_lidnr($row['LIDNR']);
            //$file.=$row['LIDNR'] . ";" . $row['CATEGORIE'] . ";" . $row['ZOEKNAAM'] . ";" . $row['AWGR'] . ";" . $row['ref_telling'] . ";" . $poule . "\n";
//			echo "<pre>";
//			var_dump ($row);
//			echo "</pre>";
            $file.=$row->datum . ";" . $row->tijd . ";" . $row->thuis . ";" . $row->uit . ";" . $row->zvoornaam ." ". $row->ztussenvoegsel." ". $row->znaam . ";" . $row->telnr.";" . $row->mobiel. "\n";
            $hlp_datum=$row->datum;
        }
        //var_dump( $file);
        //die;
        force_download($outfile, $file);
        return;
    }
        
        
        
}

?>
