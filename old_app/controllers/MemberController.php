<?php

class MemberController extends Controller {
	function beforeroute() {
	$f3=$this->f3;
	 $f3->set('message','');
	$auth_logger = new Log('auth.log');
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
{$auth_logger = new Log('auth.log');
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
	$auth_logger = new Log('auth.log');
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
function exports(){
	$f3=$this->f3;	
	$admin_logger = new Log('admin.log');
	$f3->set('message', $f3->get('PARAMS.message'));
	if($f3->exists('POST.exporttype'))
	{// analyze the export type and produce the list and download it
	$admin_logger->write('in MemberController  exports WITH POST');
	}
	else { // NOT a POST so setup the forms
	$admin_logger->write('in MemberController  exports NOT POST');
        $f3->set('view','member/exports.htm'); 
		$f3->set('page_head','Export Mailing Lists');
		$f3->set('page_role',$f3->get('SESSION.user_role'));
	}
	
	
	}



public function payments ()
		{
	$f3=$this->f3;
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
	$auth_logger = new Log('auth.log');
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


} // end of Class MemberController