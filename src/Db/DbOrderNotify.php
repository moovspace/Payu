<?php
namespace Payu\Db;

use \Exception;
use Payu\Util\Log;

class DbOrderNotify
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
	 * @param object $obj Response data object
	 * @return int
	 */
	function Create($obj, $log = false)
	{
		if(empty($obj))
		{
			throw new Exception("ERR_RESPONSE", 9990);
		}

		return $this->AddNotify($obj, $log);
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
	protected function AddNotify($res, $log = false)
	{
		if($log){
			Log::Msg('### ADDNOTIFY ### '.json_encode($res));
		}

		$order = $res->response->order;

		$pay = '';
		if(!empty($order->payMethod))
		{
			$pay = json_encode($order->payMethod);
		}

		$products = '';
		if(!empty($order->products))
		{
			$products = json_encode($order->products);
		}

		$buyer = '';
		if(!empty($order->buyer))
		{
			$buyer = json_encode($order->buyer);
		}

		$time = '';
		if(!empty($res->localReceiptDateTime))
		{
			$time = $res->localReceiptDateTime;
		}

		$props = '';
		if(!empty($res->properties))
		{
			$props = json_encode($res->properties);
		}

		if(!empty($order->orderId))
		{
			// Update order in database
			$db = $this->Db;
			$r = $db->Pdo->prepare("INSERT INTO payment_order_notify(orderId,extOrderId,orderCreateDate,status,customerIp,totalAmount,currencyCode,description,merchantPosId,properties,buyer,products,localReceiptDateTime,payMethod) VALUES(:orderId, :extOrderId, :orderCreateDate, :status, :customerIp, :totalAmount, :currencyCode, :description, :merchantPosId, :properties, :buyer, :products, :localReceiptDateTime, :payMethod)");
			$r->execute([':orderId' => strip_tags($order->orderId), ':extOrderId' => strip_tags($order->extOrderId), ':orderCreateDate' => strip_tags($order->orderCreateDate), ':status' => strip_tags($order->status), ':customerIp' => strip_tags($order->customerIp), ':totalAmount' => strip_tags($order->totalAmount), ':currencyCode' => strip_tags($order->currencyCode), ':description' => strip_tags($order->description), ':merchantPosId' => strip_tags($order->merchantPosId), ':properties' => strip_tags($props), ':buyer' => strip_tags($buyer), ':products' => strip_tags($products), ':localReceiptDateTime' => strip_tags($time), ':payMethod' => strip_tags($pay)]);

			return $db->Pdo->lastInsertId();
		}
		else
		{
			throw new Exception("ERR_EMPTY_ORDER", 1);
		}
	}
}

/*
// Powiadomienie zamówienia
{
   "order":{
      "orderId":"LDLW5N7MF4140324GUEST000P01",
      "extOrderId":"Id zamówienia w Twoim sklepie",
      "orderCreateDate":"2012-12-31T12:00:00",
      "notifyUrl":"http://tempuri.org/notify",
      "customerIp":"127.0.0.1",
      "merchantPosId":"{Id punktu płatności (pos_id)}",
      "description":"Twój opis zamówienia",
      "currencyCode":"PLN",
      "totalAmount":"200",
      "buyer":{
         "email":"john.doe@example.org",
         "phone":"111111111",
         "firstName":"John",
         "lastName":"Doe",
         "language":"pl"
      },
      "payMethod": {
         "type": "PBL" //lub "CARD_TOKEN", "INSTALLMENTS"
      },
      "products":[
         {
               "name":"Product 1",
               "unitPrice":"200",
               "quantity":"1"
         }
      ],
      "status":"COMPLETED"
   },
   "localReceiptDateTime": "2016-03-02T12:58:14.828+01:00",
   "properties": [
      {
         "name": "PAYMENT_ID",
         "value": "151471228"
      }
   ]
}

// Powiadomienie anulowania zamówienia
{
   "order":{
      "orderId":"LDLW5N7MF4140324GUEST000P01",
      "extOrderId":"Id zamówienia w Twoim sklepie",
      "orderCreateDate":"2012-12-31T12:00:00",
      "notifyUrl":"http://tempuri.org/notify",
      "customerIp":"127.0.0.1",
      "merchantPosId":"{Id punktu płatności (pos_id)}",
      "description":"Twój opis zamówienia",
      "currencyCode":"PLN",
      "totalAmount":"200",
      "products":[
         {
               "name":"Product 1",
               "unitPrice":"200",
               "quantity":"1"
         }
      ],
      "status":"CANCELED"
   }
}

// Powiadomienie z PayU Refund
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
