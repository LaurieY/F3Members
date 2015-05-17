<?php
// *****************  mcrud index.php *
//$f3=require('lib/base.php');
require 'vendor/autoload.php';
$f3 = require('lib/base.php');
$f3->set('CACHE',FALSE);
$f3->config('config/config.ini');

$f3->route('GET /app/views/feespertypes [ajax]','AjaxController->getfeespertypes');
$f3->route('GET /membergrid [ajax]','AjaxController->members');
//$f3->route('GET /membergrid','MyAjax->members');
$f3->route('GET /usergrid [ajax]','AjaxController->users');
$f3->route('POST /edituser [ajax]','AjaxController->edituser');

$f3->route('GET /members','MemberController->index');
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

$f3->route('GET /exports','MemberController->exports');
$f3->route('POST /exports','MemberController->exports');
//$f3->route('GET /1','MemberController->index1');
//$f3->route('GET /2','MemberController->index2');
//$f3->config('config/routes.ini');
$f3->run();
