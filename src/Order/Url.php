<?php
namespace Payu\Order;

class Url
{
    const HTTPS = 'https://secure.';
    const PAYU = 'payu.com';
    const SANDBOX = 'snd.';
    // Payu paths
    const AUTH = '/pl/standard/user/oauth/authorize';
    const PAY_METHODS = '/api/v2_1/paymethods';
    const ORDERS = '/api/v2_1/orders';
    const TOKENS = '/api/v2_1/tokens';

    /**
     * Create authorize url
     *
     * @param boolean $sandbox Is sandbox
     * @return string url
     */
    static function Authorize($sandbox = false)
    {
        if($sandbox == true){
            return self::HTTPS.self::SANDBOX.self::PAYU.self::AUTH;
        }
        return self::HTTPS.self::PAYU.self::AUTH;
    }

    /**
     * Create new order url
     *
     * @param boolean $sandbox Is sandbox
     * @return string url
     */
    static function Create($sandbox = false)
    {
        if($sandbox == true){
            return self::HTTPS.self::SANDBOX.self::PAYU.self::ORDERS;
        }
        return self::HTTPS.self::PAYU.self::CREATE_ORDER;
    }

    /**
     * Create refund order url
     * /api/v2_1/orders/{orderId}/refunds
     *
     * @param boolean $sandbox Is sandbox
     * @return string url
     */
    static function Refund($sandbox = false, $orderId = '')
    {
        if(empty($orderId)){
            throw new Exception("ERR_ORDER_ID", 9000);
        }
        if($sandbox == true){
            return self::HTTPS.self::SANDBOX.self::PAYU.self::ORDERS.'/'.$orderId.'/refunds';
        }
        return self::HTTPS.self::PAYU.self::ORDERS.'/'.$orderId.'/refunds';
    }

    /**
     * Retrive order url
     * Odbiór opłaconego zamówienia z PayU
     * /api/v2_1/orders/{orderId}/status
     *
     * @param boolean $sandbox Is sandbox
     * @return string url
     */
    static function Retrive($sandbox = flase, $orderId = '')
    {
        if(empty($orderId)){
            throw new Exception("ERR_ORDER_ID", 9000);
        }
        if($sandbox == true){
            return self::HTTPS.self::SANDBOX.self::PAYU.self::ORDERS.'/'.$orderId.'/status';
        }
        return self::HTTPS.self::PAYU.self::ORDERS.'/'.$orderId.'/status';
    }

    /**
     * Cancel order url
     * /api/v2_1/orders/{orderId}
     *
     * @param boolean $sandbox Is sandbox
     * @return string url
     */
    static function Cancel($sandbox = false, $orderId = '')
    {
        if(empty($orderId)){
            throw new Exception("ERR_ORDER_ID", 9000);
        }

        if($sandbox == true){
            return self::HTTPS.self::SANDBOX.self::PAYU.self::ORDERS.'/'.$orderId;
        }
        return self::HTTPS.self::PAYU.self::ORDERS.'/'.$orderId;
    }

    /**
     * Order details url
     * /api/v2_1/orders/{orderId}
     *
     * @param boolean $sandbox Is sandbox
     * @return string url
     */
    static function Status($sandbox = false, $orderId = '')
    {
        if(empty($orderId)){
            throw new Exception("ERR_ORDER_ID", 9000);
        }

        if($sandbox == true){
            return self::HTTPS.self::SANDBOX.self::PAYU.self::ORDERS.'/'.$orderId;
        }
        return self::HTTPS.self::PAYU.self::ORDERS.'/'.$orderId;
    }

    /**
     * Transaction details url
     *
     * /api/v2_1/orders/{orderId}/transactions
     *
     * @param boolean $sandbox Is sandbox
     * @return string url
     */
    static function Transactions($sandbox = false, $orderId = '')
    {
        if(empty($orderId)){
            throw new Exception("ERR_ORDER_ID", 9000);
        }
        if($sandbox == true){
            return self::HTTPS.self::SANDBOX.self::PAYU.self::ORDERS.'/'.$orderId.'/transactions';
        }
        return self::HTTPS.self::PAYU.self::ORDERS.'/'.$orderId.'/transactions';
    }

    /**
     * Paymethods url
     * /api/v2_1/paymethods
     *
     * @param boolean $sandbox Is sandbox
     * @return string url
     */
    static function PayMethods($sandbox = false)
    {
        if($sandbox == true){
            return self::HTTPS.self::SANDBOX.self::PAYU.self::PAY_METHODS;
        }
        return self::HTTPS.self::PAYU.self::PAY_METHODS;
    }

    /**
     * Tokens url
     * /api/v2_1/tokens
     *
     * @param boolean $sandbox Is sandbox
     * @return string url
     */
    static function DeleteToken($sandbox = true, $token = '')
    {
        if(empty($token)){
            throw new Exception("ERR_TOKEN_EMPTY", 9000);
        }

        if($sandbox == true){
            return self::HTTPS.self::SANDBOX.self::PAYU.self::TOKENS.'/'.$token;
        }
        return self::HTTPS.self::PAYU.self::TOKENS.'/'.$token;
    }
}