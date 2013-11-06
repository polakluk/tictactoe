<?php

$f3=require('lib/base.php');

if ((float)PCRE_VERSION<7.9)
	trigger_error('PCRE version is out of date');

$f3->config('config.ini');

$f3->route('GET|POST /', 'Dispatcher->display_home');
$f3->route('GET|POST /@view', 'Dispatcher->display_view');
$f3->route('GET|POST /@view/@task', 'Dispatcher->display_view');
$f3->route('GET|POST /@view/@task/*', 'Dispatcher->display_view');

$f3->run();
