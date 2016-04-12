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
	$pdfselect = array(
	//'all'=>array('title'=> "U3A Marbella and Inland - Membership List ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email',paidthisyear as 'Paid' from members where u3ayear='".$u3ayear."' and status='Active' and paidthisyear in ".$paidstatus." order by surname ASC "),
	'all'=>array('title'=> "U3A Marbella and Inland - Membership List ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and paidthisyear in ".$paidstatus." order by surname ASC "),
					'at'=>array('title'=> "U3A Marbella and Inland - Admin Team ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and membtype in ('AT','ATGL') order by surname ASC "),
//					'gl'=>array('title'=> "U3A Marbella and Inland - Group Leaders ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and membtype in ('GL','GLS','ATGL') order by surname ASC "),
					'gl'=>array('title'=> "U3A Marbella and Inland - Group Leaders ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and membtype in ('GL','ATGL') order by surname ASC "),
					'unpaid'=>array('title'=> "U3A Marbella and Inland - Unpaid Members ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and paidthisyear in ('N') order by surname ASC "),
					'willpay'=>array('title'=> "U3A Marbella and Inland - WillPay Members ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and  paidthisyear in ('W') order by surname ASC "),
//					'paid'=>array('title'=> "U3A Marbella and Inland - Paid Members ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and  paidthisyear in ('Y') and membtype in ('M','MJL1')order by surname ASC "),
					'paid'=>array('title'=> "U3A Marbella and Inland - Paid Members ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and  paidthisyear in ('Y') order by surname ASC "),
					'lastyear'=>array('title'=> "U3A Marbella and Inland - Last Year Members ".$lastu3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$lastu3ayear."' and status='Active' and paidthisyear in ".$paidstatus." order by surname ASC "),
					'payers'=>array('title'=> "U3A Marbella and Inland - This Week's Payers since"."\n".$since,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email', amtpaidthisyear as 'Fee', feewhere as 'Who has'  from members where u3ayear='".$u3ayear."' and status in ('Active','Left') and paidthisyear in ('Y') and datepaid > NOW() - INTERVAL '". $dayssince ."' DAY  order by surname ASC "),
					'weekly'=>array('title'=> "U3A Marbella and Inland - This Week\'s Payers since"."\n".$since,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email', amtpaidthisyear as 'Fee', feewhere as 'Who has'  from members where u3ayear='".$u3ayear."' and status in ('Active','Left') and paidthisyear in ('Y') and datepaid > NOW() - INTERVAL '". $dayssince ."' DAY  order by surname ASC "),
		
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
		switch ($theset)
		{case "weekly":
		case "payers":
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
				break;
				
				case "all":
				$attr = array('titleFontSize'=>18, 'titleText'=>$pdfselect[$theset]['title'],
				'tablewidths'=>array(  // change these to get a better fit of columns
					100, // surname 
					90, // forename 
					42, // membnum 
					64, // phone 
					64, // mobile 
					180, // email 
				//	50, // paid
				//	48 /* location 
				)
				); 
				break;
				default :
				$attr = array('titleFontSize'=>18, 'titleText'=>$pdfselect[$theset]['title'],
				'tablewidths'=>array(  // change these to get a better fit of columns
					100, // surname 
					90, // forename 
					42, // membnum 
					64, // phone 
					64, // mobile 
					194, // email 
				//	52.68, // paid
				//	48 /* location 
				)
				); 
				break;
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
		 $mail_content="--".$boundary.NEWLINE."Content-Type: text/plain; ".NEWLINE." charset=\"iso-8859-1\"".NEWLINE."Content-Transfer-Encoding: 7bit".NEWLINE.NEWLINE.$subject." Report Successful...".NEWLINE.NEWLINE."Please see attached for your Report file".NEWLINE.NEWLINE. $file_name .NEWLINE.$mail_attached;
		 return mail($to_emailaddress, $subject, $mail_content, "From: $from_emailaddress".NEWLINE."Reply-To:$from_emailaddress".NEWLINE.$add_header);
		} // end of xmail
		
public function weeklyxmail () {
		$since=strftime("%d %b %Y", time() - (7 * 24 * 60 * 60));
		$f3=Base::instance();
		$options= new Option($this->db);
		$options->initu3ayear();
		$mypdf= new ReportController();
		$dlfilename='downloads/email_list_weekly.pdf';
 
		$mypdf->writeemailpdf($dlfilename,'payers');
		
		$buffer='';
			$f = fopen($dlfilename,'r');
			if(!$f)
				$this->Error('Unable to open input file: '.$dlfilename);
			$buffer=fread($f,(1024*1024));
			fclose($f);
			/**  Get emails for weekly report from optionsu3a table
			***/
			$weeklylist=$options->find("optionname='weeklyemail'");
			foreach ($weeklylist as $weeklymail) {
//	$this->xmail('president@u3a.es', 'laurie@u3a.es','Report for Weekly Joiners since '.$since,$buffer,'email_list_weekly.pdf',"F","0.9"	);
//	$this->xmail('membership@u3a.es', 'laurie@u3a.es','Report for Weekly Joiners since '.$since,$buffer,'email_list_weekly.pdf',"F","0.9"	);
//	$this->xmail('laurie.lyates@gmail.com', 'laurie@u3a.es','Report for Weekly Joiners since '.$since,$buffer,'email_list_weekly.pdf',"F","0.9"	);
	//$this->xmail('laurie2@lyates.com', 'laurie@u3a.es','Report for Weekly Joiners since '.$since,$buffer,'email_list_weekly.pdf',"F","0.9"	);
	//echo "sending email to ",$weeklymail->optionvalue,"\n" ;
	$this->xmail($weeklymail->optionvalue, 'laurie@u3a.es','Report for Weekly Payers since '.$since,$buffer,'email_list_weekly.pdf',"F","0.9"	);
	}		
		$f3->set('view','member/exports.htm'); 
		$f3->set('page_head','Primary Member Lists');
		$f3->set('page_role',$f3->get('SESSION.user_role'))	;	
			 $f3->set('message','');
		
		
		}
public function weeklyreports() {
		$f3=Base::instance();
	$reportset = $f3->get('PARAMS.reportset');
		$since=strftime("%d %b %Y", time() - (7 * 24 * 60 * 60));

		$options= new Option($this->db);
		$options->initu3ayear();
		$u3ayear = $f3->get('SESSION.u3ayear');
		$lastu3ayear = $f3->get('SESSION.lastu3ayear');
		//$mypdf= new ReportController();
		$dlfilename='downloads/email_list_'.$reportset.'.pdf';
 
		$this->writeemailpdf($dlfilename,$reportset);
	$emailinfo = array(
	'all'=>array('title'=> "U3A Marbella and Inland - Membership List ".$u3ayear),
					'at'=>array('title'=> "U3A Marbella and Inland - Admin Team ".$u3ayear),
					'gl'=>array('title'=> "U3A Marbella and Inland - Group Leaders ".$u3ayear),
					'unpaid'=>array('title'=> "U3A Marbella and Inland - Unpaid Members ".$u3ayear),
					'willpay'=>array('title'=> "U3A Marbella and Inland - WillPay Members ".$u3ayear),
					'paid'=>array('title'=> "U3A Marbella and Inland - Paid Members ".$u3ayear),
					'lastyear'=>array('title'=> "U3A Marbella and Inland - Last Year Members ".$lastu3ayear),
					'payers'=>array('title'=> "U3A Marbella and Inland - This Week's Payers since"."\n".$since),
					'weekly'=>array('title'=> "U3A Marbella and Inland - This Week's Payers since"."\n".$since),
					);
					
$emailtitle = $emailinfo[$reportset]['title'];
					
		$buffer='';
			$f = fopen($dlfilename,'r');
			if(!$f)
				$this->Error('Unable to open input file: '.$dlfilename);
			$buffer=fread($f,(1024*1024));
			fclose($f);
			/**  Get emails for weekly report from optionsu3a table
			***/
			$weeklylist=$options->find("optionname='".$reportset."email'");
			foreach ($weeklylist as $weeklymail) {

	$this->xmail($weeklymail->optionvalue, 'laurie@u3a.es',$emailtitle,$buffer,$dlfilename,"F","0.9"	);
	}		
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

		date_default_timezone_set('Europe/Madrid');

		/** Include PHPExcel */
		require_once('vendor/Classes/PHPExcel.php');
		//require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


		// Create new PHPExcel object
		//echo date('H:i:s') , " Create new PHPExcel object" , EOL;
		$admin_logger->write( 'Create new PHPExcel object',$uselog );
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
		//$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(26);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		
		
		$i=2;	
		$styleArray = array(
				'font' => array(
					'bold' => true, 'size' =>14		
				),
				'borders' => array(
				'bottom' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				),
	);	
		
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':G'.$i)->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->setCellValue("B$i","U3A Marbella and Inland Financial Statement for $whichyear");

	
		$i++;
		/************ produced at ***/
		$styleArray = array('font' => array('bold' => false, 'size' =>9	));
		$producedat=date('dS M Y H:i:s');
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':G'.$i)->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->setCellValue("B$i","Generated on $producedat");	
		//$i++;
		$i=10;
		$i++;
		$styleArray = array('font' => array('bold' => true),'borders' => array(	'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN,)));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "Surname	")
									  ->setCellValue('B'.$i, "Forename")
									  ->setCellValue('C'.$i, "Number")
									  ->setCellValue('D'.$i, "Type")
									  ->setCellValue('E'.$i, "Date Joined")
									  ->setCellValue('F'.$i, "Date Paid")
									  ->setCellValue('G'.$i, "Paid");
									  $i++;
									  $i++;
		
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(5, 5);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$headlines = array('Lates'=>0,'Advance'=>0,'Members'=>0);
		
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

	$overalltotal =0;
	/***********************
	* For each member type go through the Active and Left members
	*
	* For the 'free' member types the fyear is always the beginning of the u3ayear so will show as such, ie normally in last year's report
	*
	* If the membtype is 'M' then subdivide according to when they paid
	*
	* Since its all in one financial year it can be 2 different U3A years
	*
	* 1st category is late payers, anyone paying before the rollover date given by optionsu3a.u3a_year_start_month
	*
	* 2nd category are people paying for the following u3ayear, ie after or in optionsu3a.u3a_year_start_month
	*
	* Brian's other category advance subscriptions are now called MJL1 and are show separately in their own sublist
	*	
	***********************/
	$options= new Option($this->db);
	
	$monthoption=$options->find("optionname='u3a_year_start_month'");
	
	$rollovermonth = $monthoption[0]->optionvalue;
	$rolloverdate= mktime(0,0,0,$rollovermonth,1, $whichyear);
	$admin_logger->write( " getting rollovermonth  $rollovermonth", $uselog);
	$admin_logger->write( " getting rolloverdate $rolloverdate", $uselog);
	
	foreach($mtypes as $mtype) {
	//echo date('H:i:s') , " getting membertypes  " ,$mtype->membtype, EOL;
	//echo date('H:i:s') , " getting membertypes  " ,"fyear='".$whichyear."' and membtype = '".$mtype->membtype."' ", EOL;
	
	$membercount=$members->count("fyear='".$whichyear."' and membtype = '".$mtype->membtype."' ");
	if ($membercount !=0 ) {
			if ($i % 40 <2) {
		// Add a page break
		$objPHPExcel->getActiveSheet()->setBreak( 'A' . $i, PHPExcel_Worksheet::BREAK_ROW );
	}
	//echo date('H:i:s') , " getting membertcount " ,var_export($membercount,true), EOL;
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "Member Type $mtype->membtype");
								
	$i++;
	
	
//	$members2 =$members->find("fyear='$whichyear' and membtype = '".$mtype->membtype."' and status in $statustypes",array('order' =>'membtype,membnum'));
	$members2 =$members->find("fyear='$whichyear' and membtype = '".$mtype->membtype."' and status in $statustypes and paidthisyear='Y'",array('order' =>'membtype,membnum'));
	
			//echo date('H:i:s') , " getting membertypes 2nd " ,var_export($members2,true), EOL;
		$subtot=0;
		/***********************
		* For each membertype 
		
		*
		
		************************/
			foreach($members2 as $memb) {
			//echo date('H:i:s') , " getting membertypes third " ,$memb->fyear, EOL;
			/*************  Now arr a subtitle row for member type */
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $memb->surname)
												->setCellValue('B' . $i, $memb->forename)
												->setCellValue('C' . $i, $memb->membnum)
												->setCellValue('D' . $i, $memb->membtype)
												->setCellValue('E' . $i, $memb->datejoined)
												->setCellValue('F' . $i, date("Y-m-d",strtotime($memb->datepaid)))
												->setCellValue('G' . $i, $memb->amtpaidthisyear);
			/****************
			* add into the headline subtotals
			*
			* based on the member type and when it was paid wrt the financial year and u3ayear
			*
			* Only paying types are relevant so M and MJL1
			* M is split into late payers and correct payers
			****************/
			//$admin_logger->write( "getting switch  $mtype->membtype", $uselog);
			switch ($mtype->membtype) {
			case 'M':
			// if datepaid is in the months before the rollover date from optionsu3a.u3a_year_start_month then late payer
			$thedate=strtotime($memb->datepaid);
			if($thedate <$rolloverdate) $headlines['Lates'] +=$memb->amtpaidthisyear;
			else $headlines['Members'] +=$memb->amtpaidthisyear;
			
			//if 
			break;
			case 'MJL1':
			//$admin_logger->write( "getting MJL1 amount $memb->amtpaidthisyear", $uselog);
			$headlines['Advance'] +=$memb->amtpaidthisyear;
			break;
			}
			$subtot += $memb->amtpaidthisyear;
			$objPHPExcel->getActiveSheet()->getRowDimension($i)->setOutlineLevel(1);
																//->setCollapsed(true)
																//->setVisible(false);
			$i++;
				if ($i % 40 == 0) {
		// Add a page break
		$objPHPExcel->getActiveSheet()->setBreak( 'A' . $i, PHPExcel_Worksheet::BREAK_ROW );
	}
			} // foreach member
			//  now add a subtotal row
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "Member Type $mtype->membtype Subtotal")
									->setCellValue('G' . $i, $subtot);
			$overalltotal+=$subtot;
			$i++;
			$i++;
			//echo date('H:i:s') , " getting membertypes 4th " ,$memb->membtype," I= ",$i, EOL;
			
			} // membercount not zero
	}
			//  now add a subtotal row

									//$i++;
									$i=5;
									$j=$i+2;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$j)->getFill()
					->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					->getStartColor()->setARGB('FF92D050'); //146 208 80 // For Summary		

	
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "Late Subscriptions	");								
			$objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $headlines['Lates']);
									$i++;			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "Advance Subscriptions");								
			$objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $headlines['Advance']);
									$i++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "Member Subscriptions");								
			$objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $headlines['Members']);
						$i++;
						$i++;
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "Overall Total ")
									->setCellValue('G' . $i, $overalltotal);
									$i++;
									$i++;							
	
	
		// echo date('H:i:s') , " getting membertypes " ,var_export($mtypes,true), EOL;
		 
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle("Financial Report for $whichyear");

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

				
/****			$styleArray = array(
				'font' => array(
					'bold' => true,
				),
				'borders' => array(
				'top' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				),
	);
		
		$objPHPExcel->getActiveSheet()->getStyle('A2:D4')->applyFromArray($styleArray); // For summary
***/


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