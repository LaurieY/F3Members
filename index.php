<?php
// *****************  mcrud index.php *
//$f3=require('lib/base.php');
require 'vendor/autoload.php';
$f3 = require('lib/base.php');
$f3->set('CACHE',FALSE);
$f3->config('config/config.ini');

$f3->route('GET /membergrid [ajax]','AjaxController->members');
//$f3->route('GET /membergrid','MyAjax->members');
$f3->route('GET /usergrid [ajax]','AjaxController->users');
$f3->route('POST /edituser [ajax]','AjaxController->edituser');

$f3->route('GET /','MemberController->index');
$f3->route('GET /payments','MemberController->payments'); 

$f3->route('POST /editmember [ajax]','AjaxController->editmember');

$f3->route('POST /app/views/markpaid [ajax]','AjaxController->markpaid'); 


$f3->route('GET /email1','EmailController->email1');
$f3->route('GET /subscribe1','EmailController->subscribe1'); 
$f3->route('GET /subscribe2','EmailController->subscribe2'); 
$f3->route('GET /batchsubscribe2','EmailController->batch_subscribe2'); 

$f3->route('GET /login','MemberController->login');
$f3->route('GET /logout','MemberController->logout');
$f3->route('POST /login','MemberController->auth');
$f3->route('GET /admin','AdminController->index');
$f3->route('GET /users','UserController->index');
$f3->route('GET /user/update/@usr','UserController->update');

$f3->route('GET /1','MemberController->index1');
$f3->route('GET /2','MemberController->index2');
//$f3->config('config/routes.ini');
$f3->run();
