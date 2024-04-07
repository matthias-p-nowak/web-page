<?php

namespace WebApp;

class Entry
{
    function __construct()
    {
        error_log('Entry controller constructed');
    }

    function Default(){
        $view= new ShowView();
        $view->ShowHomePage();
    }
}
