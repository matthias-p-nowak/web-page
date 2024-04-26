<?php

namespace WebApp;

require_once 'functions.php';

use WebApp\Db\SiteConfig;

class Admin{

    function __construct(){
        // all admin functions require admin priviledges
        Db\AppUser::AdminCheck();
    }

    function UserAdmin(){
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
    
    private function ChangeUser(){
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

    function SiteConfig(){
        if(sizeof($_POST)>0){
            $db = Db\DbCtx::GetInstance();
            error_log('got a posting');
            if(isset($_POST['title'])){
                $configValue=$db->FindRow('SiteConfig',['Name'=> 'title']);
                error_log(print_r($configValue,true));
                if(!$configValue){
                    $configValue=new SiteConfig();
                    $configValue->Name='title';
                }
                $configValue->Value=htmlspecialchars( $_POST['title'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 , 'UTF-8');
                $configValue->Modified=date('Y-m-d H:i:s');
                $db->StoreRow($configValue);
                $sc=\WebApp\Config::CreateInstance();
                \view('admin/titleupdate',$sc);
                return;
            }
            if(isset($_POST['logo'])){
                $configValue=$db->FindRow('SiteConfig',['Name'=> 'logo']);
                error_log(print_r($configValue,true));
                if(!$configValue){
                    $configValue=new SiteConfig();
                    $configValue->Name='logo';
                }
                $configValue->Value=htmlspecialchars( $_POST['logo'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 , 'UTF-8');
                $configValue->Modified=date('Y-m-d H:i:s');
                $db->StoreRow($configValue);
                $sc=\WebApp\Config::CreateInstance();
                \view('admin/logoupdate',$sc);
                return;
            }

        }
        $sv=new ShowView();
        $sv->htmx_lite=true;
        $sv->ShowForm('admin/SiteConfig');
    }
}
