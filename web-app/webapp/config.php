<?php
namespace WebApp;

/**
 * general cached configuration for easy fill of all pages
 */
class Config{

    const CacheFile = '/cache/content.data';
    private static $instance;

    /**
     * returns site values from memory, cached or created
     */
    public static function GetConfig(){
        return static::$instance ?? static::readInstance() ?? static::CreateInstance();
    }

    /**
     * tries to read from cache file
     */
    private static function readInstance(){
        $content=file_get_contents(__DIR__. self::CacheFile);
        if($content){
            error_log('unserialzing cached instance');
            $instance=unserialize($content);
            return $instance;
        }
        error_log('no cached instance');
        return;
    }

    /**
     * Creates a new instance from values in the database and stores a cached version.
     * Intended to be called from admin functions.
     */
    public static function CreateInstance(){
        error_log('creating a new config instance');
        $instance=new \stdClass();
        // TODO read related info from database
        $db = Db\DbCtx::GetInstance();
        foreach($db->FindRows('SiteConfig') as $cfg){
            error_log(print_r($cfg,true));
            $n=$cfg->Name;
            $instance->$n = $cfg->Value;
            $n .= 'Date';
            $instance->$n = $cfg->Modified;
        }
        file_put_contents(__DIR__. self::CacheFile, serialize($instance));
        self::$instance=$instance;
        return $instance;
    }



}