public function index(){
		$f3=$this->f3;
		$uselog=$f3->get('uselog');
		$admin_logger = new MyLog('auth.log');
		$admin_logger->write( 'Entering OptionsControllerindex' , $uselog );
		$f3->set('page_head','Amend Options');
		$f3->set('page_role',$f3->get('SESSION.user_role'));
        $f3->set('message', $f3->get('PARAMS.message'));
		
		$f3->set('view','option/optionlist.htm');
		$f3->set('SESSION.lastseen',time()); 		

}

	public   function initu3ayear(){
	$fw=Base::instance();
	//var_export($fw->get('SESSION',false)); //LEY
	if(!$fw->exists('SESSION.u3ayear')) {
  $today = getdate();
	  $thismon= $today['mon'];
	  $thisyear = (string) $today['year'];
	  $lastyear = (string) $today['year'] -1;
	  $nextyear = (string) $today['year'] +1;
	  $this->load('optionname="u3a_year_start_month"');
	$whichmonth = $this->optionvalue;
	  //'select optionvalue from options where optionname ="u3a_year_start_month" ';
	  if ($thismon <$whichmonth)
		$fw->set('SESSION.u3ayear', $lastyear.'-'.$thisyear);
		else
		$fw->set('SESSION.u3ayear',  $thisyear.'-'.$nextyear);
		//print_r($fw->get('u3ayear'));
		}
	return $fw->get('SESSION.u3ayear');
}
public  function initlastu3ayear(){
	$fw=Base::instance();
	if(!$fw->exists('SESSION.lastu3ayear')) {
  $today = getdate();
	  $thismon= $today['mon'];
	  $thisyear = (string) $today['year'];
	  $lastyear = (string) $today['year'] -1;
	  $lastbutoneyear = (string) $today['year'] -2;
	  $whichmonth = $this->optionvalue;
	  if ($thismon <$whichmonth)
		$fw->set('SESSION.lastu3ayear',  $lastbutoneyear.'-'.$lastyear);
		else
		$fw->set('SESSION.lastu3ayear',  $lastyear.'-'.$thisyear);
		}
			return $fw->get('SESSION.lastu3ayear');
}	
	function writeemailpdf($thefile,$theset) {
//require 'vendor/autoload.php';
//$f3 = require('lib/base.php');	//based on http://www.fpdf.org/en/script/script21.php
	//	$f3=$this->f3;

	$admin_logger = new MyLog('admin.log');
	$admin_logger->write('in writeemailpdf',true);
		$f3=Base::instance();
	$uselog=$f3->get('uselog');
	
		$u3ayear = $f3->get('SESSION.u3ayear');
		$lastu3ayear = $f3->get('SESSION.lastu3ayear');
		$admin_logger->write('in writeemailpdf for U3AYEAR = '.$f3->get('SESSION.u3ayear'),true);
		$admin_logger->write('in writeemailpdf for fw = '.var_export($f3,true),true);
	$paidstatus="('Y','N','W')";
	$pdfselect = array('all'=>array('title'=> "U3A Marbella and Inland - Membership List ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and paidthisyear in ".$paidstatus." order by surname ASC "),
					'cm'=>array('title'=> "U3A Marbella and Inland - Committee Members ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and membtype in ('CM','CMS','CMGL') order by surname ASC "),
					'gl'=>array('title'=> "U3A Marbella and Inland - Group Leaders ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and membtype in ('GL','GLS','CMGL') order by surname ASC "),
					'unpaid'=>array('title'=> "U3A Marbella and Inland - Unpaid Members ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and paidthisyear in ('N') order by surname ASC "),
					'willpay'=>array('title'=> "U3A Marbella and Inland - WillPay Members ".$u3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and  paidthisyear in ('W') order by surname ASC "),
					'lastyear'=>array('title'=> "U3A Marbella and Inland - Last Year Members ".$lastu3ayear,'sqlselect'=>"select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$lastu3ayear."' and status='Active' and paidthisyear in ".$paidstatus." order by surname ASC ")
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
		// attributes for the page titles
		//$attr = array('titleFontSize'=>18, 'titleText'=>'First Example Title.');
		//$sql_statement ="select surname as 'Surname',forename as 'Forename',membnum as 'Member Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' ,membtype as 'Member Type',location as 'Location' from members where u3ayear='".$u3ayear."' and status='Active' and paidthisyear in ".$paidstatus." order by membnum ASC";
		//$sql_statement ="select surname as 'Surname',forename as 'Forename',membnum as 'Number',phone as 'Phone',mobile as 'Mobile' ,email as 'Email' from members where u3ayear='".$u3ayear."' and status='Active' and paidthisyear in ".$paidstatus." order by surname ASC ";
		$sql_statement = $pdfselect[$theset]['sqlselect'];
		// Generate report
		$admin_logger->write('in writeemailpdf sql  = '.$sql_statement,$uselog);
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
		$pdf->mysql_report($sql_statement, false, $attr );
		
		$dldir=$f3->get('BASE').$f3->get('downloads');
		$admin_logger->write('in writeemailpdf outputted to  '.$thefile,$uselog);
		//$pdf->Output($dldir.'/email_list_'.$theset.'.pdf',"F");
		$pdf->Output($thefile,"F");

	return true;
		
	}
	public function optiongrid() {
	$f3=$this->f3;
	$options =	new Option($this->db);
	$uselog=$f3->get('uselog');
	$admin_logger = new MyLog('admin.log');
	$admin_logger->write('in fn optiongrid #128 ',$uselog);
	$f3->set('page_head','Option List');
	
	header("Content-type: text/xml;charset=utf-8");
	$page = $_GET['page']; 
	$sidx = $_GET['sidx']; 
	$sord = $_GET['sord']; 
	// get count of records
	echo "<?xml version='1.0' encoding='utf-8'?><rows><page>1</page><total>1</total><records>1</records><row id='1154'><cell>f</cell><cell>c</cell></row></rows>";
	
	

	
	}	
