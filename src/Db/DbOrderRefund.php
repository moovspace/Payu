<?php
namespace Payu\Db;

use \Exception;
use Payu\Util\Log;

class DbOrderRefund
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
	 * Create notify in db
	 *
	 * @param object $obj Object with {status, response}
	 * @return int
	 */
	static function Create($obj, $log = false)
	{
		if(empty($obj))
		{
			throw new Exception("ERR_RESPONSE", 9990);
		}

		return $this->AddRefund($obj, $log);
	}

	/**
	 * Update payment database
	 * override this method or change
	 * Add import class
	 * require_once 'Db/Db.php';
	 *
	 * @param object $order
	 * @return int 1 or 0
	 */
	protected static function AddRefund($obj, $log = true)
	{
		if($log){
			Log::Msg('### REFUNDED ### '.json_encode($obj));
		}

		// Response obj
		$res = $obj->response;
		// Data
		$orderId = $res->orderId;
		$extOrderId = $res->extOrderId;

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
}

/*
// Powiadomienie z PayU $obj
{
   "orderId": "2DVZMPMFPN140219GUEST000P01",
   "extOrderId": "Id zamówienia w Twoim sklepie",
   "refund": {
      "refundId": "912128",
      "amount": "15516",
      "currencyCode": "PLN",
      "status": "FINALIZED",
      "statusDateTime": "2014-08-20T19:43:31.418+02:00",
      "reason": "refund",
      "reasonDescription": "na życzenie klienta",
      "refundDate": "2014-08-20T19:43:30.150+02:00"
   }
}

// W powiadomieniu można otrzymać dwa statusy zwrotu:
// FINALIZED - zwrot został dokonany pomyślnie,
// CANCELED - zwrot został anulowany.

*/