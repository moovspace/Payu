<?php
namespace Payu\Db;
use \Exception;
use Payu\Util\Log;

class DbOrder
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
	 * Save created with Create::Order() order in database
	 *
	 * @param object $obj Open payu response: {status, response}
	 * @param array $order Order array
	 * @return int 0 or id in db table
	 */
	function Create($obj, $order, $log = false)
	{
		if(empty($obj))
		{
			throw new Exception("ERR_RESPONSE", 9990);
		}

		if($log){
			Log::Msg('### NEW ORDER ### '.json_encode($obj));
		}

		// Response from Order::Create()
		$res = $obj->response;

		$orderId = '';
		$extOrderId  = '';
		$totalAmount = 0;
		$currencyCode = 'PLN';
		$url = '';

		if(!empty($order['currencyCode'])){
			$currencyCode = $order['currencyCode'];
		}else{
			throw new Exception("ERR_CURRENCY", 1);
		}

		if($order['totalAmount'] >= 0){
			$totalAmount = (int) $order['totalAmount'];
		}else{
			throw new Exception("ERR_AMOUNT", 1);
		}

		if(!empty($res->orderId)){
			$orderId = $res->orderId;
		}else{
			throw new Exception("ERR_ORDER_ID", 1);
		}

		if(!empty($res->extOrderId)){
			$extOrderId = $res->extOrderId;
		}

		return $this->AddOrder($orderId, $extOrderId, $totalAmount, $currencyCode);
	}

	/**
	 * Check is order exists in database
	 *
	 * @param sring $orderId Payu order id: Q3GQD5KLRM200318GUEST000P01
	 * @return int
	 */
	function ValidOrder($orderId)
	{
		return self::OrderExists($orderId);
	}

	/**
	 * Update order status in shop orders
	 *
	 * @param string $orderId Payu order id
	 * @param string $extOrderId Merchant order id
	 * @return bool true or false
	 */
	function Confirm($orderId, $extOrderId = '')
	{
		// confirm order id
		return true;
	}

	protected function OrderExists($orderId)
	{
		$db = $this->Db;
		$r = $db->Pdo->prepare('SELECT * FROM payment_order WHERE orderId = :orderId');
		$r->execute([':orderId' => strip_tags($orderId)]);
		return $r->rowCount();
	}

	protected function AddOrder($orderId, $extOrderId, $totalAmount, $currencyCode)
	{
		$db = $this->Db;
		$r = $db->Pdo->prepare('INSERT INTO payment_order(orderId, extOrderId, totalAmount, currencyCode) VALUES(:orderId, :extOrderId, :totalAmount, :currencyCode)');
		$r->execute([':orderId' => strip_tags($orderId), ':extOrderId' => strip_tags($extOrderId), ':totalAmount' => strip_tags($totalAmount), ':currencyCode' => strip_tags($currencyCode)]);
		return $db->Pdo->lastInsertId();
	}
}