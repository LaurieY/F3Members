<?php

class MemberController extends Controller {
	function beforeroute() {
	$f3=$this->f3;
	 $f3->set('message','');
	$auth_logger = new Log('auth.log');
	$auth_logger->write( 'Entering MemberController beforeroute URI= '.$f3->get('URI'  ) );
	
	if (!$f3->get('COOKIE.PHPSESSID')){
	//if (!$this->check_cookie()) {
			$f3->set('message','Cookies must be enabled to enter this area');
			$auth_logger->write( ' COOKIE PHPSESSID NOT exists contents = '.var_export($f3->get('COOKIE'), true));
			$f3->reroute('/nocookie');
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
	if ((!($f3->get('URI')=='/login' )&&!($f3->get('URI')=='/logout' ))&&$relogincondition      ) 
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
 function auth() {
	$f3=$this->f3;
	$f3->clear('message');
	
//$f3->set('page_head','Login');
		$auth_logger = new Log('auth.log');
		$auth_logger->write( 'In auth ');
		//if (!$f3->get('COOKIE.sent'))
		if (!$f3->get('COOKIE.PHPSESSID'))
			{$f3->set('message','Cookies must be enabled to enter this area');
			$auth_logger->write( 'In auth Cookies must be enabled to enter this area');
			$auth_logger->write( ' COOKIE contents = '.var_export($f3->get('COOKIE'), true));
			$auth_logger->write( ' SESSION contents = '.var_export($f3->get('SESSION'), true));
		//	echo var_export($f3->get('COOKIE'),true);
			//echo var_export($f3->get('SESSION'), true);
			$f3->reroute('/nocookie');
			}
		else {/***********
	****/
	$auth_logger->write( 'In auth Cookies ARE enabled');
			$auth_logger->write( ' COOKIE contents = '.var_export($f3->get('COOKIE'), true));
			$auth_logger->write( ' SESSION contents = '.var_export($f3->get('SESSION'), true));
	$thisuserid= $f3->get('POST.user_id');
	$thispassword = $f3->get('SESSION.password') ;
		if ($this->checkpwd($thisuserid,$thispassword) ){$f3->reroute('/members');
		
		}
		else 
		$this->login($f3); 
		//$f3->reroute('/login');
		}
	}	
function checkpwd($thisuserid,$thispassword) { 
	$f3=$this->f3;
	$auth_logger = new Log('auth.log');
			$memuser = new DB\SQL\Mapper($this->db, 'mem_users'); 
			
		//$thisuser=$memuser->load(array('username =:user',array(':user'=> $f3->get('POST.user_id')) ) );
			$thisuser=$memuser->load(array('username =:user',array(':user'=> $thisuserid)));
			//$auth_logger->write( 'the posted password = '.$f3->get('SESSION.password'))	;
			$auth_logger->write( 'checkpwd the posted userid/name = '.$thisuserid);
			//$auth_logger->write( 'the posted username = '.$thisuser);
			$auth_logger->write( 'the posted password = '.$thispassword);
			if($memuser->loaded() ){
			$auth_logger->write( 'thisusers loaded count = '.$memuser->loaded())	;
			$auth_logger->write( 'thisuser = '.$thisuser->username)	;
			}
			else 
			return false;
			$pwdcrypt=$thisuser->password;
			$auth_logger->write( 'this encrypted password = '.$pwdcrypt)	;
			
			$captcha=$f3->get('SESSION.captcha');
			if ($captcha && strtoupper($f3->get('POST.captcha'))!=$captcha)
				{$f3->set('message','Invalid CAPTCHA code');
				return false;}
			elseif ($pwdcrypt!=crypt($f3->get('POST.password'),$pwdcrypt))/*****check Posted  the database ***/
				{$auth_logger->write( 'encrypted password NOT equal to POST.password which was = '.$f3->get('POST.password'))	;
	/*****		$f3->get('POST.user_id')!=$f3->get('user_id') ||
				crypt($f3->get('POST.password'),$crypt)!=$crypt)********/
				$f3->set('message','Invalid user ID or password');
				return false;}
			else {$auth_logger->write( 'encrypted password IS equal to POST.password which was = '.$f3->get('POST.password'))	;
				//$f3->clear('COOKIE.sent');
				
				
				$f3->clear('SESSION.captcha');
				$f3->set('SESSION.user_id',$f3->get('POST.user_id'));
				$f3->set('SESSION.crypt',$pwdcrypt);
				$f3->set('SESSION.user_role',$thisuser->role);
				$f3->set('SESSION.lastseen',time());
			
				
				$auth_logger->write( 'Exiting checkpwd SESSION.user_id= '.$f3->get('SESSION.user_id'  ) );
				$auth_logger->write( 'Exiting checkpwd SESSION.user_role= '.$f3->get('SESSION.user_role'  ) );
				$auth_logger->write( 'Exiting checkpwd SESSION.lastseen= '.$f3->get('SESSION.lastseen'  ) );
				return true;
			}
		
	return true;
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


public function login() {
	$f3=$this->f3;
		$login_logger = new Log('login.log');
		//$login_logger->erase();
	$login_logger->write( 'Entering login'  );
/*	$login_logger->write( 'Root = '.$f3->get('ROOT')   );
	$login_logger->write( 'Base = '.$f3->get('BASE')   );
	$login_logger->write( 'Ui = '.$f3->get('PATH')   );
	$login_logger->write( 'Path = '.$f3->get('UI')   );
	$login_logger->write( 'Logs = '.$f3->get('LOGS')   ); */
	//$mysession = http_build_query($f3->get('SESSION'));
	//$f3->dump($mysession   );
		//$f3->clear('SESSION');
		if ($f3->get('eurocookie')) {
		$login_logger->write( 'IN login IN Eurocookie'  );
		/*	$loc=Web\Geo::instance()->location(); // innecessary because we ARE in the EU
			$f3->set('message','Cookies Set');
			if (isset($loc['continent_code']) && $loc['continent_code']=='EU')
			*/
				
			$f3->set('message',
					'The administrator pages of this Web site uses cookies '.
					'for identification and security. Without these '.
					'cookies, these pages would simply be inaccessible. By '.
					'using these pages you agree to this safety measure.');
$login_logger->write( 'In login in continent==EU'  );
		}
		F3::set('FONTS','ui/fonts/');
	/*	$fontdir=http_build_query(scandir('ui'));
		$login_logger->write( 'Fonts = '.$f3->get('FONTS')   )	;
		$login_logger->write( 'UI dir contains= '.$fontdir   )	;
		$login_logger->write( 'Session.captcha = '.get_class($f3-> get( 'SESSION.captcha' )))	;
		$login_logger->write( 'Session.captcha = '.$f3-> get( 'SESSION.captcha' ))	;
		****/
		
		//$f3->set('COOKIE.sent',TRUE);
		$img = new Image();
		//$fred=$img->captcha('ui/fonts/thunder.ttf',16,5);
		$login_logger->write( 'message contains= '.$f3->get('message'))	;
		if ($f3->get('message')) {
			$img=new Image;
			// $finfo = finfo_open(FILEINFO_MIME_TYPE);
			//$finfofile=  finfo_file($finfo, 'ui/fonts/thunder.ttf') ;
		/*	$login_logger->write( 'file details = '.$finfofile)	;
			$capt = $img->captcha('ui/fonts/thunder.ttf',18,5,'SESSION.captcha');
			$login_logger->write( 'image class is = '.get_class($img   ))	;
			$login_logger->write( 'captcha contains= '.get_class($capt   ))	;
			***/
			$f3->set('captcha',$f3->base64(
				$img->captcha('ui/fonts/thunder.ttf',18,5,'SESSION.captcha')->
					dump(),'image/png'));
		}
		//$mysession = http_build_query($f3->get('SESSION'));
		//$f3->dump($mysession   );
	$login_logger->write( 'In  login setting page_head'  );
	if ($f3->get('COOKIE.PHPSESSID'))
	$login_logger->write( ' COOKIE PHPSESSID exists contents = '.var_export($f3->get('COOKIE'), true));
	else {
	$login_logger->write( ' COOKIE PHPSESSID NOT exists contents = '.var_export($f3->get('COOKIE'), true));
			$this->f3->reroute('/nocookie');}
			$f3->set('page_head','Login');
		$f3->set('page_role','');
		$f3->set('view','member/login.htm');
		$f3->set('SESSION.lastseen',time()); 

	}
	

	//! Terminate session
function logout() {
	//$f3=$this->f3;
		$this->f3->clear('SESSION');
		
		$this->f3->reroute('/login');

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