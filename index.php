<?php

$f3=require('lib/base.php');

if ((float)PCRE_VERSION<7.9)
	trigger_error('PCRE version is out of date');

$f3->config('config.ini');

$f3->set('AUTOLOAD', 
        __dir__.'/apps/;'
        );


$f3->route('GET|POST /', 'Dispatcher->DisplayHome');
$f3->route('GET|POST /@view', 'Dispatcher->DisplayView');
$f3->route('GET|POST /@view/@task', 'Dispatcher->DisplayView');
$f3->route('GET|POST /@view/@task/*', 'Dispatcher->DisplayView');

$f3->run();
