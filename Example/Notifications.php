<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/init.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use Payu\Config; // Change to your Config.php class
use Payu\Db\Db;
use Payu\Notify\Notify;
use Payu\Order\Verify\Verify;
// Save to database
use Payu\Db\DbOrderNotify;
use Payu\Db\DbOrderConfirm;
use Payu\Db\DbUpdateShop;

try
{
	// Get php://input data (save data to document root dir /Cache with TRUE)
	$res = Notify::Get(true);

	// Verify notification signature if you need
	$obj = Verify::Notification($res, Config::PAYU_MD5_KEY);

	if(!empty($obj->response->order))
	{
		// Do something with
		$orderId = $obj->response->order->orderId;
		$extOrderId = $obj->response->order->extOrderId;

		// Save notifications
		$db = Db::GetInstance();
		$save = new DbOrderNotify($db);
		$ok = $save->Create($obj, Config::SANDBOX);

		// Update orders table payment_status column
		$up = new DbUpdateShop($db);
		$up->OrdersStatusUpdate($orderId, $extOrderId, Config::SANDBOX);

		/* TEST ORDER IN payment_orders TABLE OR CONFIRM ALL */
		$dbconf = new DbOrderConfirm($db);
		$confirmed = $dbconf->Valid($orderId, $extOrderId, Config::SANDBOX);

		if($confirmed > 0){
			// Confirm order
			Notify::StatusConfirmed();
		}else{
			// Error order
			Notify::StatusError('ERR_ORDER_ID_NOT_EXIST');
		}

		// !!! If order correct confirm
		// Notify::StatusConfirmed();
	}

	if(!empty($obj->refund))
	{
		// Do something with
		$orderId = $obj->refund->orderId;
		$extOrderId = $obj->refund->extOrderId;

		$db = Db::GetInstance();
		$save = new DbOrderRefund($db);
		// create and log to file
		$ok = $save->Create($obj, Config::SANDBOX);

		// !!! If order incorrect
		Notify::StatusConfirmed();
	}

	// Error see in payu client panel order details
	Notify::StatusError('ERR_DATA');
}
catch (Exception $e)
{
	$msg = $e->getMessage();

	// Error see in payu client panel order details
	Notify::StatusError('ERR_DATA_EXCEPTION '. $msg);
}