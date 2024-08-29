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
        '/editpage' => (new WebApp\EditPage())->EditPage(),
        '/home' => (new WebApp\Entry())->Page(''),
        '/login' => (new WebApp\Login())->Login(),
        '/logout' => (new WebApp\Login())->Logout(),
        '/permissions' => (new WebApp\Login())->Permissions(),
        '/pictures' => (new WebApp\Admin())->Pictures(),
        '/pg' => (new WebApp\Entry())->Page($_SERVER['QUERY_STRING']),
        '/siteconfig' => (new WebApp\Admin())->SiteConfig(),
        '/upgradedb' => (new WebApp\Entry())->UpgradeDb(),
        '/useradmin' => (new WebApp\Admin())->UserAdmin(),
        '/testing' => (new WebApp\Testing())->Test(),
        default =>(new WebApp\Entry())->Unknown($res),
    };
} catch (Exception $ex) {
    error_log("got exception $ex");
}finally{
    $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
    $time = number_format($time,4) ;
    $included = \get_included_files();
    $incCnt= \count($included);
    $files=\print_r($included,true);
    error_log("used  $time seconds and $incCnt files: $files");

}
