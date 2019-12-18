<?php

namespace DishCheng\SaasApi\Exceptions;

use Exception;
use Throwable;

class SaasApiException extends Exception
{
    const SAAS_ORDER_STATUS_CANT_UPDATE=10;//订单状态无法修改
    const SAAS_ORDER_SIT_NUMBER_ERROR=40;//占座数目不正确
    const SAAS_PARAMS_INVALID=60;//传入参数值非法
    const SAAS_OFF_SAL_CODE=80;//线路已下架
    const SAAS_USER_TOKEN_ERROR=401;//用户信息不正确！
    const SAAS_NEED_ELSE_PARAMS=404;//缺少参数
    const SAAS_SYSTEM_ERROR=500;//saas系统错误

    public function __construct($message="", $code=0, Throwable $previous=null)
    {
        parent::__construct('【SAAS ERROR】'.$message, $code, $previous);
    }
}
