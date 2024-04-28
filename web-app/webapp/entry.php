<?php

namespace WebApp;

/**
 * intentionally lean class that comprises useful methods
 */
class Entry
{
    function __construct()
    {
        error_log('Entry controller constructed');
    }

    function Unknown($res)
    {
        error_log('no handler for path_info ' . $res);
        $this->Page('');
    }

    function Page($hash)
    {
        $sc = Config::GetConfig();
        $view = new ShowView();
        if ($res = Sanitizer::CheckHash($hash)) {
            foreach ($sc->pages as $page) {
                if ($page->Hash === $hash) {
                    $view->ShowPage($res);
                }
            }
        } else {
            foreach ($sc->pages as $page) {
                $view->ShowPage($page);
                return;
            }
        }
    }
}

error_log(__FILE__ . ' read');

