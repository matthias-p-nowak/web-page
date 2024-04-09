<?php

$site = 'example';
require '../web-app/prep.php';

// do not edit below this line
$scriptURL = $_SERVER['SCRIPT_NAME'];
$i = stripos($scriptURL, basename(__FILE__));
$baseURL = substr($scriptURL, 0, $i);
// error_log('baseURL=' . $baseURL);

if (isset($_COOKIE[session_name()])) {
    error_log('starting session due to cookie');
    error_log(print_r($_COOKIE, true));
    session_start();
}

$res = $_SERVER['PATH_INFO'] ?? '/home';
try {match ($res) {
    '/upgrade' => WebApp\Db\DbCtx::GetInstance()->Upgrade(),
    '/home' => (new WebApp\ShowView())->ShowPage('home'),
    '/login' => (new WebApp\Login())->Login(),
    '/logout' => (new WebApp\Login())->Logout(),
    '/permissions' => (new WebApp\Login())->Permissions(),
    '/useradmin' => (new WebApp\Admin())->UserAdmin(),
    '/pg' => (new WebApp\Entry())->Page($_SERVER['QUERY_STRING']),
    default =>(new WebApp\Entry())->Unknown($res),
};} catch (Exception $ex) {
    error_log("got exception $ex");
}
