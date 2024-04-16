<?php
namespace WebApp\Db;

class AppUser{
    const Newbie=0;
    const Editor=1;
    const Admin=2;

    const Levels = array ( -1 => 'Removed', self::Newbie => 'Newbie', self::Editor => 'Editor', self::Admin => 'Admin');

    static function AdminCheck(){
        global $scriptURL;
        if($_SESSION['Level']< self::Admin){
            header("Refresh: 5; URL=".$scriptURL);
            echo('Request was not authorized');
            exit(0);
        }
    }

}
