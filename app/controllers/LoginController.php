<?php
class LoginController extends Controller {
function startup() {
	$f3=$this->f3;
	 $f3->set('message','');
	$login_logger = new Log('login.log');
	$login_logger->write( 'Entering LoginController startup URI= '.$f3->get('URI'  ) );
	if (!$f3->exists('COOKIE.PHPSESSID')){
	$login_logger->write( 'In LoginController No COOKIE.PHPSESSID ');
	}
	$f3->reroute('/login');
}

}