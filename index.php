<?php

$f3=require('lib/base.php');
$f3->config('config/config.ini');

$f3->route('GET /example [ajax]','MyAjax->example');


$f3->route('POST /example [ajax]', function($f3,$params) {
		
        echo  'ELLO POST AJAX';		
    });
	$f3->route('GET /','MemberController->index');
	$f3->route('GET /@mylist','MemberController->listn');
//$f3->config('config/routes.ini');
$f3->run();
