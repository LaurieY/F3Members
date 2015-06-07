<?php

class Option1Controller extends Controller {

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
	public function optiongrid () {
	$f3=$this->f3;
	$trail =	new Trail($this->db);
	$f3->set('page_head','User List');
	$admin_logger = new MyLog('admin.log');
	$admin_logger->write('in fn trail');
	header("Content-type: text/xml;charset=utf-8");
	$page = $_GET['page']; 
	$limit = $_GET['rows']; 
	$sidx = $_GET['sidx']; 
	$sord = $_GET['sord']; 

	$db = mysqli_connect('localhost', $f3->get('db_user'),  $f3->get('db_pass'),$f3->get('db_name')) or die("Connection Error: " . mysqli_error()); 
	// calculate the number of rows for the query. We need this for paging the result 
	$result = mysqli_query($db,"SELECT COUNT(*) AS count FROM trail"); 
	$row = mysqli_fetch_array($result,MYSQL_ASSOC); 
	$count = $row['count']; 
 
// calculate the total pages for the query 
	if( $count > 0 && $limit > 0) { 
              $total_pages = ceil($count/$limit); 
	} else { 
              $total_pages = 0; 
	}	 
 
// if for some reasons the requested page is greater than the total 
// set the requested page to total page 
	if ($page > $total_pages) $page=$total_pages;
 
// calculate the starting position of the rows 
	$start = $limit*$page - $limit;
 
// if for some reasons start position is negative set it to 0 
// typical case is that the user type 0 for the requested page 
	if($start <0) $start = 0; 
	$filters = $f3->get('GET.filters');
	$where = "";
        if (isset($filters)) {
            $filters = json_decode($filters);
            $where = " where ";
            $whereArray = array();
            $rules = $filters->rules;
/********************************/
 $groupOperation = $filters->groupOp;
        foreach($rules as $rule) {

            $fieldName = $rule->field;
           //$admin_logger->write('in fn trail old fieldname = '.$fieldName."\n");
			$admin_logger->write('in fn trail old fielddata = '.$rule->data."\n");
			//$admin_logger->write('in fn trail quoted fielddata = '.$this->db->quote($rule->data)."\n");
			$fieldData =$rule->data;
			//$fieldData =str_replace("'", "",$this->db->quote($rule->data)); // not necessary, the quote only add spurious quoted that I have to remove
			$admin_logger->write('in fn trail new fielddata = '.str_replace("'", "",$fieldData)."\n");
		   
            switch ($rule->op) {
           case "eq":
                $fieldOperation = " = '".$fieldData."'";
				
                break;
           case "ne":
                $fieldOperation = " != '".$fieldData."'";
                break;
           case "lt":
                $fieldOperation = " < '".$fieldData."'";
                break;
           case "gt":
                $fieldOperation = " > '".$fieldData."'";
                break;
           case "le":
                $fieldOperation = " <= '".$fieldData."'";
                break;
           case "ge":
                $fieldOperation = " >= '".$fieldData."'";
                break;
           case "nu":
                $fieldOperation = " = ''";
                break;
           case "nn":
                $fieldOperation = " != ''";
                break;
           case "in":
                $fieldOperation = " IN (".$fieldData.")";
                break;
           case "ni":
                $fieldOperation = " NOT IN '".$fieldData."'";
                break;
           case "bw":
                $fieldOperation = " LIKE '".$fieldData."%'";
                break;
           case "bn":
                $fieldOperation = " NOT LIKE '".$fieldData."%'";
                break;
           case "ew":
                $fieldOperation = " LIKE '%".$fieldData."'";
                break;
           case "en":
                $fieldOperation = " NOT LIKE '%".$fieldData."'";
                break;
           case "cn":
                $fieldOperation = " LIKE '%".$fieldData."%'";
                break;
           case "nc":
                $fieldOperation = " NOT LIKE '%".$fieldData."%'";
                break;
            default:
                $fieldOperation = "";
                break;
                }
            if($fieldOperation != "") $whereArray[] = $fieldName.$fieldOperation;
        }
        if (count($whereArray)>0) {
            $where .= join(" ".$groupOperation." ", $whereArray);
        } else {
            $where = "";
        }
		}
 $where_to_use = $where;

// the actual query for the grid data 
$admin_logger->write('in fn trail where value ='. $where_to_use );
	$SQL = 	"SELECT * FROM trail  ".$where_to_use." ORDER BY $sidx $sord LIMIT $start , $limit";
$admin_logger->write('in fn trail SQL is ='.$SQL );
	$result = mysqli_query($db, $SQL ) or die("Couldn't execute query.".mysql_error()); 
	$s = "<?xml version='1.0' encoding='utf-8'?>";
	$s .=  "<rows>";
	$s .= "<page>".$page."</page>";
	$s .= "<total>".$total_pages."</total>";
	$s .= "<records>".$count."</records>";

   
 
// be sure to put text data in CDATA

	while($row = mysqli_fetch_array($result,MYSQL_ASSOC)) {
		foreach($row as $key => $value)
		{if ($key=='id') {$s .= "<row id='". $value."'>";  }
		else
		{ $s .= "<cell>". "$value"."</cell>";
	  
			}
         //$key holds the table column name
		}
		$s .= "</row>"; 
	
	} 
	$s .= "</rows>"; 
	echo $s;
	
	}
public function optiongrid_0() {
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
	
	}
