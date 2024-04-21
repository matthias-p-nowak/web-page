<?php
namespace WebApp;

class Config{

    const CacheFile = '/cache/content.data';
    private static $instance;

    public static function GetConfig(){
        return static::$instance ?? static::readInstance() ?? static::createInstance();
    }

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

    public static function createInstance(){
        error_log('creating a new config instance');
        $instance=new \stdClass();
        // TODO read related info from database
        $db = Db\DbCtx::GetInstance();
        self::$instance=$instance;
        return $instance;
    }



}