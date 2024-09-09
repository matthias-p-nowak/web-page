<?php

namespace WebApp;

require_once 'functions.php';

use WebApp\Db\SiteConfig;

/**
 * Functions providing admin functionality
 */
class Admin
{

    private $good_types = ['image/svg+xml', 'image/png', 'image/jpeg', 'application/pdf'];

    // TODO: remove old users

    function __construct()
    {
        // all admin functions require admin priviledges
        Db\AppUser::AdminCheck();
    }

    /**
     * retrieve users and their levels
     * @return void
     */
    function UserAdmin(): void
    {
        if (isset($_POST['UserId'])) {
            $this->ChangeUser();
            return;
        }
        $db = Db\DbCtx::GetInstance();
        $sql = 'SELECT au.* FROM ${prefix}AppUser au LEFT JOIN ( SELECT * FROM ${prefix}UserPassword ORDER BY Used desc LIMIT 1 ) up ON au.UserId = up.UserId ORDER by au.Created';
        $sv = new ShowView();
        foreach ($db->FetchRows($sql) as $userRow) {
            error_log(__FILE__.':'.__LINE__ .' '. print_r($userRow, true));
            $userRow->LevelStr = Db\AppUser::Levels[$userRow->Level];
            $sv->users[] = $userRow;
            // $sv->levels=['Newbie','Editor','Administrator','Remove account'];
        }
        $sv->ShowForm('admin/useradmin');
    }

    /**
     * Reaction to post, change user level accordingly or remove user
     * @return void
     */
    private function ChangeUser(): void
    {
        error_log(__FILE__.':'.__LINE__ .' '. print_r($_POST, true));
        $db = Db\DbCtx::GetInstance();
        $userId = intval($_POST['UserId']);
        $userRow = $db->FindRow('AppUser', ['UserId' => $userId]);
        error_log(__FILE__.':'.__LINE__ .' '. print_r($userRow, true));
        $newLevel = array_search($_POST['NewLevel'], Db\AppUser::Levels, true);
        $uv = new ShowUpdates();
        if ($newLevel === -1) {
            $db->DeleteRow($userRow);
            $uv->UserId = $userId;
            $uv->ShowUpdate('updates/deleteuserrow');
            exit(0);
        } else {
            $userRow->Level = $newLevel;
            $db->StoreRow($userRow);
            $uv->UserId = $userId;
            $uv->LevelStr = Db\AppUser::Levels[$newLevel];
            $uv->ShowUpdate('updates/updateuserlevel');
            exit(0);
        }
    }
    /**
     * @return void
     */
    function SiteConfig(): void
    {
        if (sizeof($_POST) > 0) {
            $db = Db\DbCtx::GetInstance();
            if (isset($_POST['title'])) {
                error_log(__FILE__.':'.__LINE__);
                $configValue = $db->FindRow('SiteConfig', ['Name' => 'title']);
                error_log(__FILE__.':'.__LINE__ .' '. print_r($configValue, true));
                if (!$configValue) {
                    $configValue = new SiteConfig();
                    $configValue->Name = 'title';
                }
                $configValue->Value = Sanitizer::PlainText($_POST['title']);
                $configValue->Modified = date('Y-m-d H:i:s');
                $db->StoreRow($configValue);
                // recreates from db
                $sc = \WebApp\Config::CreateInstance();
                \view('updates/titleupdate', $sc);
                return;
            } else if (isset($_POST['slogan'])) {
                error_log(__FILE__.':'.__LINE__);
                $configValue = $db->FindRow('SiteConfig', ['Name' => 'slogan']);
                error_log(__FILE__.':'.__LINE__ .' '. print_r($configValue, true));
                if (!$configValue) {
                    $configValue = new SiteConfig();
                    $configValue->Name = 'slogan';
                }
                $configValue->Value = Sanitizer::PlainText($_POST['slogan']);
                $configValue->Modified = date('Y-m-d H:i:s');
                $db->StoreRow($configValue);
                // recreates from db
                $sc = \WebApp\Config::CreateInstance();
                \view('updates/sloganupdate', $sc);
                return;
            } else if (isset($_POST['pageid'])) {
                error_log(__FILE__.':'.__LINE__);
                if ($_POST['pageid'] === 'new') {
                    error_log(__FILE__ . ':' . __LINE__);
                    $page = new Db\Page();
                    $sql = 'SELECT max(Position) as `pos` FROM `${prefix}Page`';
                    foreach ($db->FetchRows($sql) as $max) {
                        $maxPos = $max->pos;
                    }
                    $page->Position = $maxPos + 1;
                    $sql = 'SELECT max(PageId) as `pageId` FROM `${prefix}Page`';
                    foreach ($db->FetchRows($sql) as $max) {
                        $maxPageId = $max->pageId;
                    }
                    $page->PageId = $maxPageId + 1;
                    $page->IsActive=1;
                    $name = trim($_POST['page_name']);
                    $name = Sanitizer::PlainText($name);
                    $page->Name = $name;
                    $db->StoreRow($page);
                    // recreates from db
                    $sc = \WebApp\Config::CreateInstance();
                    $sc->newPage = $page;
                    \view('updates/updatepages', $sc);
                    return;
                } else if (is_numeric($_POST['pageid'])) {
                    error_log(__FILE__.':'.__LINE__);
                    $name = trim($_POST['page_name']);
                    if ($name === '') {
                        $pageId=(int) $_POST['pageid'];
                        $criteria=['PageId' =>  $pageId];
                        $row = $db->FindRow('Page', $criteria);
                        $sql = 'DELETE from `${prefix}Page`';
                        $db->ExecuteSQL($sql, $criteria);
                        /*
                        $db->DeleteRow($row);
                        */
                        $sc = \WebApp\Config::CreateInstance();
                        $sc->deletedPage = $row;
                        \view('updates/updatepages', $sc);
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

    /**
     * @return void
     */
    function Pictures(): void
    {
        // \WebApp\Config::CreateInstance();
        // TODO: handling uploaded pictures
        $sv = new ShowView();
        error_log(__FILE__.':'.__LINE__ .' '. print_r($_POST, true));
        if (isset($_FILES['files'])) {
            $files = $_FILES['files']; // same name as the file-input field
            $l = count($files['tmp_name']);
            $destDir = dirname($_SERVER["SCRIPT_FILENAME"]) . DIRECTORY_SEPARATOR . 'media';
            if(! \is_dir($destDir)){
                error_log('creating directory '.$destDir);
                mkdir($destDir, 0511, true);
            }
            for ($i = 0; $i < $l; $i++) {
                if (in_array($files['type'][$i], $this->good_types)) {
                    $dest = $destDir . DIRECTORY_SEPARATOR . $files['name'][$i];
                    copy($files['tmp_name'][$i], $dest);
                    error_log('copied file ' . $dest);
                } else {
                    $msg = 'ignoring file ' . $files['name'][$i] . ', type=' . $files['type'][$i];
                    error_log($msg);
                }
            }
            // TODO adapt the views
            return;
        }
        if(isset($_POST['del'])){
            return;
        }
        $sv->ShowForm('admin/Pictures');
    }
}
