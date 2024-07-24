<?php

namespace WebApp;

require_once 'functions.php';

use WebApp\Db\SiteConfig;

/**
 * Functions providing admin functionality
 */
class Admin
{

    // TODO: remove old users

    function __construct()
    {
        // all admin functions require admin priviledges
        Db\AppUser::AdminCheck();
    }

    /**
     * retrieve users and their levels
     */
    function UserAdmin()
    {
        if (isset($_POST['UserId'])) {
            $this->ChangeUser();
            return;
        }
        $db = Db\DbCtx::GetInstance();
        $sql = 'SELECT au.* FROM ${prefix}AppUser au LEFT JOIN ( SELECT * FROM ${prefix}UserPassword ORDER BY Used desc LIMIT 1 ) up ON au.UserId = up.UserId ORDER by au.Created';
        $sv = new ShowView();
        foreach ($db->FetchRows($sql) as $userRow) {
            error_log(print_r($userRow, true));
            $userRow->LevelStr = Db\Appuser::Levels[$userRow->Level];
            $sv->users[] = $userRow;
            // $sv->levels=['Newbie','Editor','Administrator','Remove account'];
        }
        $sv->ShowForm('admin/useradmin');
    }

    /**
     * Reaction to post, change user level accordingly or remove user
     */
    private function ChangeUser()
    {
        error_log(print_r($_POST, true));
        $db = Db\DbCtx::GetInstance();
        $userId = intval($_POST['UserId']);
        $userRow = $db->FindRow('AppUser', ['UserId' => $userId]);
        error_log(print_r($userRow, true));
        $newLevel = array_search($_POST['NewLevel'], Db\AppUser::Levels, true);
        $uv = new ShowUpdates();
        if ($newLevel === -1) {
            $db->DeleteRow($userRow);
            $uv->UserId = $userId;
            $uv->ShowUpdate('admin/deleteuserrow');
            exit(0);
        } else {
            $userRow->Level = $newLevel;
            $db->StoreRow($userRow);
            $uv->UserId = $userId;
            $uv->LevelStr = Db\AppUser::Levels[$newLevel];
            $uv->ShowUpdate('admin/updateuserlevel');
            exit(0);
        }
    }

    function SiteConfig()
    {
        if (sizeof($_POST) > 0) {
            $db = Db\DbCtx::GetInstance();
            if (isset($_POST['title'])) {
                $configValue = $db->FindRow('SiteConfig', ['Name' => 'title']);
                error_log(print_r($configValue, true));
                if (!$configValue) {
                    $configValue = new SiteConfig();
                    $configValue->Name = 'title';
                }
                $configValue->Value = Sanitizer::PlainText($_POST['title']);
                $configValue->Modified = date('Y-m-d H:i:s');
                $db->StoreRow($configValue);
                // recreates from db
                $sc = \WebApp\Config::CreateInstance();
                \view('admin/titleupdate', $sc);
                return;
            } else if (isset($_POST['logo'])) {
                $configValue = $db->FindRow('SiteConfig', ['Name' => 'logo']);
                error_log(print_r($configValue, true));
                if (!$configValue) {
                    $configValue = new SiteConfig();
                    $configValue->Name = 'logo';
                }
                $configValue->Value = Sanitizer::PlainText($_POST['logo']);
                $configValue->Modified = date('Y-m-d H:i:s');
                $db->StoreRow($configValue);
                // recreates from db
                $sc = \WebApp\Config::CreateInstance();
                \view('admin/logoupdate', $sc);
                return;
            } else if (isset($_POST['page_hash'])) {
                if ($_POST['page_hash'] === 'new') {
                    $sql = 'SELECT max(Position) as `pos` FROM `${prefix}Page`';
                    foreach ($db->FetchRows($sql) as $max) {
                        $maxPos = $max->pos;
                    }
                    $page = new Db\Page();
                    $page->Position = $maxPos + 1;
                    $name = trim($_POST['page_name']);
                    $name = Sanitizer::PlainText($name);
                    $page->Name = $name;
                    $page->Hash = \hash('xxh64', $name . date('YmdHis'));
                    $db->StoreRow($page);
                    // recreates from db
                    $sc = \WebApp\Config::CreateInstance();
                    $sc->newPage = $page;
                    \view('admin/updatepages', $sc);
                    return;
                } else if ($pageHash = Sanitizer::CheckHash($_POST['page_hash'])) {
                    $name = trim($_POST['page_name']);
                    if ( $name === '') {
                        $row = $db->FindRow('Page', ['Hash' => $pageHash]);
                        $db->DeleteRow($row);
                        $sc = \WebApp\Config::CreateInstance();
                        $sc->deletedPage = $row;
                        \view('admin/updatepages', $sc);
                        return;
                    }
                    $name = Sanitizer::PlainText($name);
                }
            } else {
                error_log(__FILE__ . ':' . __LINE__ . ': not yet implemented ' . print_r($_POST, true));
            }
            die();
        }
        \WebApp\Config::CreateInstance();
        $sv = new ShowView();
        $sv->ShowForm('admin/SiteConfig');
    }

    function Pictures(){
        \WebApp\Config::CreateInstance();
        $sv = new ShowView();
        $sv->ShowForm('admin/Pictures');
    }
}
