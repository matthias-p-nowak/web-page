<?php

$site='example';
require('../web-app/prep.php');

// do not edit below this line
$baseURL=$_SERVER['SCRIPT_NAME'];
$i=stripos($baseURL,basename( __FILE__));
$baseURL=substr($baseURL,0,$i);
error_log('baseURL='.$baseURL);


$res = $_SERVER['PATH_INFO'] ?? '/home';
try { match ($res) {
        '/upgrade' => WebApp\Db\DbCtx::GetInstance()->Upgrade(),
        '/home' => (new WebApp\Entry())->Home(),
        '/login' => (new WebApp\Login())->Login(),
        default => (new WebApp\Entry())->Unknown($res),
    };
} catch (Exception $ex) {
    error_log("got exception $ex");
}