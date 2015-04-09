<?php

class EmailController extends Controller {
   public $name = 'Lists';
    public $uses = array();
public function email2(){ 
	$f3=$this->f3;
//$this->mc = new Mailchimp('1e88266ff71a6f5eaef954a244cf5426-us2');
	
		$email_logger = new Log('email.log');
		$email_logger->write( "In email1 \n"   );
		
		$this->f3->set('page_head','Email');
        $this->f3->set('message', $this->f3->get('PARAMS.message'));
	//	$admin_logger->write('in admin index PARAMS.message is '.$f3->get('PARAMS.message'));
	$f3->set('page_role',$f3->get('SESSION.user_role'));
        $this->f3->set('view','admin/list.htm');
}
    public function email1() {
	$f3=$this->f3;
			$email_logger = new Log('email.log');
		$email_logger->write( "In email index \n"   );
//	$this->mc = new Mailchimp('1e88266ff71a6f5eaef954a244cf5426-us2');
        try {
            $lists = $this->mc->lists->getList();
            $f3->set('lists', $lists['data']);
			//$email_logger->write( "In email lists ".var_dump($lists['data'])   ."\n"   );
        } catch (Mailchimp_Error $e) {
            if ($e->getMessage()) {
                $this->Session->setFlash($e->getMessage(), 'flash_error');
            } else {
                $this->Session->setFlash('An unknown error occurred', 'flash_error');
            }
           }
		    $this->f3->set('page_head','Email');
        $this->f3->set('message', $this->f3->get('PARAMS.message'));
		
		//$email_logger->write('in admin index PARAMS.message is '.$f3->get('PARAMS.message'));
		$f3->set('page_role',$f3->get('SESSION.user_role'));
        $this->f3->set('view','email/list.htm');
    }
public function subscribe1(){
$f3=$this->f3;
//$this->mc = new Mailchimp('1e88266ff71a6f5eaef954a244cf5426-us2');
		$email_logger = new Log('email.log');
		$email_logger->write( "In subscribe1 \n"   );
    //    $id = $this->request->params['id'];
      // $email = $this->request->data['email'];
$id='0d81982f0d';
//$id='806749';
//$id='0ef7c93efc';
$email = 'laurie9c@lyates.com';
	try {
            $this->mc->lists->subscribe($id, array('email'=>$email),array('FNAME'=>'laurie9'),'html',false);
			$email_logger->write( "In subscribe1 Success \n"   );
           // $f3->Session->setFlash('User subscribed successfully!', 'flash_success');
        } catch (Mailchimp_Error $e) {
				$email_logger->write( "In subscribe1 Error ".$e."\n"   );
				$email_logger->write( "In subscribe1 Error ".$e->getMessage()."\n"   );
            if ($e->getMessage()) {
                $this->Session->setFlash($e->getMessage(), 'flash_error');
            } else {
                $this->Session->setFlash('An unknown error occurred', 'flash_error');
            }
        }
}
public function subscribe2() {
		$email_logger = new Log('email.log');
		$email_logger->write( "In subscribe2 \n"   );
$MailChimp = new \Drewm\MailChimp('1e88266ff71a6f5eaef954a244cf5426-us2');
$result = $MailChimp->call('lists/subscribe', array(
                'id'                => '0d81982f0d',
                'email'             => array('email'=>'lauriedrewm@lyates.com'),
                'merge_vars'        => array('FNAME'=>'Laurie', 'LNAME'=>'Yates'),
                'double_optin'      => false,
                'update_existing'   => true,
                'replace_interests' => false,
                'send_welcome'      => false,
            ));
print_r($result);


}
public function batch_subscribe2() {
		$email_logger = new Log('email.log');
		$email_logger->write( "In batch_subscribe2 \n"   );
		$MailChimp = new \Drewm\MailChimp('1e88266ff71a6f5eaef954a244cf5426-us2');
		$batch[] = array('email' => array('email' => 'lauriedrewm2@lyates.com'));
$result = $MailChimp->call('lists/subscribe', array(
                'id'                => '0d81982f0d',
				$batch,
             //   'batch'             => array('email'=>array('email'=>'lauriedrewm2@lyates.com','euid'=>'180','leid'=>'180'),
			//						'email_type'=>'html',
            //    'merge_vars'=> array('FNAME'=>'Laurie', 'LNAME'=>'Yates')),
                'double_optin'      => false,
                'update_existing'   => true,
                'replace_interests' => false,
               
            ));
print_r($result);


}




}

