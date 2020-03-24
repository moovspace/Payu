<?php
namespace Payu\Notify;
use Payu\Util\Log;
use Payu\Notify\AddressIP;

class Notify
{
    /**
     * Get PayU notification request from php://input
     *
     * use Payu\Order\Verify\Verify;
     * Verify notification with Verify::Notification();
     *
     * @param boolean $log Save to file in cache directory /Cache/notify.log
     * @return void
     */
    static function Get($log = false)
    {
        // Check ip
        AddressIP::Allowed();

        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            // Get request
            $data = trim(file_get_contents('php://input'));

            if($log == true)
            {
                // Log to file
                Log::Msg('### NOTIFY DATA ### ' .$data);
            }

            return $data;
        }else{
            self::StatusError('ERR_METHOD');
        }
    }

    /**
     * Confirm order retrive
     *
     * @param string $msg Send messsage to PayU order status
     * @return void
     */
    static function StatusConfirmed($msg = 'CONFIRMED')
    {
        echo $msg;
        header("HTTP/1.1 200 OK");
        exit;
    }

    /**
     * Unconfirm order
     *
     * @param string $msg Send messsage to PayU panel order status page
     * @return void
     */
    static function StatusError($msg = 'ERROR')
    {
        echo $msg;
        header("HTTP/1.1 402 Payment required");
        exit;
    }


}