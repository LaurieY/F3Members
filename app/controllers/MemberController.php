<?php

class MemberController extends Controller {
	function beforeroute() {
	$f3=$this->f3;
	$auth_logger = new Log('auth.log');
	$auth_logger->write( 'Entering beforeroute URI= '.$f3->get('URI'  ) );
	$auth_logger->write( "Session user_id = ".$f3->get('SESSION.user_id')); 
	$auth_logger->write( "Session lastseen = ".$f3->get('SESSION.lastseen')); 
	$auth_logger->write( "Session expiry secs = ".($f3->get('user_expiry')*3600)); 
	$auth_logger->write( "Session time now = ".time());
	$auth_logger->write( "Session lastseen  expiry = ".($f3->get('SESSION.lastseen')+($f3->get('user_expiry')*3600))); 
	//$auth_logger->write( "Member beforeroute $this = ".var_export($f3->get('SESSION.user_id')));
	
	$relogincondition = !($f3->get('SESSION.user_id'))&&( $f3->get('SESSION.lastseen')+($f3->get('user_expiry')*3600)>time());
	if (((!$f3->get('URI')=='/login' )&&(!$f3->get('URI')=='/logout' ))&&$relogincondition      ) 
	// not login or logout and not a session user_id already then need to force a login
	{$auth_logger->write( 'Exiting beforeroute with relogincondition ='.$relogincondition);
	$auth_logger->write( 'Exiting beforeroute with reroute to login');	 
	$this->f3->reroute('/login');
		}
	$auth_logger->write( 'Exiting beforeroute URI= '.$f3->get('URI'  ));		}
	
	private  function checkpwd() {
	$f3=$this->f3;
	$auth_logger = new Log('auth.log');
			$memuser = new DB\SQL\Mapper($this->db, 'mem_users'); 
			
		$thisuser=$memuser->load(array('username =:user',array(':user'=> $f3->get('POST.user_id')) ) );
			
			$auth_logger->write( 'the posted password = '.$f3->get('SESSION.password'))	;
			
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
				$f3->clear('COOKIE.sent');
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
public function sessionly ()
{ $this->f3->set('page_head','Session info');

$this->f3->set('lyvar','in before');
$this->f3->set('view','member/session.htm');
}

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
		
		public function index1()	
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

	
	  $f3->set('listnn','member/list1_xml.htm');
	$f3->set('view','member/list1_xml.htm');
		$f3->set('SESSION.lastseen',time()); 
	}
	public function index2()	
	{
	$f3=$this->f3;
	$auth_logger = new Log('auth.log');
	$auth_logger->write( 'Entering index2'  );	       
		   $member = new Member($this->db);
        $f3->set('members',$member->all());
		$f3->set('page_head','Member List');
		$f3->set('page_role',$f3->get('SESSION.user_role'));
        $f3->set('message', $f3->get('PARAMS.message'));
		$f3->set('listn', $f3->get('PARAMS.mylist'));

	
	  $f3->set('listnn','member/list2.htm');
	$f3->set('view','member/list2.htm');
		$f3->set('SESSION.lastseen',time()); 
	}
			
    public function payments ()
	
	{
	$f3=$this->f3;
		   $member = new Member($this->db);
    $f3->set('members',$member->all());
	$f3->set('page_head','Update Payments');
	$f3->set('page_role',$f3->get('SESSION.user_role'));
	$f3->set('message', $f3->get('PARAMS.message'));	//NEEDED in Header 
	$f3->set('view','member/listpaid.htm');
	$f3->set('SESSION.lastseen',time()); 
	}
	public function create()
    {
        if($this->f3->exists('POST.create'))
        {
            $user = new User($this->db);
            $user->add();

            $this->f3->reroute('/success/New User Created');
        } else
        {
            $this->f3->set('page_head','Create User');
            $this->f3->set('view','user/create.htm');
        }
$f3->set('SESSION.lastseen',time()); 
    }

    public function update()
    {

        $user = new User($this->db);

        if($this->f3->exists('POST.update'))
        {
            $user->edit($this->f3->get('POST.id'));
            $this->f3->reroute('/success/User Updated');
        } else
        {
            $user->getById($this->f3->get('PARAMS.id'));
            $this->f3->set('user',$user);
            $this->f3->set('page_head','Update User');
            $this->f3->set('view','user/update.htm');
        }
$f3->set('SESSION.lastseen',time()); 
    }
		public function listn()	
	{
	        $user = new Member($this->db);
        $this->f3->set('members',$user->all());
		        $this->f3->set('page_head','User List');
        $this->f3->set('message', $this->f3->get('PARAMS.message'));
		$this->f3->set('listn', $this->f3->get('PARAMS.mylist'));

		
	     $this->f3->set('listnn','member/list'.$this->f3->get('listn').'.htm');
		 $this->f3->set('view','member/list'.$this->f3->get('listn').'.htm');
	//  $this->f3->set('listnn','member/list.htm');
	//$this->f3->set('view','member/list.htm');
		 
	}
		
   

    public function delete()
    {
        if($this->f3->exists('PARAMS.id'))
        {
            $user = new User($this->db);
            $user->delete($this->f3->get('PARAMS.id'));
        }
$f3->set('SESSION.lastseen',time()); 
        $this->f3->reroute('/success/User Deleted');
    }
	function login() {
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
		$f3->clear('SESSION');
		if ($f3->get('eurocookie')) {
$login_logger->write( 'IN login IN Eurocookie'  );
			$loc=Web\Geo::instance()->location();
			if (isset($loc['continent_code']) && $loc['continent_code']=='EU')
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
		
		$f3->set('COOKIE.sent',TRUE);
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
		$this->f3->set('page_head','Login');
		$this->f3->set('page_role','');
		$f3->set('view','member/login.htm');
		$f3->set('SESSION.lastseen',time()); 
	}
	function auth() {
	$f3=$this->f3;
	$f3->clear('message');
	
		if (!$f3->get('COOKIE.sent'))
			$f3->set('message','Cookies must be enabled to enter this area');
		else {/***********
	****/
		if ($this->checkpwd() ){$f3->reroute('/');
		
		}
		else 
		$this->login($f3);
		}
	}

	//! Terminate session
	function logout() {
	//$f3=$this->f3;
		$this->f3->clear('SESSION');
		
		$this->f3->reroute('/login');
	//$this->f3->reroute('/z');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	

}