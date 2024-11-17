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
$oldPath = get_include_path();
$newPath = join(PATH_SEPARATOR, [$oldPath, __DIR__]);
set_include_path($newPath);
foreach (spl_autoload_functions() as $f) {
    spl_autoload_unregister($f);
}
spl_autoload_extensions('.php');
spl_autoload_register();

// get old state into memory
$dataFile = join(DIRECTORY_SEPARATOR, [__DIR__, $config->dataFile ?? 'data/data.bin']);
if (file_exists($dataFile)) {
    $content = file_get_contents($dataFile);
    if ($content) {
        // error_log('unserialzing cached instance');
        $data = unserialize($content);
    }
}
$data = $data ?? new stdClass();
$archive= join(DIRECTORY_SEPARATOR, [__DIR__, $config->archive ?? 'data/archive.zip']);

$scriptURL = $_SERVER['SCRIPT_NAME'];
$bn=basename($scriptURL);
$i = stripos($scriptURL, $bn);
$baseURL = substr($scriptURL, 0, $i);

if (isset($_COOKIE[session_name()])) {
    session_start();
}

$res = $_SERVER['PATH_INFO'] ?? '/home';
try {
    // routing section
    if (session_status() == PHP_SESSION_ACTIVE) {
        match ($res) {
            // '/home' => error_log('home'),
            '/edit' => Code\Editor::Edit(),
            '/duplText' => Code\Editor::Duplicate(),
            '/help' => Code\Help::ShowHelp(),
            '/login' => Code\Login::Login(),
            '/logout' => Code\Login::Logout(),
            '/makeeditor' => Code\MakeEditor::Add(),
            '/media' => Code\Media::Handle(),
            '/page' => Code\Page::Handle(),
            '/reindex' => Code\HtmlDoc::ReIndex(),
            '/saveState' => Code\Archive::SaveState(),
            '/saveText' => Code\Editor::SaveText(),
            default => noDefault(),
        };
    } else {
        Code\Login::Login();
    }
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

function noDefault(){
    http_response_code(404);
    $p=$_SERVER['PATH_INFO'];
    echo <<< EOM
    no default action, try login, got $p
    EOM;
}
