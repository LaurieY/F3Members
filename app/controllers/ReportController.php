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
					'weekly'=>array('title'=> "U3A Marbella and Inland - This Week's Joiners since"."\n".$since,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email', amtpaidthisyear as 'Fee', feewhere as 'Who has'  from members where u3ayear='".$u3ayear."' and status in ('Active','Left') and paidthisyear in ('Y') and datepaid > NOW() - INTERVAL '". $dayssince ."' DAY  order by surname ASC "),
		
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
		
} // end of class