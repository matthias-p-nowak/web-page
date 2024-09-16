<?php
namespace WebApp;

require_once 'functions.php';

class ShowView
{
    /** the title to show in the browser */
    public $title;
    public  $content;
    public  $background;
    public  $view;
    public  $description;

    function __construct()
    {
        global $config;
        $this->title=$config->title;
    }

    /**
     * Shows a static page as content
     * @param mixed $page a data structure containing pageId, name, and more
     */
    function ShowPage($page): void
    {
        error_log(__FILE__ .':'.__LINE__ .' showing page: '.print_r($page,true));
        $this->content = $page->PageId;
        $this->title .= ' '.$page->Name;
        $this->background=$page->Picture;
        $this->description=$page->Description;
        // allways start with the main stuff
        \view('main', $this);
    }

    /**
     * Shows a whole form starting with main
     * @param mixed $form
     */
    function ShowForm($form): void
    {
        global $config;
        // error_log(__FILE__ .':'.__LINE__ .' showing form: '.$form);
        $this->view = $form;
        $this->title .=  ' ' .$form;
        // allways start with the main stuff
        \view('main', $this);
    }

}
