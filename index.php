<?php
// *****************  mcrud index.php *
//$f3=require('lib/base.php');

$f3 = require('lib/base.php');
$f3->set('CACHE',FALSE);
$f3->config('config/config.ini');

$f3->route('GET /membergrid [ajax]','MyAjax->members');
//$f3->route('GET /membergrid','MyAjax->members');
$f3->route('GET /usergrid [ajax]','MyAjax->users');
$f3->route('POST /edituser [ajax]','MyAjax->edituser');

$f3->route('GET /','MemberController->index');
$f3->route('POST /editmember [ajax]','MyAjax->editmember');
$f3->route('POST /app/views/markpaid [ajax]','MyAjax->markpaid'); 

$f3->route('GET /login','MemberController->login');
$f3->route('GET /logout','MemberController->logout');
$f3->route('POST /login','MemberController->auth');
$f3->route('GET /admin','AdminController->index');
$f3->route('GET /users','UserController->index');
$f3->route('GET /user/update/@usr','UserController->update');

//$f3->config('config/routes.ini');
$f3->run();
