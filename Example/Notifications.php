<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/init.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use Payu\Config; // Change to your Config.php class
use Payu\Db\Db; // Change to your db class
use Payu\Db\PayuOrders;
use Payu\Notify\Notify;
use Payu\Order\Verify\Verify;
use Payu\Util\Log;

try
{
	// Get php://input data (save data to document root dir /Cache with TRUE)
	$res = Notify::Get(true);

	// Verify notification signature if you need
	$obj = Verify::Notification($res, Config::PAYU_MD5_KEY);

	// Database
	$db = Db::GetInstance();
	$orders = new PayuOrders($db);
	$ok = $orders->AddNotify($res);
	if($ok == 0){
		throw new Exception("ERR_DB_ORDER_CREATE", 1);
	}

	// Get notification order
	if(!empty($obj->response->order))
	{
		// Do something with
		$orderId = $obj->response->order->orderId;
		$extOrderId = $obj->response->order->extOrderId;
		$status = $obj->response->order->status;

		// Update orders table payment_status column
		$orders->UpdateOrderStatus($orderId, $status);

		// Confirm order
		Notify::StatusConfirmed();
	}

	// Get notification refund
	if(!empty($obj->response->refund))
	{
		// Do something with
		$orderId = $obj->response->orderId;

		// Save in database
		$orders->AddRefund($obj);

		// Confirm order
		Notify::StatusConfirmed();
	}

	// Error see in payu client panel order details
	Notify::StatusError('ERR_NOTIFY_DATA');
}
catch (Exception $e)
{
	$msg = $e->getMessage();

	// Log to file
	Log::Msg($msg);

	// Error see in payu client panel order details
	Notify::StatusError('ERR_EXCEPTION '. $msg);
}