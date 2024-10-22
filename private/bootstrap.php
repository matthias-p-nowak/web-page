<?php


$config = __DIR__ . '/site.ini';
// reading config file from a secure location
if (file_exists($config)) {
    $config = parse_ini_file($config, true);
} else {
    error_log('no site file found at ' . $config);
    $config = [];
}

$config = (object) $config;

if (isset($config->timezone)) {
    date_default_timezone_set($config->timezone);
}

// setting auto loader to this folder
$oldPath=get_include_path();
$newPath=join(PATH_SEPARATOR,[$oldPath,__DIR__]); 
set_include_path($newPath);
foreach (spl_autoload_functions() as $f) {
    spl_autoload_unregister($f);
}
spl_autoload_extensions('.php');
spl_autoload_register();

// get old state into memory
$dataFile = join(DIRECTORY_SEPARATOR, [__DIR__, $config->dataFile]);
if (file_exists($dataFile)) {
    $content = file_get_contents($dataFile);
    if ($content) {
        // error_log('unserialzing cached instance');
        $data = unserialize($content);
    }
}
$data=$data ?? new stdClass();

$scriptURL = $_SERVER['SCRIPT_NAME'];
$i = stripos($scriptURL, basename(__FILE__));
$baseURL = substr($scriptURL, 0, $i);

if (isset($_COOKIE[session_name()])) {
    session_start();
}

$res = $_SERVER['PATH_INFO'] ?? '/home';
try {
    // routing section
    match ($res) {
        // '/home' => error_log('home'),
        '/makeeditor' => Code\MakeEditor::Add(),
        '/login' => Code\Login::Login(),
        '/logout' => Code\Login::Logout(),
        default => http_response_code(404),
    };
} catch (Exception $ex) {
    error_log("got exception $ex");
} finally {
    // logging statistics
    $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
    $time = number_format($time, 4);
    $included = \get_included_files();
    $incCnt = \count($included);
    $files = \print_r($included, true);
    error_log("used  $time seconds and $incCnt files: $files");
    // saving state to datafile
    file_put_contents($dataFile, serialize($data));
}
