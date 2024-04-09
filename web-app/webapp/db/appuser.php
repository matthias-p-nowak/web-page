<?php
namespace WebApp\Db;

class AppUser{
    const Newbie=0;
    const Editor=1;
    const Admin=2;

    static function AdminCheck(){
        global $scriptURL;
        if($_SESSION['Level']< self::Admin){
            header("Refresh: 5; URL=".$scriptURL);
            echo('Request was not authorized');
            exit(0);
        }
    }

}