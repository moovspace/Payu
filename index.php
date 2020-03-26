<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/init.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use \Exception;
use Payu\Config; // Change to your Config.php class
use Payu\Order\Order;
use Payu\Order\CartOrder;
use Payu\Auth\Credentials;
use Payu\Db\Db;
use Payu\Db\DbOrder;
use Payu\Db\DbUpdateShop;

try
{
	// Unikalny id zamówienia w twoim sklepie
	$extOrderId = md5(uniqid('', true));

	// Zamówienie w payu
	$o = new CartOrder();

	// Powiadomienia
	$o->UrlContinue('https://twoja.strona.www/Example/notify/success.php');
	$o->UrlNotify('https://twoja.strona.www/Example/Notifications.php');

	// Produkty
	$o->Add($extOrderId, 25569, 'Zamówienie '.$extOrderId, 'PLN', Config::PAYU_POS_ID, $_SERVER['REMOTE_ADDR']);
	$o->AddProduct('Zamówienie-'.$extOrderId, 25569, 1);
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

		echo "</br> OrderId: " . $orderId;
		echo "</br> ExtOrderId: " . $extOrderId;

		/*
			SAVE PAYU ORDER IN DATABASE
		*/
		$db = Db::GetInstance();
		$save = new DbOrder($db);
		$ok = $save->Create($obj, $order, Config::SANDBOX);

		/*
			UPDATE shop orders TABLE payment_orderId COLUMN
		*/
		$update = new DbUpdateShop($db);
		$ok = $update->OrdersOrderId($orderId, $extOrderId);

		if($ok == 0){
			// Orders table update error
			// echo "</br> Zamówienie o takim extOrderId w orders nie istnieje!";
		}

		/*
			Jak wsio ok to wyświetl link do płatności
			lub przekieruj na ten url z header('Location: '.$paymentUrl);
		*/
		echo '</br> <a href="'.$paymentUrl.'" target="__blank"> Pay Now </a>';

		echo '</br></br> <a href="/Example/OrderRetrive.php?id='.$orderId.'" target="__blank"> Retrive </a>';
		echo '</br></br> <a href="/Example/OrderCancel.php?id='.$orderId.'" target="__blank"> Cancel </a>';
		echo '</br></br> <a href="/Example/OrderRefresh.php?id='.$orderId.'" target="__blank"> Refresh </a>';
		echo '</br></br> <a href="/Example/OrderRefund.php?id='.$orderId.'" target="__blank"> Refund </a>';


	}
	else
	{
		echo "Ups errors!";
		print_r($obj);
	}

} catch (Exception $e) {
	echo $e->getMessage();
}