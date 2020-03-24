<?php
namespace Payu\Util;

class Error
{
    // Auth, orders
    const E9001 = 'ERR_CONFIG_POS_ID';
    const E9002 = 'ERR_CONFIG_CLIENT_SECRET';
    const E9003 = 'ERR_EMPTY_TOKEN';
    const E9004 = 'ERR_EMPTY_RESPONSE';
    const E9005 = 'ERR_AUTHORIZATION';
    const E9006 = 'ERR_EMPTY_TOKEN';
    // Verify
    const E9900 = 'ERR_SIGNATURE';
    const E9901 = 'ERR_EMPTY_NOTIFICATION_DATA';
    const E9902 = 'ERR_RESPONSE_VERIFY';
    const E9903 = 'ERR_EMPTY_SIGNATURE';

}