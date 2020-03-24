<?php
namespace Payu\Order;

use \Exception;
use Payu\Http\Http;
use Payu\Order\Url;
use Payu\Order\Verify\Verify;

/**
 * Payu main class for requests
 */
class Order
{
    /**
     * Create new order and payment link
     *
     * @param array $data
     * @param string $token
     * @param boolean $sandbox
     * @return void
     */
    static function Create(array $data, $token, $sandbox = false)
    {
        if(empty($token)){
            throw new Exception("ERR_EMPTY_TOKEN", 9003);
        }

        $res = Http::Post(Url::Create($sandbox), json_encode($data), $token);

        $res = Verify::Response($res);

        return $res;
    }

    /**
     * Refund order, full or part
     *
     * @param string $orderId
     * @param integer $amount
     * @param string $token
     * @param boolean $sandbox
     * @return void
     */
    static function Refund($orderId, int $amount = 0, $token = null, $sandbox = false)
    {
        if(empty($token)){
            throw new Exception("ERR_EMPTY_TOKEN", 9003);
        }

        if($amount > 0 && $amount < 20){
            throw new Exception("ERR_REFUND_AMOUNT", 9007);
        }

        $data = '{"refund": {"description": "Refund"}}';
        if($amount > 0){
            $data = '{"refund": {"description": "Refund", "amount": '.$amount.'}}';
        }

        $res = Http::Post(Url::Refund($sandbox, $orderId), $data, $token);

        $res = Verify::Response($res);

        return $res;
    }

    /**
     * Confirm order with status WAITING_FOR_CONFIRMATION
     *
     * @param string $orderId
     * @param string $token
     * @param boolean $sandbox Is sandbox true or false
     * @return void
     */
    static function Retrive($orderId, $token = null, $sandbox = false)
    {
        if(empty($token)){
            throw new Exception("ERR_EMPTY_TOKEN", 9003);
        }

        if(empty($orderId)){
            throw new Exception("ERR_ORDER_ID", 9000);
        }

        $data = '{"orderId": "'.$orderId.'","orderStatus": "COMPLETED"}';

        $res = Http::Put(Url::Retrive($sandbox, $orderId), $data, $token);

        $res = Verify::Response($res);

        return $res;
    }

    /**
     * Cancel order with status WAITING_FOR_CONFIRMATION
     *
     * @param string $orderId
     * @param string $token
     * @param boolean $sandbox Is sandbox true or false
     * @return void
     */
    static function Cancel($orderId, $token = null, $sandbox = false)
    {
        if(empty($token)){
            throw new Exception("ERR_EMPTY_TOKEN", 9003);
        }

        if(empty($orderId)){
            throw new Exception("ERR_ORDER_ID", 9000);
        }

        $res = Http::Delete(Url::Cancel($sandbox, $orderId), $token);

        $res = Verify::Response($res);

        return $res;
    }

    /**
     * Get payu order details
     *
     * @param string $orderId
     * @param string $token
     * @param boolean $sandbox Is sandbox true or false
     * @return void
     */
    static function Status($orderId, $token = null, $sandbox = false)
    {
        if(empty($token)){
            throw new Exception("ERR_EMPTY_TOKEN", 9003);
        }

        if(empty($orderId)){
            throw new Exception("ERR_ORDER_ID", 9000);
        }

        $res = Http::Get(Url::Status($sandbox, $orderId), $token);

        $res = Verify::Response($res);

        return $res;
    }

    /**
     * Get order transactions details
     *
     * @param string $orderId
     * @param string $token
     * @param boolean $sandbox Is sandbox true or false
     * @return void
     */
    static function Transactions($orderId, $token = null, $sandbox = false)
    {
        if(empty($token)){
            throw new Exception("ERR_EMPTY_TOKEN", 9003);
        }

        if(empty($orderId)){
            throw new Exception("ERR_ORDER_ID", 9000);
        }

        $res = Http::Get(Url::Transactions($sandbox, $orderId), $token);

        $res = Verify::Response($res);

        return $res;
    }

    /**
     * Get payu paymethods
     *
     * @param string $token
     * @param boolean $sandbox Is sandbox true or false
     * @return void
     */
    static function PayMethods($token = null, $sandbox = false)
    {
        if(empty($token)){
            throw new Exception("ERR_EMPTY_TOKEN", 9003);
        }

        $res = Http::Get(Url::PayMethods($sandbox), $token);

        $res = Verify::Response($res);

        return $res;
    }

    /**
     * Delete token
     *
     * @param string $token
     * @param boolean $sandbox Is sandbox true or false
     * @return void
     */
    static function DeleteToken($token = null, $sandbox = false)
    {
        if(empty($token)){
            throw new Exception("ERR_EMPTY_TOKEN", 9003);
        }

        $res = Http::Delete(Url::DeleteToken($sandbox, $token), $token);

        $res = Verify::Response($res);

        return $res;
    }
}