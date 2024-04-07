<?php
namespace WebApp;

require_once 'functions.php';

class ShowView
{
    function __construct()
    {
        global $config;
        $this->title=$config->title;
        error_log('ShowView constructed');
    }
    function ShowPage($page)
    {
        error_log('showing page: '.$page);
        $this->content = $page;
        // allways start with the main stuff
        \view('main', $this);
    }
    function ShowForm($form)
    {
        global $config;
        $this->view = $form;
        $this->title .=  ' ' .$form;
        // allways start with the main stuff
        \view('main', $this);
    }
}
