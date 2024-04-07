<?php

function show($fn)
{
    $fn = strtolower($fn);
    if (include (__DIR__ . '/content/' . $fn . '.html')) {
        return;
    }
    throw new \Exception("file $fn not found");
}

function view($fn, $arg)
{
    global $baseURL, $config;
    $fn = strtolower($fn);
    if (include (__DIR__ . '/views/' . $fn . '.php')) {
        return;
    }
    throw new \Exception("file $fn not found");
}

error_log('showview functions loaded');
