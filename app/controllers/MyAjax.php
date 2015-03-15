<?php

class MyAjax {
	protected $f3;
	protected $db;

	function beforeroute() {
//$this->f3->set('message','');
	}

	function afterroute() {
//		echo Template::instance()->render('layout.htm');	
	}

	function __construct() {

        $f3=Base::instance();

        $db=new DB\SQL(
            $f3->get('db_dns') . $f3->get('db_name'),
            $f3->get('db_user'),
            $f3->get('db_pass')
        );	

		$this->f3=$f3;
		$this->db=$db;
	}
	public function example()
	 {
	 $f3=$this->f3;
	 $this->f3->set('page_head','User List');
//$this->$f3->config('config/config.ini');		
      //  echo  'ELLO GET AJAX';	
//print_r($this->db);
header("Content-type: text/xml;charset=utf-8");
 $page = $_GET['page']; 
 $limit = $_GET['rows']; 
 $sidx = $_GET['sidx']; 
 $sord = $_GET['sord']; 
 //$fred = $this->f3->get('db_user');
 $db = mysql_connect('localhost', $this->f3->get('db_user'),  $this->f3->get('db_pass')) or die("Connection Error: " . mysql_error()); 
 mysql_select_db($this->f3->get('db_name')) or die("Error connecting to db."); 
 // calculate the number of rows for the query. We need this for paging the result 
$result = mysql_query("SELECT COUNT(*) AS count FROM members"); 
$row = mysql_fetch_array($result,MYSQL_ASSOC); 
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
 
// the actual query for the grid data 
//$SQL = "SELECT id,surname	, forename, membnum FROM members ORDER BY $sidx $sord LIMIT $start , $limit"; 
//$SQL = "SELECT id, FROM members ORDER BY $sidx $sord LIMIT $start , $limit"; 
 $SQL = "SELECT id,surname ,forename,membnum ,phone,mobile,email,membtype,location,paidthisyear,amtpaidthisyear,datejoined FROM members ORDER BY $sidx $sord LIMIT $start , $limit"; 
$result = mysql_query( $SQL ) or die("Couldn't execute query.".mysql_error()); 
$s = "<?xml version='1.0' encoding='utf-8'?>";
$s .=  "<rows>";
$s .= "<page>".$page."</page>";
$s .= "<total>".$total_pages."</total>";
$s .= "<records>".$count."</records>";
 
// be sure to put text data in CDATA
/*
while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
    $s .= "<row id='". $row['id']."'>";  
	
    $s .= "<cell>". $row['surname']."</cell>"; 
	$s .= "<cell>". $row['forename']."</cell>";
	$s .= "<cell>". $row['membnum']."</cell>"; 	
   // $s .= "<cell>". $row['mobile']."</cell>";
	//$s .= "<cell>". $row['phone']."</cell>";
	//$s .= "<cell>". $row['email']."</cell>";
    $s .= "</row>";
}  
*/
 while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
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
} // end of function  example
} // end of class