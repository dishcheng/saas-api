<?php

return [
    'saas_TAGuid'=>env('SAAS_TAGuid', ''),//
    'saas_host'=>env('SAAS_DOMAIN', ''),//这里的值只要域名，一定一定以/结尾！！！
    'saas_userId'=>env('SAAS_USERID', ''),
    'saas_password'=>env('SAAS_PASSWORD', ''),
    //必填
    'cache_token_header'=>env('SAAS_CACHE_TOKEN_HEADER', 'SAAS_TOKEN:'),//后面会接saasTAGuid+账户id，值为token
    'coupon_cache_token_header'=>env('SAAS_CACHE_TOKEN_HEADER', 'SAAS_COUPON_TOKEN:'),//后面会接saasTAGuid+优惠券id，值为token
];