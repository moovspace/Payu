<?php
namespace Payu\Http;
use \Exception;

class HttpCurl
{
    /**
     * @var
     */
    static $headers;
    const METHODS = ['POST', 'GET', 'PUT', 'DELETE'];

    /**
     * @param $requestType
     * @param $pathUrl
     * @param $data
     * @param $token
     * @return array
     * @throws Exception
     */
    public static function Request($requestType, $pathUrl, $token = null, $data = null)
    {
        if(!in_array($requestType, self::METHODS)){
            throw new Exception('ERR_REQUEST_METHOD');
        }

        if (empty($pathUrl)) {
            throw new Exception('ERR_EMPTY_URL');
        }

        $ch = curl_init($pathUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, self::getHeaders($token));
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'self::readHeader');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($response === false) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);

        return array('code' => $httpStatus, 'response' => trim($response), 'httpStatus' => $httpStatus);
    }

    public static function getHeaders($token)
    {
        if($token){
            return array(
                'Content-Type: application/json',
                'Accept: */*',
                'Authorization: Bearer ' . $token
            );
        }else{
            return array(
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: */*'
            );
        }

    }

    /**
     * @param array $headers
     *
     * @return mixed
     */
    public static function getSignature($headers)
    {
        foreach($headers as $name => $value)
        {
            if(preg_match('/X-OpenPayU-Signature/i', $name) || preg_match('/OpenPayu-Signature/i', $name))
                return $value;
        }

        return null;
    }

    /**
     * @param resource $ch
     * @param string $header
     * @return int
     */
    public static function readHeader($ch, $header)
    {
        if( preg_match('/([^:]+): (.+)/m', $header, $match) ) {
            self::$headers[$match[1]] = trim($match[2]);
        }

        return strlen($header);
    }
}