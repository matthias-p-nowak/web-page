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

    /**
     * show home page
     */
    function Home(){
        $view= new ShowView();
        $view->ShowPage('home');
    }

    function Unknown($res){
        error_log('no handler for path_info '.$res);
        $this->Home();
    }
    function Page($page){
        $pat='/([a-zA-Z0-9_]+)/';
        if(preg_match($pat,$page,$matches)){
            error_log(print_r($matches,true));
            $view=new ShowView();
            $view->ShowPage($matches[1]);
        }else{
            $this->Home();
        }
    }
}

error_log(__FILE__ .' read');
