<?php

class MemberController extends Controller {
	function beforeroute() {
	$f3=$this->f3;
	$f3=$this->f3;
	 $f3->set('message','');
	$auth_logger = new MyLog('auth.log');
	$auth_logger->write( 'Entering MemberController beforeroute URI= '.$f3->get('URI'  ) );
	
	if (!$f3->get('COOKIE.PHPSESSID')){
			$f3->set('message','Cookies must be enabled to enter this area');
			$auth_logger->write( ' COOKIE PHPSESSID NOT set contents = '.var_export($f3->get('COOKIE'), true));
			$f3->reroute('/');
			}
	
	if($f3->get('SESSION.user_id')){$auth_logger->write( "Session user_id = ".$f3->get('SESSION.user_id')); 
	$auth_logger->write( "Session lastseen = ".$f3->get('SESSION.lastseen')); 
	$auth_logger->write( "Session expiry config secs = ".($f3->get('user_expiry'))*($f3->get('user_expiry_mult'))); 
	//$auth_logger->write( "Session expiry secs = ".($f3->get('user_expiry'))*($f3->get('user_expiry_mult'))); 
	$auth_logger->write( "Session time now = ".time());
	$auth_logger->write( "Session lastseen  expiry = ".($f3->get('SESSION.lastseen')+(($f3->get('user_expiry'))*($f3->get('user_expiry_mult'))))); 

	}
	else
	{$auth_logger->write( "In membercontroller beforeroute Session user_id NOT set");
	$auth_logger->write( "Session lastseen = ".$f3->get('SESSION.lastseen')); 
	$auth_logger->write( "Session expiry config secs = ".($f3->get('user_expiry'))*($f3->get('user_expiry_mult'))); 
	//$auth_logger->write( "Session expiry secs = ".($f3->get('user_expiry'))*($f3->get('user_expiry_mult'))); 
	$auth_logger->write( "Session time now = ".time());
	$auth_logger->write( "Session lastseen  expiry = ".($f3->get('SESSION.lastseen')+(($f3->get('user_expiry'))*($f3->get('user_expiry_mult'))))); 
}
	$relogincondition= true;
	$relogincondition = (!$f3->get('SESSION.user_id'))||( $f3->get('SESSION.lastseen')+($f3->get('user_expiry')*($f3->get('user_expiry_mult')))<time());
	$auth_logger->write( 'beforeroute with relogincondition a ='.$relogincondition);
	//if ((!($f3->get('URI')=='/login' )&&!($f3->get('URI')=='/logout' ))&&$relogincondition      ) 
	if ($relogincondition)
	// not login or logout and not a session user_id already then need to force a login
	{$auth_logger->write( 'Exiting beforeroute with relogincondition ='.$relogincondition);
	$auth_logger->write( 'Exiting beforeroute with reroute to login');	 
	$this->f3->reroute('/login');
		}
	$auth_logger->write( 'Exiting beforeroute URI= '.$f3->get('URI'  ));
	$auth_logger->write( 'Exiting beforeroute page_head set to = '.$f3->get('page_head'  ));
//debug_backtrace();	
}
function check_cookie()
{$auth_logger = new MyLog('auth.log');
$f3=$this->f3;
	$auth_logger->write( 'Entering check_cookie URI= '.$f3->get('URI'  ) );
	
    setcookie('COOK_CHK', uniqid(), time()+60);
    if(!isset($_COOKIE['COOK_CHK']))
    {$auth_logger->write( 'check_cookie !isset' );
        header('Location: ' . $_SERVER['PHP_SELF']);
    }
    else
    {$auth_logger->write( 'check_cookie isset inner' );
        return TRUE;
    }
$auth_logger->write( 'check_cookie isset outer' );
    return TRUE;
}
/**public function sessionly ()
{ $this->f3->set('page_head','Session info');

$this->f3->set('lyvar','in before');
$this->f3->set('view','member/session.htm');
}
**/

public function index()	
	{
	$f3=$this->f3;
	$auth_logger = new MyLog('auth.log');
	$auth_logger->write( 'Entering index'  );	       
		   $member = new Member($this->db);
        $f3->set('members',$member->all());
		$f3->set('page_head','Member List');
		$f3->set('page_role',$f3->get('SESSION.user_role'));
        $f3->set('message', $f3->get('PARAMS.message'));
		$f3->set('listn', $f3->get('PARAMS.mylist'));

	
	  $f3->set('listnn','member/list.htm');
	$f3->set('view','member/list.htm');
		$f3->set('SESSION.lastseen',time()); 
	}


public function payments ()
		{
	$f3=$this->f3;
		$auth_logger = new MyLog('auth.log');
	$auth_logger->write( 'Entering payments'  );
		   $member = new Member($this->db);
    $f3->set('members',$member->all());
	$f3->set('page_head','Update Payments');
	$f3->set('page_role',$f3->get('SESSION.user_role'));
	if ($f3->get('SESSION.user_role') =='user' ) {//don't allow any changes for standard user so payments not allowed
	$this->f3->reroute('/login');
	}
	$f3->set('message', $f3->get('PARAMS.message'));	//NEEDED in Header 
	$f3->set('view','member/listpaid.htm');
	$f3->set('SESSION.lastseen',time()); 
	}
/**********  show a grid for the audit trail table IFF logged in with role admin ******/

public function trail() {
$f3=$this->f3;
	$auth_logger = new MyLog('auth.log');
	$auth_logger->write( 'Entering trail'  );	
if ($f3->get('SESSION.user_role')==="admin"){
		   $trail = new Trail($this->db);
        $f3->set('trail',$trail->all());
		$f3->set('page_head','Audit Trail List');
		$f3->set('page_role',$f3->get('SESSION.user_role'));
        $f3->set('message', $f3->get('PARAMS.message'));
		//$f3->set('listn', $f3->get('PARAMS.mylist'));
		//$f3->set('listnn','member/list.htm');
		$f3->set('view','admin/trail.htm');
		$f3->set('SESSION.lastseen',time()); 

}
}
function exports(){// generate all the likely export files for downloading
	$f3=$this->f3;	
	$admin_logger = new MyLog('admin.log');
	$uselog=$f3->get('uselog');
	//$uselog =false;
	//$f3->set('message', $f3->get('PARAMS.message'));
	$u3ayear= Member::getu3ayear();
	
	//$admin_logger->write('in MemberController $u3ayear='.$u3ayear,$uselog);
	// Now fetch the required data sets-  all, Committee Members , GL's
	//$result=$this->emails('all');
$dldir=$f3->get('downloads');
$admin_logger->write('in exports dldir = '.$dldir,$uselog);
		$result=$this->emails('all');
		$resp=$this->writeemails($result,'all');

		$result=$this->emails('cm');
		$resp=$this->writeemails($result,'cm');
		
		$result=$this->emails('gl');
		$resp=$this->writeemails($result,'gl');
		//$admin_logger->write('in exports written emails gl resp=  '.$resp,$uselog);
		$result=$this->emails('all',"('N')");
		$resp=$this->writeemails($result,'unpaid');
		$result=$this->emails('all',"('W')");
		$resp=$this->writeemails($result,'willpay');

		$lastu3ayear = Member::getlastu3ayear(); //Last year's members
		$result=$this->emails('all',"('Y')",$lastu3ayear);
		$resp=$this->writeemails($result,'lastyear');
		
		
		$f3->set('view','member/exports.htm'); 
		$f3->set('page_head','Email Lists');
		$f3->set('page_role',$f3->get('SESSION.user_role'))		;
	
	}
function writeemails($data,$theset) {
		$f3=$this->f3;
		$dldir=$f3->get('BASE').$f3->get('downloads');
		$resp=99;
		
		//$resp=$f3->write($dldir.'/email_list_'.$theset.'.csv',var_export($data,TRUE));
		
			$out = "";
		foreach($data as $arr) {
				$out .= implode(",", $arr)."\n" ;
				}
		$resp=$f3->write($dldir.'/email_list_'.$theset.'.csv',$out.",,,,");
		return $resp;
	
	
		}
function emails($setofmembers='all',$paidstatus="('Y','N','W')",$u3ayear=NULL ) {
    	$f3=$this->f3;       
		$db=new DB\SQL(
            $f3->get('db_dns') . $f3->get('db_name'),
            $f3->get('db_user'),
            $f3->get('db_pass')
        );	
	 $u3ayear = isset($u3ayear) ? $u3ayear : Member::getu3ayear();;

  // $u3ayear= Member::getu3ayear();
   $emailfilename='membersemails-'.$setofmembers.'.csv';
	switch($setofmembers){
		case 'all':
		$thesql="select forename,surname,location,membtype,membnum,email from members where u3ayear='".$u3ayear."' and status='Active' and paidthisyear in ".$paidstatus." order by membnum ASC";
		
		break;
		case 'cm':
		$thesql="select forename,surname,location,membtype,membnum,email from members where u3ayear='".$u3ayear."' and status='Active' and membtype in ('CM','CMGL')"." order by membnum ASC";
		
		break;
		case 'gl':
		$thesql="select forename,surname,location,membtype,membnum,email from members where u3ayear='".$u3ayear."' and status='Active' and membtype in ('GL','CMGL')"." order by membnum ASC";
		
		break;


		default:
		$thesql="select forename,surname,location,membtype,membnum,email from members where u3ayear='never'"." order by membnum ASC";
		break;
		}
		
		return $db->exec($thesql);

		
        
    }
} // end of Class MemberController