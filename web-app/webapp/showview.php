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

    /**
     * Shows a static page as content
     */
    function ShowPage($page)
    {
        error_log(__FILE__ .':'.__LINE__ .' showing page: '.print_r($page,true));
        $this->content = $page->Hash;
        $this->title .= ' '.$page->Name;
        $this->background=$page->Picture;
        // allways start with the main stuff
        \view('main', $this);
    }

    /**
     * Shows a whole form starting with main
     */
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
