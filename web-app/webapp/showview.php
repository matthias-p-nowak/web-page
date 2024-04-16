<?php
namespace WebApp;

require_once 'functions.php';

class ShowView
{
    function __construct()
    {
        global $config;
        $this->title=$config->title;
    }
    function ShowPage($page)
    {
        // error_log(__FILE__ .':'.__LINE__ .' showing page: '.$page);
        $this->content = $page;
        // allways start with the main stuff
        \view('main', $this);
    }
    function ShowForm($form)
    {
        global $config;
        // error_log(__FILE__ .':'.__LINE__ .' showing form: '.$form);
        $this->view = $form;
        $this->title .=  ' ' .$form;
        // allways start with the main stuff
        \view('main', $this);
    }
}
