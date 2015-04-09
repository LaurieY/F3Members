<?php

class Controller {
 protected $mc; //for Mailchimp
	protected $f3;
	protected $db;

	function beforeroute() {
	$db=$this->db;
	$this->mc = new Mailchimp('1e88266ff71a6f5eaef954a244cf5426-us2',array('debug'=> true));
	//	$this->f3->set('message','');
	//	echo ($this->f3->get('message' ));
	}

	function afterroute() {
		echo Template::instance()->render('layout.htm');	
	}

	function __construct() {

        $f3=Base::instance();

        $db=new DB\SQL(
            $f3->get('db_dns') . $f3->get('db_name'),
            $f3->get('db_user'),
            $f3->get('db_pass')
        );	
		new DB\SQL\Session($db);
		// Save frequently used variables
		$this->db=$db;
		$this->f3=$f3;
		$this->db=$db;
	}
}
