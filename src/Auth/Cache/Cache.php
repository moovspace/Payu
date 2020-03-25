<?php
namespace Payu\Auth\Cache;
use Payu\Config;

class Cache
{
    protected $Dir = 'Cache';
    public $Res = '';
    public $CachePath = '';
    public $DirPath = '';

    function __construct()
    {
        // Create file
        $this->CachePath();
    }

    function SaveToken($token, $expire)
    {
        $arr['expire'] = (int) $expire + time();
        $arr['token'] = $token;

        @file_put_contents($this->CachePath, json_encode($arr));
    }

    function GetToken()
    {
        $data = @file_get_contents($this->CachePath);
        $obj = json_decode($data);

        if(empty($obj->expire) || (int) $obj->expire < (time() - 60 * 60)){
            $this->Clear();
            return '';
        }else{
            return $obj->token;
        }
    }

    function Clear()
    {
        @file_put_contents($this->CachePath, '{}');
    }

    protected function CachePath()
    {
        $this->CreateDir();
        $this->CachePath = rtrim($this->DirPath, '/').'/'.$this->CacheFile();
    }

    protected function CreateDir()
    {
        $this->DirPath = $_SERVER['DOCUMENT_ROOT'].'/'.ltrim($this->Dir, '/');
        if(!file_exists($this->DirPath)){
            mkdir($this->DirPath, 0700);
            @file_put_contents(rtrim(self::$DirPath, '/').'/.htaccess', 'Require all denied');
        }
    }

    protected function CacheFile()
    {
        return '.'.md5(Config::PAYU_CLIENT_SECRET);
    }
}