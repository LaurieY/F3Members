<?php

class MemberController extends Controller {

public function sessionly ()
{ $this->f3->set('page_head','Session info');

$this->f3->set('lyvar','in before');
$this->f3->set('view','member/session.htm');
}

		public function index()	
	{
	        $member = new Member($this->db);
        $this->f3->set('members',$member->all());
		        $this->f3->set('page_head','Member List');
        $this->f3->set('message', $this->f3->get('PARAMS.message'));
		$this->f3->set('listn', $this->f3->get('PARAMS.mylist'));

		
	//     $this->f3->set('listnn','member/list'.$this->f3->get('listn').'.htm');
	//	 $this->f3->set('view','member/list'.$this->f3->get('listn').'.htm');
	  $this->f3->set('listnn','member/list.htm');
	$this->f3->set('view','member/list.htm');
		 
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

        $this->f3->reroute('/success/User Deleted');
    }
	function login() {
	$f3=$this->f3;
		$login_logger = new Log('login.log');
	$login_logger->write( 'Entering login'  );
	$login_logger->write( 'Root = '.$f3->get('ROOT')   );
	$login_logger->write( 'Base = '.$f3->get('BASE')   );
	$login_logger->write( 'Ui = '.$f3->get('PATH')   );
	$login_logger->write( 'Path = '.$f3->get('UI')   );
	$login_logger->write( 'Logs = '.$f3->get('LOGS')   );
	$mysession = http_build_query($f3->get('SESSION'));
	$login_logger->write( 'Session = '.$mysession   );
		$f3->clear('SESSION');
		if ($f3->get('eurocookie')) {
			$loc=Web\Geo::instance()->location();
			if (isset($loc['continent_code']) && $loc['continent_code']=='EU')
				$f3->set('message',
					'The administrator pages of this Web site uses cookies '.
					'for identification and security. Without these '.
					'cookies, these pages would simply be inaccessible. By '.
					'using these pages you agree to this safety measure.');
		}
		F3::set('FONTS','ui/fonts/');
		$fontdir=http_build_query(scandir('ui'));
		$login_logger->write( 'Fonts = '.$f3->get('FONTS')   )	;
		$login_logger->write( 'UI dir contains= '.$fontdir   )	;
		$login_logger->write( 'Session.captcha = '.get_class($f3-> get( 'SESSION.captcha' )))	;
		$login_logger->write( 'Session.captcha = '.$f3-> get( 'SESSION.captcha' ))	;
		
		$f3->set('COOKIE.sent',TRUE);
		$img = new Image();
		$fred=$img->captcha('fonts/CoolFont.ttf',16,5);
		$login_logger->write( 'captcha1 contains= '.get_class($fred   ))	;
		if ($f3->get('message')) {
			$img=new Image;
			 $finfo = finfo_open(FILEINFO_MIME_TYPE);
			$finfofile=  finfo_file($finfo, 'ui/fonts/thunder.ttf') ;
			$login_logger->write( 'file details = '.$finfofile)	;
			$capt = $img->captcha('ui/fonts/thunder.ttf',18,5,'SESSION.captcha');
			$login_logger->write( 'image class is = '.get_class($img   ))	;
			$login_logger->write( 'captcha contains= '.get_class($capt   ))	;
			$f3->set('captcha',$f3->base64(
				$img->captcha('ui/fonts/thunder.ttf',18,5,'SESSION.captcha')->
					dump(),'image/png'));
		}
		$f3->set('inc','login.htm');
	}
	function auth() {
	$f3=$this->f3;
		if (!$f3->get('COOKIE.sent'))
			$f3->set('message','Cookies must be enabled to enter this area');
		else {
			$crypt=$f3->get('password');
			$captcha=$f3->get('SESSION.captcha');
			if ($captcha && strtoupper($f3->get('POST.captcha'))!=$captcha)
				$f3->set('message','Invalid CAPTCHA code');
			elseif ($f3->get('POST.user_id')!=$f3->get('user_id') ||
				crypt($f3->get('POST.password'),$crypt)!=$crypt)
				$f3->set('message','Invalid user ID or password');
			else {
				$f3->clear('COOKIE.sent');
				$f3->clear('SESSION.captcha');
				$f3->set('SESSION.user_id',$f3->get('POST.user_id'));
				$f3->set('SESSION.crypt',$crypt);
				$f3->set('SESSION.lastseen',time());
				$f3->reroute('/admin/pages');
			}
		}
		$this->login($f3);
	}

	//! Terminate session
	function logout() {
	//$f3=$this->f3;
		$this->f3->clear('SESSION');
		$this->f3->reroute('/login');
	//$this->f3->reroute('/z');
	}
}