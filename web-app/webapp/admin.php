<?php
namespace WebApp;

class Admin{

    function UserAdmin(){
        Db\AppUser::AdminCheck();
        if(isset($_POST['UserId'])){
            error_log(print_r($_POST,true));
        }
        $sql='SELECT au.* FROM ${prefix}AppUser au LEFT JOIN ( SELECT * FROM ${prefix}UserPassword ORDER BY Used desc LIMIT 1 ) up ON au.UserId = up.UserId ORDER by au.Created';
        $db = Db\DbCtx::GetInstance();
        $sv=new ShowView();
        foreach($db->FetchRows($sql) as $userRow){
            error_log(print_r($userRow,true));
            $userRow->LevelStr= match($userRow->Level){
                Db\AppUser::Newbie => "None",
                Db\AppUser::Editor => "Editor",
                Db\AppUser::Admin => "Administrator",
                default => "Unknown"
            };
            $sv->users[]=$userRow;
            $sv->levels=['Newbie','Editor','Administrator','Remove account'];
        }
        $sv->ShowForm('UserAdminView');
    }
}