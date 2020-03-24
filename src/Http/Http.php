<?php
namespace Payu\Http;
use Payu\Http\HttpCurl;

class Http
{
    /**
     * @param string $pathUrl
     * @param string $data
     * @param string $token
     * @return mixed
     */
    public static function Post($pathUrl, $data, $token)
    {
        $response = HttpCurl::Request('POST', $pathUrl, $token, $data);

        return $response;
    }

    /**
     * @param string $pathUrl
     * @param string $token
     * @return mixed
     */
    public static function Get($pathUrl, $token)
    {
        $response = HttpCurl::Request('GET', $pathUrl, $token);

        return $response;
    }

    /**
     * @param string $pathUrl
     * @param string $token
     * @return mixed
     */
    public static function Delete($pathUrl, $token)
    {
        $response = HttpCurl::Request('DELETE', $pathUrl, $token);

        return $response;
    }

    /**
     * @param string $pathUrl
     * @param string $data
     * @param string $token
     * @return mixed
     */
    public static function Put($pathUrl, $data, $token)
    {
        $response = HttpCurl::Request('PUT', $pathUrl, $token, $data);

        return $response;
    }
}