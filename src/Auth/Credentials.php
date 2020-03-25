<?php
namespace Payu\Auth;

use Exception;
use Payu\Http\Http;
use Payu\Order\Url;
use Payu\Auth\Cache\Cache;

class Credentials
{
    public $Res = '';

    static function IsValidClient($res)
    {
        if(empty($res)){
            throw new Exception("ERR_AUTH_RESPONSE", 1);
        }

        if(strpos(trim($res['response']), '"error":"invalid_client"') > 0){
            throw new Exception("ERR_INVALID_CREDENTIALS", 1);
        }
    }

    function Token($posId, $clientSecret, $sandbox = false)
    {
        $cache = new Cache();
        $token = $cache->GetToken();

        if(empty($token))
        {
            try
            {
                // Auth
                $res = $this->Auth($posId, $clientSecret, $sandbox);

                self::IsValidClient($res);

                // Get token, expire
                $token = $this->ParseToken($res);
                $expire = $this->ParseExpire($res);

                if(empty($token) || empty($expire)){
                    $cache->Clear();
                    throw new Exception("ERR_EMPTY_TOKEN", 9006);
                }else{
                    // Save
                    $cache->SaveToken($token, $expire);
                }
                return $token;
            }
            catch(Exception $e)
            {
                $cache->Clear();
                throw new Exception("ERR_AUTHORIZATION " .$e->getMessage(), 9005);
            }

        }else{
            // echo "Cached token";
            return $token;
        }
    }

    function Auth($posId, $clientSecret, $sandbox = false)
    {
        $res = Http::Post(Url::Authorize($sandbox), $this->CreateCredentials($posId, $clientSecret), null);
        $this->Res = $res;
        return $res;
    }

    final protected function CreateCredentials($posId, $clientSecret)
    {
        if(empty($posId)){
            throw new Exception("ERR_CONFIG_POS_ID", 9001);
        }

        if(empty($clientSecret)){
            throw new Exception("ERR_CONFIG_CLIENT_SECRET", 9002);
        }

        return 'grant_type=client_credentials&client_id='.$posId.'&client_secret='.$clientSecret;
    }

    /**
     * Clear token if error response
     * with Credentials -> Token()
     *
     * Add to
     * try {
     *      ...
     *      Order::Create( ... );
     * }catch(Exception $e){
     *      Order::ClearCacheToken();
     * } block
     *
     * @return void
     */
    static function ClearCacheToken()
    {
        // Clear cache if error token !!! Must be with cache credentials
        $cache = new Cache();
        $cache->Clear();
    }

    protected function ParseResponse($res)
    {
        if(empty($res['response'])){
            throw new Exception("ERR_EMPTY_RESPONSE", 9004);
        }

        return json_decode($res['response'], true);
    }

    protected function ParseToken($res)
    {
        return $this->ParseResponse($res)['access_token'];
    }

    protected function ParseExpire($res)
    {
        return $this->ParseResponse($res)['expires_in'];
    }
}

// grant_type=client_credentials&client_id=145227&client_secret=12f071174cb7eb79d4aac5bc2f07563f