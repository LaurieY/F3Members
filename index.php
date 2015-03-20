<?php

$f3=require('lib/base.php');
$f3->config('config/config.ini');

$f3->route('GET /membergrid [ajax]','MyAjax->example');


$f3->route('POST /example [ajax]', function($f3,$params) {
		
        echo  'ELLO POST AJAX';		
    });
	$f3->route('GET /','MemberController->index');
		$f3->route('GET /admin','AdminController->index');
$f3->route('GET /login','MemberController->login');
$f3->route('GET /logout','MemberController->logout');
$f3->route('POST /login','MemberController->auth');
$f3->route('GET /sessionly','MemberController->sessionly');

//$f3->config('config/routes.ini');
$f3->run();
