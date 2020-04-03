<?php
namespace Payu\Util;

class Log
{
	static $Dir = 'Cache';
	static $File = '.notify.log';

	static function Msg($txt)
	{
		self::CreateDir();

		$file = $_SERVER['DOCUMENT_ROOT'].'/'.self::$Dir.'/.htaccess';
		if(!file_exists($file))
		{
			@file_put_contents($file, 'Require all denied');
		}

		if(!empty($txt))
		{
			$txt = time(). ' --- ' . $txt;
			@file_put_contents($_SERVER['DOCUMENT_ROOT'].'/'.self::$Dir.'/'.self::$File, $txt."\r\n", FILE_APPEND | LOCK_EX);
		}
	}

	static function CreateDir()
    {
        $p = $_SERVER['DOCUMENT_ROOT'].'/'.self::$Dir;
		if(!file_exists($p))
		{
			mkdir($p, 0700);
        }
    }
}