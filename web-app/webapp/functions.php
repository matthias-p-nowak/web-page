<?php

function show($fn)
{
    if(include(__DIR__ . '/content/' . $fn . '.html')){
        return;
    }
    throw new \Exception("file $fn not found");
}

function view($fn, $arg)
{
    if(include(__DIR__ . '/views/' . $fn . '.php')){
        return;
    }
    throw new \Exception("file $fn not found");
}

error_log('showview functions loaded');
