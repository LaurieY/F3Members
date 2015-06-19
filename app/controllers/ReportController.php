<?php

class ReportController extends Controller {
	
	function afterroute() {
	echo Template::instance()->render('layout.htm');	
	}
	
	
	function writeemailpdf($thefile,$theset) {
//require 'vendor/autoload.php';
//$f3 = require('lib/base.php');	//based on http://www.fpdf.org/en/script/script21.php
	//	$f3=$this->f3;

	$admin_logger = new MyLog('admin.log');
	$admin_logger->write('in writeemailpdf',true);
		$f3=Base::instance();
	$uselog=$f3->get('uselog');
		$today = getdate();
		$u3ayear = $f3->get('SESSION.u3ayear');
		$lastu3ayear = $f3->get('SESSION.lastu3ayear');
		$admin_logger->write('in writeemailpdf for U3AYEAR = '.$f3->get('SESSION.u3ayear'),true);
		//$admin_logger->write('in writeemailpdf for fw = '.var_export($f3,true),true);
	$paidstatus="('Y','N','W')";
	
	$dayssince=7;
	$since=strftime("%d %b %Y", time() - ($dayssince* 24 * 60 * 60));
	$pdfselect = array('all'=>array('title'=> "U3A Marbella and Inland - Membership List ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and paidthisyear in ".$paidstatus." order by surname ASC "),
					'cm'=>array('title'=> "U3A Marbella and Inland - Committee Members ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and membtype in ('CM','CMS','CMGL') order by surname ASC "),
					'gl'=>array('title'=> "U3A Marbella and Inland - Group Leaders ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and membtype in ('GL','GLS','CMGL') order by surname ASC "),
					'unpaid'=>array('title'=> "U3A Marbella and Inland - Unpaid Members ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and paidthisyear in ('N') order by surname ASC "),
					'willpay'=>array('title'=> "U3A Marbella and Inland - WillPay Members ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and  paidthisyear in ('W') order by surname ASC "),
					'lastyear'=>array('title'=> "U3A Marbella and Inland - Last Year Members ".$lastu3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$lastu3ayear."' and status='Active' and paidthisyear in ".$paidstatus." order by surname ASC "),
					'weekly'=>array('title'=> "U3A Marbella and Inland - This Week's Payers since"."\n".$since,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email', amtpaidthisyear as 'Fee', feewhere as 'Who has'  from members where u3ayear='".$u3ayear."' and status in ('Active','Left') and paidthisyear in ('Y') and datepaid > NOW() - INTERVAL '". $dayssince ."' DAY  order by surname ASC "),
		
					);
					

		$pdf = new PDF('P','pt','A4');
		$pdf->SetFont('Arial','',10);
		$pdf->SetMargins(17,38,17);


		// ************  change the below to establish the database connection.   *********
		$host = 'localhost';
		$username = $f3->get('db_user');
		$password = $f3->get('db_pass');
		$database = $f3->get('db_name');
		$connok=$pdf->connect($host, $username, $password, $database);
		$admin_logger->write('in writeemailpdf after connect = '.$connok,$uselog);
		//$admin_logger->write('in writeemailpdf sql  = '.$pdfselect['all']['sqlselect'],$uselog);
		$sql_statement = $pdfselect[$theset]['sqlselect'];
		// Generate report
		$admin_logger->write('in writeemailpdf sql  = '.$sql_statement,$uselog);
		if($theset=='weekly') {
		$attr = array('titleFontSize'=>18, 'titleText'=>$pdfselect[$theset]['title'],
		'FontSizePt'=>9,
		'tablewidths'=>array(  // change these to get a better fit of columns
					100, // surname 
					80, // forename 
					38, // membnum 
					56, // phone 
					56, // mobile 
					150, // email 
					22, // amtpaidthisyear
					52 // Who has
				)
				); 
				}
				else{
						$attr = array('titleFontSize'=>18, 'titleText'=>$pdfselect[$theset]['title'],
		'tablewidths'=>array(  // change these to get a better fit of columns
					100, // surname 
					90, // forename 
					42, // membnum 
					64, // phone 
					64, // mobile 
					194, // email 
				//	52.68, /* membtype 
				//	48 /* location 
				)
				); 
				}
		$pdf->mysql_report($sql_statement, false, $attr );
		
		$dldir=$f3->get('BASE').$f3->get('downloads');
		$admin_logger->write('in writeemailpdf outputted to  '.$thefile,$uselog);
		//$pdf->Output($dldir.'/email_list_'.$theset.'.pdf',"F");
		$theresult= $pdf->Output($thefile,"S");
		//$admin_logger->write('in writeemailpdf output is  '.$theresult,$uselog);
		$pdf->Output($thefile,"F");

	return true;
		
	} //end of 	writeemailpdf
/******************
*		XMAIL copied from  phpmysqlautobackup_extras.php
*
*
**********************/

public	function xmail ($to_emailaddress,$from_emailaddress, $subject, $content, $file_name, $backup_type, $ver)
		{
		if(!defined('NEWLINE'))	define('NEWLINE',"\n");
		 $mail_attached = "";
		 $boundary = "----=_NextPart_000_01FB_010".md5($to_emailaddress);
		 $mail_attached.="--".$boundary.NEWLINE
							   ."Content-Type: application/octet-stream;".NEWLINE." name=\"$file_name\"".NEWLINE
							   ."Content-Transfer-Encoding: base64".NEWLINE
							   ."Content-Disposition: attachment;".NEWLINE." filename=\"$file_name\"".NEWLINE.NEWLINE
							   .chunk_split(base64_encode($content)).NEWLINE;
		 $mail_attached .= "--".$boundary."--".NEWLINE;
		 $add_header ="MIME-Version: 1.0".NEWLINE."Content-Type: multipart/mixed;".NEWLINE." boundary=\"$boundary\" ".NEWLINE;
		 $mail_content="--".$boundary.NEWLINE."Content-Type: text/plain; ".NEWLINE." charset=\"iso-8859-1\"".NEWLINE."Content-Transfer-Encoding: 7bit".NEWLINE.NEWLINE."Weekly Joiner Report Successful...".NEWLINE.NEWLINE."Please see attached for your Report file".NEWLINE.NEWLINE. $file_name .NEWLINE.$mail_attached;
		 return mail($to_emailaddress, $subject, $mail_content, "From: $from_emailaddress".NEWLINE."Reply-To:$from_emailaddress".NEWLINE.$add_header);
		} // end of xmail
		
public function weeklyxmail () {
		$since=strftime("%d %b %Y", time() - (7 * 24 * 60 * 60));
		$f3=Base::instance();
		$options= new Option($this->db);
		$options->initu3ayear();
		$mypdf= new ReportController();
		$dlfilename='downloads/email_list_weekly.pdf';
 
		$mypdf->writeemailpdf($dlfilename,'weekly');
		
		$buffer='';
			$f = fopen($dlfilename,'r');
			if(!$f)
				$this->Error('Unable to open input file: '.$dlfilename);
			$buffer=fread($f,(1024*1024));
			fclose($f);
//	$this->xmail('president@u3a.es', 'laurie@u3a.es','Report for Weekly Joiners since '.$since,$buffer,'email_list_weekly.pdf',"F","0.9"	);
//	$this->xmail('membership@u3a.es', 'laurie@u3a.es','Report for Weekly Joiners since '.$since,$buffer,'email_list_weekly.pdf',"F","0.9"	);
//	$this->xmail('laurie.lyates@gmail.com', 'laurie@u3a.es','Report for Weekly Joiners since '.$since,$buffer,'email_list_weekly.pdf',"F","0.9"	);
	$this->xmail('laurie2@lyates.com', 'laurie@u3a.es','Report for Weekly Joiners since '.$since,$buffer,'email_list_weekly.pdf',"F","0.9"	);
			
		$f3->set('view','member/exports.htm'); 
		$f3->set('page_head','Primary Member Lists');
		$f3->set('page_role',$f3->get('SESSION.user_role'))	;	
			 $f3->set('message','');
		
		
		}
public function financialxmail ($whichfy) {
		$this->financialreport( $whichfy,"('Active','Left')");
			$f3=$this->f3;		
		$f3->set('view','member/exports.htm'); 
		$f3->set('page_head','Primary Member Lists');
		$f3->set('page_role',$f3->get('SESSION.user_role'))	;	
			 $f3->set('message','');

}
public function financialxmail2 () {
		$this->financialreport( '2015',"('Active','Left')");
			$f3=$this->f3;		
		$f3->set('view','member/exports.htm'); 
		$f3->set('page_head','Primary Member Lists');
		$f3->set('page_role',$f3->get('SESSION.user_role'))	;	
			 $f3->set('message','');

}

 function financialreport( $whichyear,$statustypes)
		{
		$f3=$this->f3;
		$admin_logger = new MyLog('admin.log');
		$uselog=$f3->get('uselog');
		//echo date('H:i:s') , " whichyear=",$whichyear ,"\n";
		$admin_logger->write( 'Entering ReportController financialreport which year = '.$whichyear,$uselog );
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);

		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

		date_default_timezone_set('Europe/London');

		/** Include PHPExcel */
		require_once('vendor/Classes/PHPExcel.php');
		//require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


		// Create new PHPExcel object
		//echo date('H:i:s') , " Create new PHPExcel object" , EOL;
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Laurie Yates")
							 ->setLastModifiedBy("Laurie Yates")
							 ->setTitle("U3A Marbella and Inland Financial Statement for $whichyear")
							 ->setSubject("Financial Statement for $whichyear")
							 ->setDescription("U3A Marbella and Inland Financial Statement for $whichyear, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Financial result file");
		 // Create a first sheet

		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', "Surname	")
									  ->setCellValue('B1', "Forename")
									  ->setCellValue('C1', "Number")
									  ->setCellValue('D1', "Memb. Type")
									  ->setCellValue('E1', "Date Joined")
									  ->setCellValue('F1', "Date Paid")
									  ->setCellValue('G1', "Amt. Paid");
		
		
		
		
		$response = new stdClass();
		$total_pages=
		$page=0;
		$start = 0; 
		$response->page = 1;
		$response->page = $page;
	/*********************
	* First Read the types of membtype from the members table in alphabetic order with count of them
	* Then for each type 
	* Read all records in membnum order and add to excel
	* At the end of each type add a subtotal row and mark it as a group/outline
	*
	**********************/
	
		 $members =	new Member($this->db);
	
	$mtypes=$members->find(null,array('group'=>'membtype','order'=>'membtype')  );
	$i=2;
	$overalltotal =0;
	foreach($mtypes as $mtype) {
	//echo date('H:i:s') , " getting membertypes  " ,$mtype->membtype, EOL;
	//echo date('H:i:s') , " getting membertypes  " ,"fyear='".$whichyear."' and membtype = '".$mtype->membtype."' ", EOL;
	
	$membercount=$members->count("fyear='".$whichyear."' and membtype = '".$mtype->membtype."' ");
	if ($membercount !=0 ) {
	//echo date('H:i:s') , " getting membertcount " ,var_export($membercount,true), EOL;
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, 'Member Type')
									->setCellValue('D' . $i, $mtype->membtype);
	$i++;
	
	
	$members2 =$members->find("fyear='$whichyear' and membtype = '".$mtype->membtype."' and status in $statustypes",array('order' =>'membtype,membnum'));
	//	$members2=$members->find(array('fyear=?  and membtype = ? and status in ?',$whichyear,$mtype->membtype,$statustypes),array('order' =>'membtype,membnum'));
	
			//echo date('H:i:s') , " getting membertypes 2nd " ,var_export($members2,true), EOL;
		$subtot=0;
			foreach($members2 as $memb) {
			//echo date('H:i:s') , " getting membertypes third " ,$memb->fyear, EOL;
			/*************  Now arr a subtitle row for member type */
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $memb->surname)
												->setCellValue('B' . $i, $memb->forename)
												->setCellValue('C' . $i, $memb->membnum)
												->setCellValue('D' . $i, $memb->membtype)
												->setCellValue('E' . $i, $memb->datejoined)
												->setCellValue('F' . $i, $memb->datepaid)
												->setCellValue('G' . $i, $memb->amtpaidthisyear);
			$subtot += $memb->amtpaidthisyear;
			$objPHPExcel->getActiveSheet()->getRowDimension($i)->setOutlineLevel(1);
			$i++;
			} // foreach member
			//  now add a subtotal row
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "Member Type $mtype->membtype Subtotal")
									->setCellValue('D' . $i, $subtot);
			$overalltotal+=$subtot;
			$i++;
			//echo date('H:i:s') , " getting membertypes 4th " ,$memb->membtype," I= ",$i, EOL;
			
			} // membercount not zero
	}
			//  now add a subtotal row
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "Overall Total ")
									->setCellValue('D' . $i, $overalltotal);
	
	
		// echo date('H:i:s') , " getting membertypes " ,var_export($mtypes,true), EOL;
		 
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle('Financial Report');

			// Set print headers
			$objPHPExcel->getActiveSheet()
				->getHeaderFooter()->setOddHeader('&C&24&K0000FF&B&U&A');
			$objPHPExcel->getActiveSheet()
				->getHeaderFooter()->setEvenHeader('&C&24&K0000FF&B&U&A');

			// Set print footers
			$objPHPExcel->getActiveSheet()
				->getHeaderFooter()->setOddFooter('&R&D &T&C&F&LPage &P / &N');
			$objPHPExcel->getActiveSheet()
				->getHeaderFooter()->setEvenFooter('&L&D &T&C&F&RPage &P / &N');



			// Save Excel 2007 file
			//echo date('H:i:s') , " Write to Excel2007 format" , EOL;
			$callStartTime = microtime(true);

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
			$objWriter->save("downloads/Financial_report_$whichyear.xlsx");
		//	$callEndTime = microtime(true);
		//	$callTime = $callEndTime - $callStartTime;

			//echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
			//echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
			// Echo memory usage
			//echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;			


}
		
} // end of class