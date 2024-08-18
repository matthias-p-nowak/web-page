<?php
/**
 * Contains the site configuration
 * @var $site contains the name
 */
$config = __DIR__ . '/' . $site . '.ini';
// reading config file from a secure location
if (file_exists($config)) {
    $config = parse_ini_file($config, true);
} else {
    error_log('no site file found at '.$config);
    $config = [];
}

/**
 * turning the nested array into a nested object
 */ 
function TurnIntoObject(array $array): object
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = TurnIntoObject($value);
        }
    }
    return (object) $array;
}

/**
 * applying recursive function
 */
$config = TurnIntoObject($config);

if (isset($config->timezone)) {
    date_default_timezone_set($config->timezone);
}

// setting auto loader to this folder
set_include_path(__DIR__);
foreach (spl_autoload_functions() as $f) {
    spl_autoload_unregister($f);
}
spl_autoload_extensions('.php');
spl_autoload_register();

error_log(__FILE__ .' read');

