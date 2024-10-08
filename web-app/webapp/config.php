<?php
namespace WebApp;

use stdClass;


/**
 * general cached configuration for easy fill of all pages
 */
class Config{

    const CacheFile = '/cache/content.data';
    private static $instance;

    /**
     * returns site values from memory, cached or created
     */
    public static function GetConfig(): stdClass{
        return static::$instance ?? static::readInstance() ?? static::CreateInstance()
        ;
    }

    /**
     * tries to read from cache file
     */
    private static function readInstance(): mixed{
        $content=file_get_contents(__DIR__. self::CacheFile);
        if($content){
            // error_log('unserialzing cached instance');
            self::$instance=unserialize($content);
            return self::$instance;
        }
        error_log('no cached instance');
        $db = Db\DbCtx::GetInstance();
        $db->Upgrade();
        return null;
    }

    /**
     * Creates a new instance from values in the database and stores a cached version.
     * Intended to be called from admin functions.
     */
    public static function CreateInstance(): stdClass{
        error_log('creating a new config instance');
        $instance=new \stdClass();
        // TODO read related info from database
        $db = Db\DbCtx::GetInstance();
        foreach($db->FindRows('SiteConfig') as $cfg){
            // error_log(__FILE__.':'.__LINE__ .' '. print_r($cfg,true));
            $n=$cfg->Name;
            $instance->$n = $cfg->Value;
            $n .= 'Date';
            $instance->$n = $cfg->Modified;
        }
        $sql='SELECT `PageId`, `Position`, `Name`, `Picture`, `Description` FROM `${prefix}Page` where IsActive > 0 order by `Position`';
        foreach($db->FetchRows($sql) as $page){
            $instance->pages[$page->Position]=(object)$page;
        }
        $instance->mediaDir = dirname($_SERVER["SCRIPT_FILENAME"]) . DIRECTORY_SEPARATOR . 'media';
        file_put_contents(__DIR__. self::CacheFile, serialize($instance));
        self::$instance=$instance;
        return $instance;
    }



}