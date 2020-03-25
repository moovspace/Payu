<?php
namespace Payu\Order\Verify;
use \Exception;
use Payu\Auth\Credentials;

class Verify
{
    /**
     * Consume notification message
     * Verify headers signature from Payu notification
     *
     * @access public
     * @param $res string Request array received from with PayU OrderNotifyRequest
     * @return null|data
     * @throws Exception
     */
    public static function Notification($res, $clientSecret)
    {
        if (empty($res)) {
            throw new Exception('ERR_EMPTY_NOTIFICATION_DATA', 9901);
        }

        $headers = self::getRequestHeaders();
        $incomingSignature = self::getSignature($headers);

        self::verifySign($res, $incomingSignature, $clientSecret);

        // Curl codes
        return self::Response(array('response' => $res, 'code' => 200), 'OrderNotifyRequest');
    }

    static function verifySign($data, $signature, $key_md5)
    {
        if(empty($data) || empty($signature) || empty($key_md5)){
            throw new Exception("ERR_EMPTY_VERIFY_SIGNATURE_DATA", 1);
        }

        $sign = self::parseSignature($signature);
        $algorithm = $sign['algorithm'];
        $sign_signature = $sign['signature'];

        if ($algorithm == 'MD5') {
            $hash = md5($data . $key_md5);
        } else if (in_array($algorithm, array('SHA', 'SHA1', 'SHA-1'))) {
            $hash = sha1($data . $key_md5);
        } else {
            $hash = hash('sha256', $data . $key_md5);
        }

        if (strcmp($sign_signature, $hash) == 0) {
            return true;
        }else{
            throw new Exception('ERR_SIGNATURE', 9900);
        }
    }

    /**
     * Verify response from PayU
     *
     * @param array $response
     * @param string $messageName
     * @return null|data
     * @throws Exception
     */
    public static function Response($response, $messageName = 'OrderRequest')
    {
        $data = array();
        $httpStatus = $response['code'];

        $message = self::jsonToArray($response['response'], true);

        $data['status'] = isset($message['status']['statusCode']) ? $message['status']['statusCode'] : null;

        if (json_last_error() == JSON_ERROR_SYNTAX) {
            $data['response'] = $response['response'];
        } elseif (isset($message[$messageName])) {
            unset($message[$messageName]['Status']);
            $data['response'] = $message[$messageName];
        } elseif (isset($message)) {
            $data['response'] = $message;
            unset($message['status']);
        }

		$result = self::build($data);

        if ($httpStatus == 200 || $httpStatus == 201 || $httpStatus == 422 || $httpStatus == 301 || $httpStatus == 302) {
            return json_decode(json_encode($result));
        }

        // Clear cache if error
        Credentials::ClearCacheToken();

        // Error token response status:UNAUTHORIZED
        throw new Exception(ResponseError::Get($httpStatus, $data), 9999);
    }

    protected static function jsonToArray($data, $assoc = true)
    {
        if (empty($data)) {
            return null;
        }

        return json_decode($data, $assoc);
    }

    protected static function build($data)
    {
        if (array_key_exists('status', $data) && $data['status'] == 'WARNING_CONTINUE_REDIRECT') {
            $data['status'] = 'SUCCESS';
            $data['response']['status']['statusCode'] = 'SUCCESS';
        }

        return $data;
    }

    protected static function getRequestHeaders()
    {
        if (!function_exists('apache_request_headers')) {
            $headers = array();
            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
                }
            }
            return $headers;
        } else {
            return apache_request_headers();
        }
    }

    /**
     * Function returns signature data object
     *
     * @param string $data
     *
     * @return null|array
     */
    protected static function parseSignature($data)
    {
        if (empty($data)) {
            return null;
        }

        $signatureData = array();
        $list = explode(';', rtrim($data, ';'));
        if (empty($list)) {
            return null;
        }

        foreach ($list as $value) {
            $explode = explode('=', $value);
            if (count($explode) != 2) {
                return null;
            }
            $signatureData[$explode[0]] = $explode[1];
        }

        return $signatureData;
    }

     /**
     * @param array $headers
     *
     * @return mixed
     */
    protected static function getSignature($headers)
    {
        foreach($headers as $name => $value)
        {
            if(preg_match('/X-OpenPayU-Signature/i', $name) || preg_match('/OpenPayu-Signature/i', $name))
                return $value;
        }

        return null;
    }
}