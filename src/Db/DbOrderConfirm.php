<?php
namespace Payu\Db;
use Payu\Util\Log;

class DbOrderConfirm
{
	public $Db = null;

	function __construct($db = null)
	{
		if(empty($db)){
			throw new Exception("ERR_DB_CLASS", 9011);
		}

		$this->Db = $db;
	}

	function Valid($oid, $eid, $log = false)
	{
		if($log){
			Log::Msg('!!! CONFIRM - ORDER EXISTS IN DATABASE !!! ' . $oid. ' ' . $eid);
		}

		return $this->IsOrderExist($oid, $eid);
	}

	function IsOrderExist($orderId, $extOrderId)
	{
		$db = $this->Db;
		$r = $db->Pdo->prepare('SELECT * FROM payment_order WHERE orderId = :orderId AND extOrderId = :extOrderId');
		$r->execute([':orderId' => strip_tags($orderId), ':extOrderId' => strip_tags($extOrderId)]);
		return $r->rowCount();
	}
}