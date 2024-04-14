<?php

function show($fn)
{
    $fn = __DIR__ . '/content/' . strtolower($fn) . '.html';
    if (file_exists($fn)) {
        echo (file_get_contents($fn, false));
        return;
    }
    throw new \Exception("file $fn not found");
}

function view($fn, $arg)
{
    global $baseURL, $scriptURL, $config;
    $fn = strtolower($fn);
    if (include (__DIR__ . '/views/' . $fn . '.php')) {
        return;
    }
    throw new \Exception("file $fn not found");
}

function idHash($arg)
{
    return \hash('xxh64',$arg);
}

error_log('showview functions loaded');
