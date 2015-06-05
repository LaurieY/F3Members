<?php

class AjaxController extends Controller {
//	protected $f3;
//	protected $db;

	function beforeroute() {
//$f3->set('message','');
	$f3=$this->f3;
	$auth_logger = new MyLog('auth.log');
	$auth_logger->write( 'Entering AjaxController beforeroute URI= '.$f3->get('URI'  ) );
	$auth_logger->write( 'Entering AjaxController beforeroute user role = '.$f3->get('SESSION.user_role' ));
	$auth_logger->write( 'Entering AjaxController beforeroute u3ayear = '.$f3->get('SESSION.u3ayear' ),true);
	$auth_logger->write( 'Entering AjaxController beforeroute u3ayear = '.var_export($f3,true ),true);
	if (!$f3->get('SESSION.user_id') ) {
		$this->f3->reroute('/login');
	}
/*	if ($f3->get('SESSION.user_role') =='user' ) {//don't allow any changes for standard user so payments not allowed
	$f3->set('message','Cannot access this page');
	$auth_logger->write( 'Entering AjaxController beforeroute reroute to /');
			
			$this->f3->reroute('/login');
	}
	*/
	
	

	}

	function afterroute() {
//		echo Template::instance()->render('layout.htm');	
	}

/*	function __construct() {

        $this->f3=Base::instance();
$f3=$this->f3;
        $db=new DB\SQL(
            $f3->get('db_dns') . $f3->get('db_name'),
            $f3->get('db_user'),
            $f3->get('db_pass')
        );	

		$this->f3=$f3;	
		$this->db=$db;
	}  */
	
	/****************** Return the audit trail table */
public function trail () {
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
public function members() //for POST membergrid
	 {
	 $f3=$this->f3;
		 $members =	new Member($this->db);
	 $uselog=$f3->get('uselog');
	
	 $admin_logger = new MyLog('admin.log');
	$admin_logger->write('in fn members #203 u3ayear='.$f3->get('SESSION.u3ayear'),$uselog);
	 $f3->set('page_head','User List');
	 //$admin_logger->write('in fn members '.get_class($this->db)." Parent is ".get_parent_class($this->db)."\n");
	//$admin_logger->write( "In Ajax POST membergrid fn members Session user_id = ".$f3->get('SESSION.user_id')); 

/**	$class_methods = get_class_methods('DB\SQL');
	foreach ($class_methods as $method_name) {
   //$admin_logger->write('in fn members class methods '.$method_name."\n");
	}**/
	
	//$admin_logger->write('GET _search = '.$_GET['_search']."\n");
if ($f3->get('GET._search')=='true'){
// set up filters
$filters = $f3->get('GET.filters');
$admin_logger->write('in fn members filters= '.$filters,$uselog);

$where = "";
    /****    if (isset($filters)) { // ********************filters NO LONGER USED with local grid
            $filters = json_decode($filters);
            $where = " where ";
            $whereArray = array();
            $rules = $filters->rules;

 $groupOperation = $filters->groupOp;
        foreach($rules as $rule) {

            $fieldName = $rule->field;
           //$admin_logger->write('in fn members old fieldname = '.$fieldName."\n");
			$admin_logger->write('in fn members old fielddata = '.$rule->data."\n");
			//$admin_logger->write('in fn members quoted fielddata = '.$this->db->quote($rule->data)."\n");
			$fieldData =$rule->data;
			//$fieldData =str_replace("'", "",$this->db->quote($rule->data)); // not necessary, the quote only add spurious quoted that I have to remove
			$admin_logger->write('in fn members new fielddata = '.str_replace("'", "",$fieldData)."\n");
		   //$fieldData = mysqli_real_escape_string($members,$rule->data); 
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


        }     *************/
	$admin_logger->write('in fn members where= '.$where,$uselog);
	/**********************  Now get the resulting xml via SWL using this where selection ******/
	$whh =	$this->getresult_where($where);
	
	$admin_logger->write('in fn members where result = '.$whh."\n");
echo $whh;
}
else {
$u3ayear = $f3->get('SESSION.u3ayear');
$admin_logger->write("in fn members where result #310 , u3ayear=".$u3ayear ,$uselog);
echo $this->getresult_where("where u3ayear='".$u3ayear."' and status='Active'");
}  //end of else of _search
} // end of function  members


private function getresult_where( $where_to_use)
{
 $f3=$this->f3;
  $admin_logger = new MyLog('admin.log');
	$uselog=$f3->get('uselog');
  $admin_logger->write('in getresult_where $where_to_use = '.$where_to_use,$uselog);
header("Content-type: text/xml;charset=utf-8");
 $page = $_GET['page']; 
 
 
	$sidx = $_GET['sidx']; 
	$sord = $_GET['sord']; 
	$extrasort = ', membnum DESC';
 //$fred = $f3->get('db_user');
	$db = mysqli_connect('localhost', $f3->get('db_user'),  $f3->get('db_pass'),$f3->get('db_name')) or die("Connection Error: " . mysql_error()); 
 //mysqli_select_db($f3->get('db_name')) or die("Error connecting to db."); 
 // calculate the number of rows for the query. We need this for paging the result 
	$result = mysqli_query($db,"SELECT COUNT(*) AS count FROM members ".$where_to_use); 
	$row = mysqli_fetch_array($result,MYSQL_ASSOC); 
	$count = $row['count']; 
	$limit = $count;  // temporary force all rows
//  $limit = $_GET['rows'];  // temporary comment out  to force all rows need this if non-local grid, i.e. loadOnce=false

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
 


/************Get Total paid for this selection  ************/

 
 $SQL_total="select sum(amtpaidthisyear)  as amt from members ".$where_to_use;
 $result = mysqli_query($db, $SQL_total ) or die("Couldn't execute query.".mysql_error()); 
 $row = mysqli_fetch_array($result,MYSQL_ASSOC); 
 $amt_total = $row['amt'];
 $members =	new Member($this->db);
 $fytotals = $members->gettotals(); // this will return an assoc array
 $amt_total_lastfy = $fytotals['lastfy'];
 $amt_total_thisfy = $fytotals['thisfy'];
 // the actual query for the grid data 
 // Fetch extra columns to allow for the icons columns in the payments grid
 $SQL = "SELECT id,surname ,forename,membnum ,phone,mobile,email,membtype,location,paidthisyear,amtpaidthisyear,feewhere,1,2,3,datejoined FROM members  ".$where_to_use." ORDER BY $sidx $sord ". $extrasort. " LIMIT $start , $limit"; 
//$admin_logger->write('in getresult_where SQL = '. $SQL."\n");
 $result = mysqli_query( $db,$SQL ) or die("Couldn't execute query.".mysql_error()); 
$s = "<?xml version='1.0' encoding='utf-8'?>";
$s .=  "<rows>";
$s .= "<page>".$page."</page>";
$s .= "<total>".$total_pages."</total>";
$s .= "<records>".$count."</records>";

$s .= '<userdata name="forename">Total LastFY</userdata>';   # name = target column's name
$s .= '<userdata name="phone">'.$amt_total_lastfy.'</userdata>'; 
$s .= '<userdata name="email">Total ThisFY</userdata>';   # name = target column's name
$s .= '<userdata name="location">'.$amt_total_thisfy.'</userdata>'; 
   
 
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

$admin_logger->write('in getresult_where #415 result = '.$s,$uselog);
	return $s;

	
}
public function users()
	 {
	 $f3=$this->f3;
	 
	 $f3->set('page_head','User List');

header("Content-type: text/xml;charset=utf-8");
 $page = $_GET['page']; 
 $limit = $_GET['rows']; 
 $sidx = $_GET['sidx']; 
 $sord = $_GET['sord']; 
 //$fred = $f3->get('db_user');
 $db = mysqli_connect('localhost', $f3->get('db_user'),  $f3->get('db_pass'),$f3->get('db_name')) or die("Connection Error: " . mysql_error()); 
 //mysqli_select_db($f3->get('db_name')) or die("Error connecting to db."); 
 // calculate the number of rows for the query. We need this for paging the result 
$result = mysqli_query($db,"SELECT COUNT(*) AS count FROM mem_users"); 
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
 
// the actual query for the grid data 
//$SQL = "SELECT id,surname	, forename, membnum FROM members ORDER BY $sidx $sord LIMIT $start , $limit"; 
//$SQL = "SELECT id, FROM members ORDER BY $sidx $sord LIMIT $start , $limit"; 
 $SQL = "SELECT id,username ,email,role FROM mem_users ORDER BY $sidx $sord LIMIT $start , $limit"; 
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
} // end of function  users
public function edituser()
	 {
	 $f3=$this->f3; 
	 	$admin_logger = new MyLog('admin.log');
	$admin_logger->write('in edituser');
	// $memuser = new DB\SQL\Mapper($this->db, 'mem_users',array("id","username","email","role")); 
	// $f3->get('POST.oper');
	$trail = new Trail($this->db);  // audit trail
	  //$this->f3->set('mem_users',$user->all());
	// echo (' POST.oper = '.$f3->get('POST.oper'));
	$mem_user =	new User($this->db);
 $f3->set('mem_user',$mem_user);
	 switch ($f3->get('POST.oper')) {
    case "add":  //**********************   ADD a member  force paid ==Y when adding a member, else why ?
	$temptrail= array();
        // do mysql insert statement here
		$mem_user->copyfrom('POST');
		$salt=$f3->get('security_salt'); 
		$encrypt_pwd =crypt($mem_user->password, '$2y$12$' . $salt); //generate the password
		
		$admin_logger->write('in edituser uname '.$mem_user->username);
		$admin_logger->write('in edituser pwd '.$mem_user->password);
		$mem_user->password = $encrypt_pwd ;
		$trail->surname=$mem_user->username;
		$admin_logger->write('in edituser pwd '.$mem_user->password);
		$admin_logger->write('in edituser pwd'.$mem_user->password."##\n");
		$mem_user->save();
		$trail->change="add";
		$trail->editor=$f3->get('SESSION.user_id'  );
		$trail->save();
    break;
    case "edit":
		  
		  
		 // $f3->get('mem_user')->load(array('id =:id',array(':id'=> $f3->get('POST.id')) ) );
		  $mem_user->load(array('id =:id',array(':id'=> $f3->get('POST.id')) ) );
	$admin_logger->write('in edituser '.$f3->get('mem_user')->username);
	  $mem_user->copyfrom('POST');
	
	  $mem_user->update();
        // do mysql update statement here
	//	/
    break;
    case "del":
	$mem_user->load(array('id =:id',array(':id'=> $f3->get('POST.id')) ) );
	
		$temptrail= array();
		//$mem_user->copyto('temptrail');
		//$trail->copyfrom('temptrail');	
		$trail->surname=$mem_user->username;
		$admin_logger->write('in deluser trail Surname '.$trail->surname);
		$admin_logger->write('in deluser trail editor/user_id will be'.$f3->get('SESSION.user_id'  ));
		$trail->change="del";
		$trail->editor=$f3->get('SESSION.user_id'  );
		/*  now get all the details of the members entry into the trail entry  */
		$mem_user->erase();
		$trail->id='';
		$trail->created_at=date("Y-m-d H:i:s");
		
		$trail->save();
	
        // do mysql delete statement here
    break;
}
	// echo $f3->get('POST.oper');
	}

public function editmember()
	 {
	 $f3=$this->f3; 
	 	$admin_logger = new MyLog('admin.log');
		$f3->set('admin_log',$admin_logger);
	$admin_logger->write('in editmember');

	//$members1 =	new Member($this->db);
	$members =	new Member($this->db);
	$trail = new Trail($this->db);  // audit trail
 $f3->set('members',$members);
	 switch ($f3->get('POST.oper')) {
    case "add":
    
		
		/******  Find the next membership number as the highest+1**/
	
	
	
	$result=$this->db->exec('SELECT membnum FROM members order by membnum DESC LIMIT 1'); 
	

	$admin_logger->write('in addmember db log = '.$this->db->log()."\n");


	$admin_logger->write('in addmember maxmembnum row = '.$result[0]['membnum']."\n");

	$max_membnum = ((int) $result[0]['membnum'])+1;

		$members->copyfrom('POST');
		$admin_logger->write('in addmember maxmembnum = '.$members->membnum."\n");
		$members->membnum=$max_membnum;
		
		
		$admin_logger->write('in addmember paidthisyear = '.$members->paidthisyear);
		if ($members->paidthisyear=="Y")	{ 
		$thismember= $members->membnum;
		/***  calculate the amount paid  if added as zero ******/
		if ($members->amtpaidthisyear> 0) {$admin_logger->write('in addmember amountpaid = '.$members->amtpaidthisyear);
			}	//amount specified to use that
		
		else { // no amount specified use reference table amount
		$admin_logger->write('in addmember NO amount paidthisyear ');
		$feespertypes = new \DB\SQL\Mapper($this->db, 'feespertypes');
		$feespertypes->load(array('membtype =:membtype',array(':membtype'=> $f3->get('POST.membtype')) ) );
		$feetopay = $feespertypes->firstyearfee;
		$members->amtpaidthisyear = $feetopay;
		$admin_logger->write('in addmember amount paidthisyear ='. $feetopay);
		}}
		$members->datepaid=date("Y-m-d H:i:s");
		$members->datejoined=date("Y-m-d H:i:s");
		$admin_logger->write('in addmember surname '.$members->surname);
		$admin_logger->write('in addmember Forename '.$members->forename);
		
		$trail->copyfrom('POST');	
		$admin_logger->write('in addmember trail Surname '.$trail->surname);
		$admin_logger->write('in addmember trail editor/user_id will be '.$f3->get('SESSION.user_id'  ));

		$members->add();
		$admin_logger->write('in addmember db log = '.$this->db->log()."\n");
		
		$this->logtrail($members,$trail,"add");
		
    break;
    case "edit":   //************************************ EDIT **//
	$members->load(array('id =:id',array(':id'=> $f3->get('POST.id')) ) ); //this did work but its not the same as the paid code
		$temptrail= array();
		$members->copyto('temptrail');
		$trail->copyfrom('temptrail');	

		$this->logtrail($members,$trail,"editfrom");
		$trail->reset();
		$uselog=$f3->get('uselog');	
		$admin_logger->write('in editmember before get_amt_paid membnum '.$members->membnum.' paidthis year '.$members->paidthisyear,$uselog);

		/*********IF the field paidthisyear has been changed from N or W  to Y then also update the amtpaidthisyear using feespertypes table *****/
		$oldmembtype=$members->membtype;
		$members->amtpaidthisyear=$this->get_amt_paid($members,($f3->get('POST.paidthisyear')=="Y"));
		/*****  if member type changed to M or MJLx then set Paid to N amd amt paid to zero ***/
		if ($oldmembtype!=$f3->get('POST.membtype')) { // change of membertype
		/*********IF the field membtype has changed to a paying one then change the payment status to N and amtpaid to zero ***/
		$admin_logger->write('in editmember with change of membtype from  '.$oldmembtype .' to  '.$members->membtype,$uselog);
						
			if (strpos($members->membtype,'M', 0) === 0) { //new membtype starts with M so should be some fees so zeroise and set not paid for safety 
			$admin_logger->write('in editmember new membtype starts with M  '.$members->membtype,$uselog);

			$members->amtpaidthisyear=0;
			$members->paidthisyear='N';
			
				}
				else { //if  to a non paying ie not start with M se paid ='Y' and amtpaid to zero
				$members->amtpaidthisyear=0;
			$members->paidthisyear='Y';
				
				}
		}	

		$admin_logger->write('in editmember after get_amt_paid '.$members->surname.' membnum '.$members->membnum.' amtpaidthis year '.$members->amtpaidthisyear,$uselog);
		$admin_logger->write('In editmember membnum is '.$members->membnum. ' and of type '.gettype($members->membnum),$uselog);
	
	
	//var_dump($members);  //LEY dumps in the http response
		$temptrail= array();
		$members->copyto('temptrail');
		$trail->copyfrom('temptrail');	
		$this->logtrail($members,$trail,"editto");
		
		$members->update();
		
	//$xnum= $members->membnum;
	//$admin_logger->write('In editrow xnum is '.$xnum. ' and of type '.gettype($xnum),$uselog);

		//xpaid= $members->paidthisyear;
		//$xpay= $members->amtpaidthisyear;
   //echo "membnum:".$xnum.",paidthisyear:".$xpaid.",amtpaidthisyear:".$xpay;
	// $arr = array('membnum' => $xnum, 'paidthisyear' => $xpaid, 'amtpaidthisyear' => $xpay);
		$arr=$members->cast();
		$arrencoded= json_encode($arr);
		$admin_logger->write('In editrow echo '.$arrencoded,$uselog);
	//$admin_logger->write('in editmember after jsonencode '.$arrencoded);
		echo $arrencoded;
        // do mysql update statement here
	//	/
    break;
    case "del":
        // do mysql delete statement here
		$members->load(array('id =:id',array(':id'=> $f3->get('POST.id')) ) );
		$temptrail= array();
		$members->copyto('temptrail');
		$trail->copyfrom('temptrail');	
		$admin_logger->write('in delmember trail Surname '.$trail->surname);
		$admin_logger->write('in delmember trail editor/user_id will be'.$f3->get('SESSION.user_id'  ));
		$this->logtrail($members,$trail,"del");
		//$trail->editor=$f3->get('SESSION.user_id'  );
		/*  now get alll the details of the members entry into the trail entry  */
		$members->status='Deleted';
		$members->update();
    break;
}
	// echo $f3->get('POST.oper');
	}
	/**************** get_amt_paid ******/
 function  get_amt_paid($members,$topay) {

		$f3=$this->f3; 
		$uselog=$f3->get('uselog');
		$admin_logger=$f3->get('admin_log');
		$wasnotpaid=false;
		if ($members->paidthisyear!="Y")	{$wasnotpaid=true;}
	/**** now fetch the existing row to check if paidthisyear is about to change ****/
	$admin_logger->write('in get_amt_paid for paidthisyear = '.$members->paidthisyear,$uselog);
	$admin_logger->write('in get_amt_paid for wasnotpaid = '.$wasnotpaid,$uselog);
	$admin_logger->write('in get_amt_paid for topay = '.$topay,$uselog);	
	$admin_logger->write('In get_amt_paid1 membnum is '.$members->membnum. ' and of membtype '.$members->membtype,$uselog);
		//$thismember= $members->membnum;
		$members->copyfrom('POST');
	// ********* calculate amount paid
	//$feespertpes =	new Feespertypes($this->db);
		$feespertypes = new \DB\SQL\Mapper($this->db, 'feespertypes');
		$feespertypes->load(array('membtype =:membtype',array(':membtype'=> $f3->get('POST.membtype')) ) );
	$admin_logger->write('in get_amt_paid /feespertype ='.$feespertypes->membtype.'  and feetopay '.$feespertypes->feetopay,$uselog);
	$admin_logger->write('In get_amt_paid2 membnum is '.$members->membnum ,$uselog);
		//$feetopay = $feespertypes->feetopay;
		if($wasnotpaid && $topay)  {
		//**********************look to see if its 1st year or not, i.e. if joined date since last July 1
		$djoined = explode('-',$members->datejoined); // yyyy-mm-dd
		$djoinedmk=mktime(0,0,0,$djoined[1],$djoined[2],$djoined[0]);  //mm dd yyy
		//$dnow = date();
		if( date("m") >6) { $dtest = mktime(0,0,0,6,1,date("Y")); } // recent July
		else {$dtest = mktime(0,0,0,6,1,date("Y")-1);
		}
		if($djoinedmk>$dtest) return($feespertypes->firstyearfee);
		else return($feespertypes->feetopay);
		
		}
		// if Changing from a non paying to a paying set paid status to N?? and value is already zero
		
		else return($members->amtpaidthisyear); //
		
		
		//if(!$wasnotpaid && ($members->paidthisyear=="N")) { return(0);}
	
}
/*****  when an edit specifically changes the amount paid in Member Payments page   *********/
function amtpaid() {
 $f3=$this->f3; 
 	$members =	new Member($this->db);
	$trail = new Trail($this->db);  // audit trail
		$members->load(array('membnum =:id',array(':id'=> $f3->get('POST.membnum')) ));
		$temptrail= array();
		$members->copyto('temptrail');
		$trail->copyfrom('temptrail');	
		$this->logtrail($members,$trail,"amtpaidfrom");
		$members->amtpaidthisyear=$f3->get('POST.amtpaidthisyear');
		$members->paidthisyear=$f3->get('POST.paidthisyear');
		$members->feewhere=$f3->get('POST.feewhere');
		$trail->reset();
		$this->logtrail($members,$trail,"amtpaidto");
		$members->update();
		$xnum= $members->membnum;
		$xpay= $members->amtpaidthisyear;
		$xpaid= $members->paidthisyear;
		$xwhere= $members->feewhere;
		$fytotals = $members->gettotals();
		$arr = array('membnum' => $xnum, 'paidthisyear' => $xpaid, 'amtpaidthisyear' => $xpay,'feewhere' => $xwhere,'lastfytotal'=>$fytotals['lastfy'],'thisfytotal'=>$fytotals['thisfy']);
	 	$arrencoded= json_encode($arr);
		
		
	 //$admin_logger->write('in editmember after jsonencode '.$arrencoded);
   echo $arrencoded;
}	
/*****  when an edit specifically changes the location of the fee in Member Payments page   *********/
function feewhere() {
 $f3=$this->f3; 
 	$members =	new Member($this->db);
		$members->load(array('membnum =:id',array(':id'=> $f3->get('POST.membnum')) ));
		$members->amtpaidthisyear=$f3->get('POST.amtpaidthisyear');
		$members->paidthisyear=$f3->get('POST.paidthisyear');
		$members->feewhere=$f3->get('POST.feewhere');
		
		$members->update();
		$xnum= $members->membnum;
		$xpay= $members->amtpaidthisyear;
		$xpaid= $members->paidthisyear;
		$xwhere= $members->feewhere;
		$fytotals = $members->gettotals();
		$arr = array('membnum' => $xnum, 'paidthisyear' => $xpaid, 'amtpaidthisyear' => $xpay,'feewhere' => $xwhere,'lastfytotal'=>$fytotals['lastfy'],'thisfytotal'=>$fytotals['thisfy']);
	 	$arrencoded= json_encode($arr);
	 //$admin_logger->write('in editmember after jsonencode '.$arrencoded);
   echo $arrencoded;
}	
function markpaid() { 
	 $f3=$this->f3; 
	$uselog=$f3->get('uselog');
	
	 	$admin_logger = new MyLog('admin.log');
		$admin_logger->write('In markpaid uselog ='.$uselog.' variable uselog= '.$f3->get('uselog'),$uselog);
		$f3->set('admin_log',$admin_logger);
		//$admin_logger->write('in markpaid membnum='.$this->f3->get('POST.membnum') );
		$members =	new Member($this->db);
		$members->load(array('membnum =:id',array(':id'=> $f3->get('POST.membnum')) ));
		$admin_logger->write('in markpaid after get_amt_paid '.$members->surname.' membnum '.$members->membnum.' paidthis year '.$members->paidthisyear,$uselog);
		$admin_logger->write('In markpaid membnum is '.$members->membnum. ' and of type '.gettype($members->membnum),$uselog);
	
	
		
		$members->amtpaidthisyear=$this->get_amt_paid($members,true);
		$members->paidthisyear='Y';
		//$thismember= $members->membnum;
		$admin_logger->write('end of markpaid membnum='.$members->membnum." paid= ".$members->paidthisyear." amtpaid = ".$members->amtpaidthisyear ,$uselog);
		//$admin_logger->write('In markpaid membnum is '.$members->membnum. ' and of type '.gettype($members->membnum));
		$members->datepaid=date("Y-m-d H:i:s");
		$members->update();
		$this->logtrail($members,new Trail($this->db),"paid");

		$xnum= $members->membnum;
		//$admin_logger->write('In markpaid xnum is '.$xnum. ' and of type '.gettype($xnum));
		$xpaid= $members->paidthisyear;
		$xpay= $members->amtpaidthisyear;
		$fytotals = $members->gettotals();
		$arr = array('membnum' => $xnum, 'paidthisyear' => $xpaid, 'amtpaidthisyear' => $xpay,'lastfytotal'=>$fytotals['lastfy'],'thisfytotal'=>$fytotals['thisfy']);
	 	 $arrencoded= json_encode($arr);
	 $admin_logger->write('in editmember after jsonencode '.$arrencoded,$uselog);
		echo $arrencoded;
   //echo json_encode($arr);
	}
	
function markwillpay() { 
	 $f3=$this->f3;
	 $members =	new Member($this->db);
	$members->load(array('membnum =:id and u3ayear = :u3ayear',  ':id'=> $f3->get('POST.membnum'),'u3ayear'=> $f3->get('SESSION.u3ayear')) ); 
	$members->amtpaidthisyear=0;
	$members->paidthisyear='W';	
	$members->feewhere = $f3->get('POST.feewhere');
	$members->update();
	$this->logtrail($members,new Trail($this->db),"willpay");
	$xnum= $members->membnum;
   
   $xpaid= $members->paidthisyear;
   $xpay= $members->amtpaidthisyear;
 // add new total to the return
 $fytotals = $members->gettotals(); // this will return an assoc array
 
	 $arr = array('membnum' => $xnum, 'paidthisyear' => $xpaid, 'amtpaidthisyear' => $xpay,'lastfytotal'=>$fytotals['lastfy'],'thisfytotal'=>$fytotals['thisfy']);
	$arrencoded= json_encode($arr);
	
   echo $arrencoded;
	 }
function markunpay() { 
	 $f3=$this->f3;
	 $members =	new Member($this->db);
	$members->load(array('membnum =:id and u3ayear = :u3ayear',  ':id'=> $f3->get('POST.membnum'),'u3ayear'=> $f3->get('SESSION.u3ayear') )); 
	$members->amtpaidthisyear=0;
	$members->paidthisyear='N';	
	$members->feewhere = $f3->get('POST.feewhere');
	$members->update();
	$this->logtrail($members,new Trail($this->db),"unpay");
	$xnum= $members->membnum;
   
   $xpaid= $members->paidthisyear;
   $xpay= $members->amtpaidthisyear;
 // add new total to the return
 $fytotals = $members->gettotals(); // this will return an assoc array
 
	 $arr = array('membnum' => $xnum, 'paidthisyear' => $xpaid, 'amtpaidthisyear' => $xpay,'lastfytotal'=>$fytotals['lastfy'],'thisfytotal'=>$fytotals['thisfy']);
	$arrencoded= json_encode($arr);
	
   echo $arrencoded;
	 }
function logtrail($members,$trail,$action) {
		$f3=$this->f3;
		$temptrail= array();
		$members->copyto('temptrail');
		$trail->copyfrom('temptrail');	
		$trail->change=$action;
		$trail->editor=$f3->get('SESSION.user_id'  );
		//$trail->membnum=$members->membnum;  //new number was calculated for the $member sql when an add
		//$trail->amtpaidthisyear=$members->amtpaidthisyear;
		//$trail->datepaid=$members->datepaid;
		//$trail->datejoined=$members->datejoined;
		//$admin_logger->write('in logtrail trail editor now  '.$trail->editor);
		$trail->id='';
		$trail->created_at=date("Y-m-d H:i:s");
		$trail->save();

}
function getfeespertypes() { //return all the contents of the table feespertypes in json format for localstorage to use
		$f3=$this->f3;
		$uselog=$f3->get('uselog');
		$admin_logger = new MyLog('admin.log');
		$admin_logger->write('in getfeespertypes uselog= '.$uselog,true);
		$feefields='membtype,feetopay,firstyearfee,acyear';
		$thisacyear = $f3->get('SESSION.u3ayear');
		$admin_logger->write('in getfeespertypes uselog= '.$uselog. ' academic year = '.$thisacyear,true);
		$feespertypes = new \DB\SQL\Mapper($this->db, 'feespertypes',$feefields);
		$feespertypes->load(array('acyear =:acyear',array(':acyear'=> $thisacyear) ), array('limit'=>100) );
		
		$arr=  array();
		while (!$feespertypes->dry ()) {// gets dry when we passed the last record
		$arr1 = $feespertypes->cast();
		$arr[$arr1["membtype"]]=$arr1;

		$feespertypes->next();
		}
		//$admin_logger->write('in getfeespertypes result = '.json_encode($arr),$uselog);


		echo json_encode($arr);

}

/********************function export(){  // actually output the pre received dataset (in setofmembers:)
	$f3=$this->f3;	
	$admin_logger = new MyLog('admin.log');
	$uselog=$f3->get('uselog');
	$setofmembers=$f3->get('setofmembers');
	
	$f3->set('view','member/exports.htm'); 
	$f3->set('page_head','Export Mailing Lists');
	$f3->set('page_role',$f3->get('SESSION.user_role'));
	$setofmembers=$f3->get('POST.setofmembers');
		$admin_logger->write('in export WITH POST '.$setofmembers,$uselog);	
		switch($setofmembers){
		case 'all':
		$result=$this->emails('all');
		//$admin_logger->write('in AjaxrController exports resultset = '.var_export($result,true),$uselog);
		echo json_encode($result);
		break;
		case 'cm':
		
		break;
		case 'gl':
		break;
		default:
		$admin_logger->write('in Ajax Controller at default!!',$uselog);
		break;
		}
	    
	
		
}
***********/

function downloads2(){
	$f3=$this->f3;
	$whichset=$f3->get('POST.theset');
	// $dlfilename='downloads/email_list_'.$whichset.'.pdf';
$dldir=$f3->get('BASE').$f3->get('downloads');	
	 $dlfilename=$dldir.'/email_list_'.$whichset.'.pdf';
	$admin_logger = new MyLog('admin.log');
	$uselog=$f3->get('uselog');
	$admin_logger->write('in downloads2',$uselog);
	$this->writeemailpdf($dlfilename,$whichset);
	$admin_logger->write('in downloads2 downloading '.$dlfilename,$uselog);
	/****  if (!Web::instance()->send($dlfilename,NULL,512,TRUE))   {  $f3->error(404);    }          // Generate an HTTP 404
      $dlfilename=$dldir.'/email_list_'.$whichset.'.csv';
	  if (!Web::instance()->send($dlfilename,NULL,512,TRUE))   {  $f3->error(404);    }     ***/
		echo "OK";
}
	function writeemailpdf($thefile,$theset) { //based on http://www.fpdf.org/en/script/script21.php
		$f3=$this->f3;
		
	$admin_logger = new MyLog('admin.log');
	$uselog=$f3->get('uselog');
	$admin_logger->write('in writeemailpdf',$uselog);
		$u3ayear = $f3->get('SESSION.u3ayear' );
		$lastu3ayear = $f3->get('SESSION.lastu3ayear' );
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
		
		$dldir=$f3->get('BASE').$f3->get('downloads');
		$admin_logger->write('in writeemailpdf outputted to  '.$thefile,$uselog);
		//$pdf->Output($dldir.'/email_list_'.$theset.'.pdf',"F");
		$pdf->Output($thefile,"F");

	return true;
		
	}

} // end of class