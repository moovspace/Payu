<?php
namespace Payu\Db;
use \Exception;
use Payu\Notify\NotifyStatus;

class PayuOrders
{
	public $Db = null;

	function __construct($db = null)
	{
		if(empty($db)){
			throw new Exception("ERR_DB_CLASS", 9011);
		}

		$this->Db = $db;
	}

	/**
	 * Add notification response from payu to database
	 *
	 * @param string $data
	 * @return void
	 */
	function AddNotify($data)
	{
		if(empty($data)){
			throw new Exception("ERR_DATA", 9011);
		}

		$db = $this->Db;
		$r = $db->Pdo->prepare("INSERT INTO payment_order_notify(data) VALUES(:data)");
		$r->execute([':data' => strip_tags($data)]);
		return $db->Pdo->lastInsertId();
	}

	/**
	 * Update orders table payment_orderId with payu orderId
	 *
	 * @param string $orderId Payu orderid
	 * @param string $shopId User shop id
	 * @return int
	 */
	function AddOrderId($orderId, $shopId, $url = '', $table = 'orders', $gtw = 'PAYU')
	{
		if(empty($orderId) || empty($shopId)){
			throw new Exception("ERR_DATA", 9011);
		}

		if(!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED)){
			throw new Exception("ERR_PAYMENT_URL", 9011);
		}

		$db = $this->Db;
		$r = $db->Pdo->prepare("UPDATE ".strip_tags($table)." SET payment_orderId = :oid, payment_gateway = :gtw, payment_url = :url WHERE id = :id");
		$r->execute([':oid' => strip_tags($orderId), ':gtw' => strip_tags($gtw), ':url' => strip_tags($url), ':id' => (int) $shopId]);
		return $r->rowCount();
	}

	/**
	 * Update order status
	 *
	 * @param string $orderId Payu orderid
	 * @param string $status payu order status
	 * @param string $table Table name
	 * @return int
	 */
	function UpdateOrderStatus($orderId, $status = '', $table = 'orders')
	{
		if(empty($orderId) || empty($status) || !in_array($status, NotifyStatus::STATUS_ALL)){
			throw new Exception("ERR_DATA", 9011);
		}

		$db = $this->Db;
		$r = $db->Pdo->prepare("UPDATE ".strip_tags($table)." SET payment_status = :status, payment_refresh = current_timestamp() WHERE payment_orderId = :oid AND payment_status != 'COMPLETED' AND payment_status != 'CANCELED'");
		$r->execute([':status' => strip_tags($status), ':oid' => strip_tags($orderId)]);
		return 1;
	}

	/**
	 * Update order error
	 *
	 * @param string $orderId Payu orderid
	 * @param string $error Payu order error msg
	 * @param string $table Table name
	 * @return int
	 */
	function UpdateOrderError($orderId, $err = '', $table = 'orders')
	{
		if(empty($orderId) || empty($err)){
			throw new Exception("ERR_DATA", 9011);
		}

		$db = $this->Db;
		$r = $db->Pdo->prepare("UPDATE ".strip_tags($table)." SET payment_error = :err WHERE payment_orderId = :oid");
		$r->execute([':err' => strip_tags($err), ':oid' => strip_tags($orderId)]);
		return 1;
	}

	/**
	 * Is order exists in orders table
	 *
	 * @param string $orderId Payu order id
	 * @param string $table Table name
	 * @return void
	 */
	function OrderExists($orderId, $table = 'orders')
	{
		if(empty($orderId)){
			throw new Exception("ERR_DATA", 9011);
		}

		$db = $this->Db;
		$r = $db->Pdo->prepare('SELECT id FROM '.strip_tags($table).' WHERE payment_orderId = :orderId');
		$r->execute([':orderId' => strip_tags($orderId)]);
		return $r->rowCount();
	}

	/**
	 * Add refund contirmation data
	 *
	 * @param object $obj Payu response object
	 * @return void
	 */
	function AddRefund($obj)
	{
		if(empty($obj)){
			throw new Exception("ERR_DATA", 9011);
		}

		// Response obj
		$res = $obj->response;
		// Data
		$orderId = $res->orderId;
		if(!empty($res->extOrderId)){
			$extOrderId = $res->extOrderId;
		}else{
			$extOrderId = $res->refund->refundId;
		}
		// Refund status
		$refund = $res->refund;
		$refund_json = json_encode($res->refund);
		// Amount
		$totalAmount = (int) $refund->amount;
		$currencyCode = $refund->currencyCode;

		$db = $this->Db;
		$r = $db->Pdo->prepare('INSERT INTO payment_order_refund(orderId, extOrderId, totalAmount, currencyCode, refund) VALUES(:orderId, :extOrderId, :totalAmount, :currencyCode, :refund)');
		$r->execute([':orderId' => strip_tags($orderId), ':extOrderId' => strip_tags($extOrderId), ':totalAmount' => strip_tags($totalAmount), ':currencyCode' => strip_tags($currencyCode), ':refund' => strip_tags($refund_json)]);
		return $db->Pdo->lastInsertId();
	}

	function TestOrder()
	{
		try{
			$db = $this->Db;
			$r = $db->Pdo->prepare("INSERT INTO orders(payment_gateway) VALUES('NONE')");
			$r->execute();
			return $db->Pdo->lastInsertId();
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}
}