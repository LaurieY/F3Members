<?php
// *****************  mcrud index.php *
//$f3=require('lib/base.php');
require 'vendor/autoload.php';
$f3 = require('lib/base.php');
$f3->set('CACHE',FALSE);
$f3->config('config/config.ini');





$f3->route('GET /app/views/feespertypes [ajax]','AjaxController->getfeespertypes');


$f3->route('GET /usergrid [ajax]','AjaxController->users');
$f3->route('POST /edituser [ajax]','AjaxController->edituser');

$f3->route('GET /members','MemberController->index');
$f3->route('GET /membergrid [ajax]','AjaxController->members');
$f3->route('GET /payments','MemberController->payments'); 

$f3->route('POST /editmember [ajax]','AjaxController->editmember');

$f3->route('POST /app/views/amtpaid [ajax]','AjaxController->amtpaid'); 
$f3->route('POST /app/views/feewhere [ajax]','AjaxController->feewhere');
$f3->route('POST /app/views/markpaid [ajax]','AjaxController->markpaid'); 
$f3->route('POST /app/views/markwillpay [ajax]','AjaxController->markwillpay'); 
$f3->route('POST /app/views/markunpay [ajax]','AjaxController->markunpay'); 

$f3->route('GET /email1','EmailController->email1');
$f3->route('GET /subscribe1','EmailController->subscribe1'); 
$f3->route('GET /subscribe2','EmailController->subscribe2'); 
$f3->route('GET /batchsubscribe2','EmailController->batch_subscribe2'); 

$f3->route('GET /','LoginController->startup');
$f3->route('GET /login','LoginController->login');
$f3->route('GET /logout','LoginController->logout');
$f3->route('POST /login','LoginController->auth');
$f3->route('GET /admin','AdminController->index'); // not currently used

$f3->route('GET /trail','MemberController->trail');

$f3->route('GET /trailgrid','AjaxController->trail');
$f3->route('GET /users','UserController->index');
//$f3->route('GET /user/update/@usr','UserController->update');
$f3->route('GET /changeme','UserController->changeme');
$f3->route('POST /changeme','UserController->changeme');

$f3->route('GET /nocookie','AdminController->nocookie');

$f3->route('GET /exports','MemberController->exports'); // generates all required email lists on page load
/**$f3->route('POST /app/views/export[ajax]','AjaxController->export');
$f3->route('POST /app/views/export','AjaxController->export');
$f3->route('POST /app/views/downloads [ajax]','Downloads->index');
//$f3->route('POST /app/views/downloads [ajax]','AjaxController->markpaid');
$f3->route('POST /exports','AjaxController->exports');  */

$f3->route('GET /downloads/@filename',
    function($f3,$args) {
	$mypdf= new OptionController();
	
 $dlfilename='downloads/email_list_'.$args['filename'].'.pdf';
$mypdf->writeemailpdf($dlfilename,$args['filename']);
 // now generate the pdf file appropriate
 //MemberController::writeemailpdf1($args['filename']);
        // send() method returns FALSE if file doesn't exist
        if (!Web::instance()->send($dlfilename,NULL,512,TRUE))
                  // Generate an HTTP 404
        $f3->error(404);
    }
);

$f3->route('GET /options','OptionController->index');
$f3->route('GET /optiongrid','OptionAjaxController->optiongrid');
$f3->route('POST /editoption','OptionAjaxController->editoption');
   

$f3->route('GET /rollover','MemberController->rollover');
$f3->route('GET /reverserollover','MemberController->reverserollover');


$f3->run();
