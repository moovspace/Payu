<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/init.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use \Exception;
use Payu\Config; // Change to your Config.php class
use Payu\Order\Order;
use Payu\Auth\Credentials;

try
{
	if(empty($_GET['id']))
	{
		throw new Exception("ERR_ORDER_ID", 1);
	}

	$orderId = $_GET['id'];

	// Autoryzacja
	$auth = new Credentials();

	// Pobierz token
	$token = $auth->Token(Config::PAYU_POS_ID, Config::PAYU_CLIENT_SECRET, Config::SANDBOX);

	// Utwórz link do płatności
	$obj = Order::Retrive($orderId, $token, Config::SANDBOX);

	// Pobierz status
	if($obj->status == 'SUCCESS')
	{
		echo "Order ID: " . $orderId . " retrived! </br>";
		print_r($obj);
	}
	else
	{
		echo "Ups errors!";
		print_r($obj);
	}

} catch (Exception $e) {
	echo $e->getMessage();
}