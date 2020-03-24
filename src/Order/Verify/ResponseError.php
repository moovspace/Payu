<?php
namespace Payu\Order\Verify;

class ResponseError
{
    static function Get($httpStatus, $data)
    {
        $codeLiteral = 'UNKNOWN';
        if(!empty($data['response']['status']['codeLiteral'])){
            $codeLiteral = strtoupper($data['response']['status']['codeLiteral']);
        }else{
            $codeLiteral = json_encode($data);
        }

        return 'ERR_HTTP_STATUS_' . $httpStatus . ' ' .$codeLiteral;
    }
}