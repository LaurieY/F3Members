<?php

class MyClass {
 public function __construct( ) {
 $admin_logger = new MyLog('admin.log');
	$admin_logger->write('in writeemailpdf construct',true);
 }

	function writeemailpdf($thefile,$theset) {
//require 'vendor/autoload.php';
//$f3 = require('lib/base.php');	//based on http://www.fpdf.org/en/script/script21.php
	//	$f3=$this->f3;
	
	$admin_logger = new MyLog('admin.log');
	$admin_logger->write('in writeemailpdf',true);
	$fw=Base::instance();	
	$uselog=$fw->get('uselog');
	
		$u3ayear = Member::getu3ayear();
		$lastu3ayear = Member::getlastu3ayear();
	$paidstatus="('Y','N','W')";
	$pdfselect = array('all'=>array('sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and paidthisyear in ".$paidstatus." order by surname ASC "),
					'cm'=>array('sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and membtype in ('CM','CMS','CMGL') order by surname ASC "),
					'gl'=>array('sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and membtype in ('GL','GLS','CMGL') order by surname ASC "),
					'unpaid'=>array('sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and paidthisyear in ('N') order by surname ASC "),
					'willpay'=>array('sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and  paidthisyear in ('W') order by surname ASC "),
					'lastyear'=>array('sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$lastu3ayear."' and status='Active' and paidthisyear in ".$paidstatus." order by surname ASC ")
					);
					

		$pdf = new PDF('P','pt','A4');
		$pdf->SetFont('Arial','',10);
		$pdf->SetMargins(17,38,17);


		// ************  change the below to establish the database connection.   *********
		$host = 'localhost';
		$username = $fw->get('db_user');
		$password = $fw->get('db_pass');
		$database = $fw->get('db_name');
		$connok=$pdf->connect($host, $username, $password, $database);
		$admin_logger->write('in writeemailpdf after connect = '.$connok,$uselog);
		//$admin_logger->write('in writeemailpdf sql  = '.$pdfselect['all']['sqlselect'],$uselog);
		// attributes for the page titles
		//$attr = array('titleFontSize'=>18, 'titleText'=>'First Example Title.');
		//$sql_statement ="select surname as 'Surname',forename as 'Forename',membnum as 'Member Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' ,membtype as 'Member Type',location as 'Location' from members where u3ayear='".$u3ayear."' and status='Active' and paidthisyear in ".$paidstatus." order by membnum ASC";
		//$sql_statement ="select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and paidthisyear in ".$paidstatus." order by surname ASC ";
		$sql_statement = $pdfselect[$theset]['sqlselect'];
		// Generate report
		$admin_logger->write('in writeemailpdf sql  = '.$sql_statement,$uselog);
		$attr = array('titleFontSize'=>18, 'titleText'=>'U3A Marbella and Inland - Membership List '.$u3ayear,
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
		$pdf->mysql_report($sql_statement, false, $attr );
		
		$dldir=$fw->get('BASE').$fw->get('downloads');
		$admin_logger->write('in writeemailpdf outputted to  '.$thefile,$uselog);
		//$pdf->Output($dldir.'/email_list_'.$theset.'.pdf',"F");
		$pdf->Output($thefile,"F");

	return true;
		
	}
	
	} //end of Class MyClass