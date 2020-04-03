<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/init.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use \Exception;
use Payu\Config; // Change to your Config.php class
use Payu\Db\Db; // Change to your Db class
use Payu\Db\PayuOrders;
use Payu\Order\Order;
use Payu\Order\CartOrder;
use Payu\Auth\Credentials;

try
{
	// Database
	$db = Db::GetInstance();
	$orders = new PayuOrders($db);

	// Unikalny id zamówienia w twoim sklepie
	$shopId = $orders->TestOrder(); // Create sample order

	// Zamówienie w payu
	$o = new CartOrder();

	// Powiadomienia
	$o->UrlContinue('https://twoja.strona.www/Example/notify/success.php');
	$o->UrlNotify('https://twoja.strona.www/Example/Notifications.php');

	// Produkty
	$o->Add($shopId, 1555, 'Zamówienie '.$shopId, 'PLN', Config::PAYU_POS_ID, $_SERVER['REMOTE_ADDR']);
	$o->AddProduct('Zamówienie-'.$shopId, 1555, 1);
	$o->AddBuyer('email@domain.xx', '+48 100 100 100', 'Anka', 'Specesetka', 'pl');
	$order = $o->Get();

	// Autoryzacja
	$auth = new Credentials();

	// Pobierz token
	$token = $auth->Token(Config::PAYU_POS_ID, Config::PAYU_CLIENT_SECRET, Config::SANDBOX);

	// Utwórz link do płatności
	$obj = Order::Create((array) $order, $token, Config::SANDBOX);

	// Pobierz status
	if($obj->status == 'SUCCESS')
	{
		$orderId = $obj->response->orderId;
		$extOrderId = $obj->response->extOrderId;
		$paymentUrl = $obj->response->redirectUri;

		// Add order db
		$ok = $orders->AddOrderId($orderId, $shopId, $paymentUrl);

		if($ok == 0){
			throw new Exception("ERR_DB_ORDER_CREATE", 1);
		}

		echo "</br> OrderId: " . $orderId;
		echo "</br> ExtOrderId: " . $extOrderId;
		/*
			Jak wsio ok to wyświetl link do płatności
			lub przekieruj na ten url z header('Location: '.$paymentUrl);
		*/
		echo '</br> <a href="'.$paymentUrl.'" target="__blank"> Pay Now </a>';

		// Opcje
		echo '</br></br> <a href="/Example/OrderRetrive.php?id='.$orderId.'" target="__blank"> Retrive </a>';
		echo '</br></br> <a href="/Example/OrderCancel.php?id='.$orderId.'" target="__blank"> Cancel </a>';
		echo '</br></br> <a href="/Example/OrderRefresh.php?id='.$orderId.'" target="__blank"> Refresh </a>';
		echo '</br></br> <a href="/Example/OrderRefund.php?id='.$orderId.'" target="__blank"> Refund </a>';
	}
	else
	{
		// Add order
		$orders->UpdateOrderError($orderId, json_encode($obj));

		echo "Ups errors!";
		print_r($obj);
	}

} catch (Exception $e) {
	echo $e->getMessage();
}