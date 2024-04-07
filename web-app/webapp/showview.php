<?php
namespace WebApp;

require_once 'functions.php';

class ShowView
{
    function __construct()
    {
        error_log('ShowView constructed');
    }
    function ShowHomePage()
    {
        error_log('showing homepage');
        $o = new \stdClass();
        $o->content='home';
        \view('main', $o);
    }
}
