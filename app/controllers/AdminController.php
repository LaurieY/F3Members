<?php

class AdminController extends Controller {
	function beforeroute() {

	$f3=$this->f3;
		$auth_logger = new Log('auth.log');
	$auth_logger->write( "AdminController beforeroute  Session user_id = ".$f3->get('SESSION.user_id')); 
	if ((!$f3->get('SESSION.user_id')) ||($f3->get('SESSION.user_role')!='admin')||( $f3->get('SESSION.lastseen')+($f3->get('admin_expiry')*3600)>time()))
			{$f3->set('message','Cookies must be enabled to enter this area and user must have admin access');
			$this->nocookie();

	}
}

		public function index()	
	{
	$f3=$this->f3;
	$admin_logger = new Log('admin.log');
	//$admin_logger->write('in admin index');
	    $user = new User($this->db);
			
        $this->f3->set('mem_users',$user->all());
	
		//$admin_logger->write('mem_users are '.'');
        $this->f3->set('page_head','Admin');
        $this->f3->set('message', $this->f3->get('PARAMS.message'));
	//	$admin_logger->write('in admin index PARAMS.message is '.$f3->get('PARAMS.message'));
        $this->f3->set('view','admin/index.htm');

	}
public function nocookie()
{
$f3=$this->f3;
	$auth_logger = new Log('auth.log');
	$auth_logger->write( 'Entering admin nocookie'  );	
			$f3->set('page_head','No Cookie set');
		$f3->set('page_role','user');
        $f3->set('message', 'Session Cookies MUST be allowed in your Browser for this program to function');
		
		$f3->set('view','admin/nocookie.htm');
		//$f3->set('SESSION.lastseen',time()); 

}
	

	
	}