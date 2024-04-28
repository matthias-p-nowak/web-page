<?php
/**
 * the only accessible php page in the public directory
 */
$site = 'example'; // determines the name of the ini-file
require '../web-app/bootstrap.php';

// do not edit below this line
$scriptURL = $_SERVER['SCRIPT_NAME'];
$i = stripos($scriptURL, basename(__FILE__));
$baseURL = substr($scriptURL, 0, $i);

// if the session cookie is set, the session is started
if (isset($_COOKIE[session_name()])) {
    error_log('starting session due to cookie');
    error_log(print_r($_COOKIE, true));
    session_start();
}

// routing section
$res = $_SERVER['PATH_INFO'] ?? '/home';
try {
    match ($res) {
        '/login' => (new WebApp\Login())->Login(),
        '/logout' => (new WebApp\Login())->Logout(),
        '/permissions' => (new WebApp\Login())->Permissions(),
        '/useradmin' => (new WebApp\Admin())->UserAdmin(),
        '/siteconfig' => (new WebApp\Admin())->SiteConfig(),
        '/home' => (new WebApp\Entry())->Page(''),
        '/pg' => (new WebApp\Entry())->Page($_SERVER['QUERY_STRING']),
        default =>(new WebApp\Entry())->Unknown($res),
    };
} catch (Exception $ex) {
    error_log("got exception $ex");
}finally{
    $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
    error_log("used $time seconds");
}
