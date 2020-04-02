<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/init.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use \Exception;
use Payu\Config; // Change to your Config.php class
use Payu\Order\Order;
use Payu\Auth\Credentials;
use Payu\Db\Db;
use Payu\Db\PayuOrders;

try {

	if(empty($_GET['id']))
	{
		throw new Exception("ERR_ORDER_ID", 1);
	}

	$orderId = $_GET['id'];

	$auth = new Credentials();
	$token = $auth->Token(Config::PAYU_POS_ID, Config::PAYU_CLIENT_SECRET, Config::SANDBOX);

	// Full refund
	$amount = 0;

	// Part refund
	$amount = 202; // min. 200 => 2 PLN

	// Refund sample
	$obj = Order::Refund($orderId, (int) $amount, $token, Config::SANDBOX);

	// Show status code
	if($obj->status == 'SUCCESS')
	{
		echo 'Refunded';
		// print_r($obj);

		// Database
		$db = Db::GetInstance();
		$orders = new PayuOrders($db);
		$orders->AddRefund($obj);

	}else{
		echo "Ups errors!";
		print_r($obj);
	}

} catch (Exception $e) {
	echo $e->getMessage();
}

// Decimal to int
// $totalAmount = number_format($totalDecimal, 2, '.', '');
// $totalAmount = $totalAmount * 100;