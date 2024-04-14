<?php
namespace WebApp;

class Admin{

    function UserAdmin(){
        Db\AppUser::AdminCheck();
        if(isset($_POST['UserId'])){
            $this->ChangeUser();
            return;
        }
        $db = Db\DbCtx::GetInstance();
        $sql='SELECT au.* FROM ${prefix}AppUser au LEFT JOIN ( SELECT * FROM ${prefix}UserPassword ORDER BY Used desc LIMIT 1 ) up ON au.UserId = up.UserId ORDER by au.Created';
        $sv=new ShowView();
        foreach($db->FetchRows($sql) as $userRow){
            error_log(print_r($userRow,true));
            $userRow->LevelStr = Db\Appuser::Levels[$userRow->Level];
            $sv->users[]=$userRow;
            // $sv->levels=['Newbie','Editor','Administrator','Remove account'];
        }
        $sv->ShowForm('admin/UserAdminView');
    }
    
    function ChangeUser(){
        error_log(print_r($_POST,true));
        $db = Db\DbCtx::GetInstance();
        $userId=$_POST['UserId'];
        $userRow=$db->FindRow('AppUser',['UserId' => $userId]);
        error_log(print_r($userRow,true));
        $newLevel=$_POST['NewLevel'];
        $newLevel=array_search($newLevel,Db\AppUser::Levels);
        $uv=new ShowUpdates();
        if($newLevel===-1){
            $db->DeleteRow($userRow);
            $uv->UserId=$userId;
            $uv->ShowUpdate('admin/deleteuserrow');
            exit(0);
        }else{
            $userRow->Level=$newLevel;
            $db->StoreRow($userRow);
            $uv->UserId=$userId;
            $uv->LevelStr=Db\AppUser::Levels[$newLevel];
            $uv->ShowUpdate('admin/updateuserlevel');
            exit(0);
        }
    }
}