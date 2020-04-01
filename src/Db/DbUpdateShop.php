<?php
namespace Payu\Db;
use \Exception;
use Payu\Notify\NotifyStatus;
use Payu\Util\Log;

/**
 * Update merchant shop 'orders' table status
 *      Orders table fields (required):
 *
 *      id -> extOrderId
 *      payment_gateway -> Name PAYU
 *      payment_status -> Payu notification status PENDING, WAITING_FOR_CONFIRMATION, COMPLETED, CANCELED
 *      payment_refresh -> Update time
 *      payment_orderId -> Payu order id
 *
 * @param string $extOrderId
 * @param string $status
 * @param boolean $log
 * @return void
 */
class DbUpdateShop
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
     * Update shop orders payu payment_orderId
     *
     * @param string $orderId
     * @param string $extOrderId
     * @param boolean $log
     * @param string $table_name
     * @return void
     */
    function OrdersOrderId($orderId, $extOrderId, $log = false, $table_name = 'orders')
    {
        $gtw = NotifyStatus::GATEWAY_PAYU;

        if(empty($extOrderId) || empty($orderId)){
            throw new Exception("ERR_ORDER_ID", 1);
        }

        if($log){
			Log::Msg(time().' ### ORDERS UPDATE ### ID: '.$extOrderId . ' PID: ' . $orderId);
        }

        $db = $this->Db;
		$r = $db->Pdo->prepare('UPDATE '.strip_tags($table_name).' SET payment_orderId = :oid, payment_gateway = :gtw, payment_refresh = current_timestamp() WHERE id = :eid');
		$r->execute([':oid' => strip_tags($orderId), ':gtw' => strip_tags($gtw), ':eid' => strip_tags($extOrderId)]);
        return $r->rowCount();
    }

    /**
     * Update shop orders payment_status
     *
     * @param string $orderId Payu order id
     * @param string $extOrderId Shop order id
     * @param boolean $log Logs tru or false
     * @param string $table_name Default orders
     * @return void
     */
    function OrdersStatusUpdate($orderId, $extOrderId = '', $log = false, $table_name = 'orders')
    {
        $gtw = NotifyStatus::GATEWAY_PAYU;

        $all = $this->GetPaymentStatus($extOrderId, $orderId);

        foreach ($all as $status)
        {
			$status = trim($status['status']);

            // Error status ignore
            if(!empty($status) && in_array($status, NotifyStatus::STATUS_ALL))
            {
                if($log){
                    Log::Msg(time().' ### ORDERS UPDATE STATUS ### ID: '.$extOrderId . ' PID: ' . $orderId . ' STATUS: ' .$status);
                }

                $db = $this->Db;
				$r = $db->Pdo->prepare('UPDATE '.strip_tags($table_name).' SET payment_status = :status, payment_gateway = :gtw, payment_refresh = current_timestamp() WHERE payment_orderId = :oid AND payment_status != :completed AND payment_status != :canceled');
				$r->execute([':status' => strip_tags($status), ':gtw' => strip_tags($gtw), ':oid' => strip_tags($orderId), ':completed' => trim(NotifyStatus::STATUS_COMPLETED), ':canceled' => trim(NotifyStatus::STATUS_CANCELED)]);
                // return $r->rowCount();
            }
            else
            {
                if($log){
                    Log::Msg(time().' ### ERROR ORDERS UPDATE STATUS ### ID: '.$extOrderId . ' PID: ' . $orderId . ' STATUS: ' .json_encode($status));
                }
            }
        }
        return 1;
    }

    /**
     * Get payu notification status for extOrderId
     *
     * @param string $extOrderId Merchant shop order id
     * @return void
     */
    protected function GetPaymentStatus($extOrderId, $orderId)
    {
        if(empty($orderId) && empty($extOrderId)){
            throw new Exception("ERR_ORDER_ID", 1);
        }

        $db = $this->Db;
		$r = $db->Pdo->prepare('SELECT status FROM payment_order_notify WHERE extOrderId = :eid OR orderId = :oid');
		$r->execute([':oid' => strip_tags($orderId), ':eid' => strip_tags($extOrderId)]);
        return $r->fetchAll();
    }

    function OrdersStatusRefresh($orderId, $status = '', $log = false, $table_name = 'orders')
    {
        // Error status ignore
        if(!empty($status) && in_array($status, NotifyStatus::STATUS_ALL))
        {
            if($log){
                Log::Msg(time().' ### ORDERS REFRESH STATUS ### PID: ' . $orderId . ' STATUS: ' .$status);
            }

            $db = $this->Db;
            $r = $db->Pdo->prepare('UPDATE '.strip_tags($table_name).' SET payment_status = :status, payment_refresh = current_timestamp() WHERE payment_orderId = :oid AND payment_status != :completed AND payment_status != :canceled');
            $r->execute([':status' => strip_tags($status), ':oid' => strip_tags($orderId), ':completed' => NotifyStatus::STATUS_COMPLETED, ':canceled' => NotifyStatus::STATUS_CANCELED]);
            return $r->rowCount();
        }
        else
        {
            if($log){
                Log::Msg(time().' ### ERROR ORDERS REFRESH STATUS ### PID: ' . $orderId . ' STATUS: ' .json_encode($status));
            }
        }

        return true;
    }
}