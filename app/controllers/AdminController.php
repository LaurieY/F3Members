<?php

class AdminController extends Controller {
	function beforeroute() {

	#$f3=$this->f3;
		if ($this->f3->get('SESSION.user_id')!=$this->f3->get('user_id') ||
			$this->f3->get('SESSION.crypt')!=$this->f3->get('password'))
			// Invalid session
			//$this->f3->reroute('/login');
			$this->f3->reroute('/login');
		if ($this->f3->get('SESSION.lastseen')+$this->f3->get('expiry')*3600<time())
			// Session has expired
			$this->f3->set('lyvar','in before');
		//	$this->f3->reroute('/logout');
			$this->f3->reroute('/sessionly');

		// Update session data
		$this->f3->set('SESSION.lastseen',time());
		// Prepare admin menu
		$this->f3->set('menu',
			array(
				'/admin/pages'=>'Pages',
				'/admin/assets'=>'Assets',
				'/logout'=>'Logout'
			)
		);
	}


		public function index()	
	{
	        $user = new User($this->db);
        $this->f3->set('users',$user->all());
        $this->f3->set('page_head','User List');
        $this->f3->set('message', $this->f3->get('PARAMS.message'));
        $this->f3->set('view','user/list.htm');

	}
	function login() {
	$f3=$this->f3;
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
		$f3->set('COOKIE.sent',TRUE);
		if ($f3->get('message')) {
			$img=new Image;
			$f3->set('captcha',$f3->base64(
				$img->captcha('fonts/thunder.ttf',18,5,'SESSION.captcha')->
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
	//	$this->f3->reroute('/login');
	$this->f3->reroute('/z');
	}

	
	}