<?php
namespace Payu\Notify;

abstract class AddressIP
{
	const PRODUCTION = ['185.68.12.10', '185.68.12.11', '185.68.12.12', '185.68.12.26', '185.68.12.27', '185.68.12.28'];
	const SANDBOX = ['185.68.14.10', '185.68.14.11', '185.68.14.12', '185.68.14.26', '185.68.14.27', '185.68.14.28'];

	static function Allowed()
	{
		if(!in_array($_SERVER['REMOTE_ADDR'], self::SANDBOX) && !in_array($_SERVER['REMOTE_ADDR'], self::PRODUCTION))
		{
			echo "ERR_IP_ADDRESS";
			header("HTTP/1.1 402 Error Ip address");
			exit;
		}
	}
}