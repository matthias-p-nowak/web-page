<?php

/**
 * Safe function that emits the content of an HTML file within content
 */
function show($page)
{
    $fn = strtolower($page);
    $fn = __DIR__ . '/content/page-' . $fn . '.html';
    if (file_exists($fn)) {
        echo ('<article>');
        echo (file_get_contents($fn, false));
        echo ('</article>');
        return;
    }else{
        \WebApp\Db\AppUser::EditorCheck();
        $arg= \WebApp\Config::GetConfig();
        $arg->page2edit=$page;
        $arg->pageFile=$fn;
        view('admin/editpage',$arg);
    }
}

/**
 * Shows a PHP file from views and also offers $baseURL, $scriptURL and $config
 */
function view($fn, $arg)
{
    global $baseURL, $scriptURL, $config;
    $fn = strtolower($fn);
    if (include (__DIR__ . '/views/' . $fn . '.php')) {
        return;
    }
    throw new \Exception("file $fn.php not found");
}

function idHash($arg)
{
    return \hash('xxh64',$arg);
}
