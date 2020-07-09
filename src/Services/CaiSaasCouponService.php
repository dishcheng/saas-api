<?php

namespace DishCheng\SaasApi\Services;

use DishCheng\SaasApi\Constant\UriPathConstant;
use DishCheng\SaasApi\Exceptions\SaasApiException;
use DishCheng\SaasApi\Traits\SinglePattern;

/**
 * SaaS系统兑换券接口
 * Class ZwyHotelService
 * @package App\Http\Service\Zwy
 */
class CaiSaasCouponService extends SaasCouponClientRequestService
{
    use SinglePattern;
    //存放实例对象
    protected static $_instance=[];
    public $request_config=[];
    public $coupon_id='';

    /**
     * 9.1 兑换券登录验证
     * @param $data
     * @return array
     * @throws SaasApiException
     */
    public function CouponChecking($data)
    {
        return $this->saas_post_request(UriPathConstant::CouponChecking, $data);
    }


    /**
     * 9.2 修改兑换券密码
     * @param $data
     * @return array
     * @throws SaasApiException
     */
    public function CouponPasswordModify($data)
    {
        return $this->saas_post_request(UriPathConstant::CouponPasswordModify, $data);
    }


    /**
     * 9.3，获取兑换券信息
     * @param $data
     * @return array
     * @throws SaasApiException
     */
    public function CouponInfo($data)
    {
        return $this->saas_post_request(UriPathConstant::CouponInfo, $data);
    }


    /**
     * 9.4，获取兑换券充值使用列表
     * @param $data
     * @return array
     * @throws SaasApiException
     */
    public function CouponUsedRecordList($data)
    {
        return $this->saas_post_request(UriPathConstant::CouponUsedRecordList, $data);
    }

    /**
     * 9.5，使用兑换券
     * @param $data
     * @return array
     * @throws SaasApiException
     */
    public function CouponUse($data)
    {
        return $this->saas_post_request(UriPathConstant::CouponUse, $data);
    }


    /**
     * 9.6，撤销兑换券使用
     * @param $data
     * @return array
     * @throws SaasApiException
     */
    public function CouponClearUse($data)
    {
        return $this->saas_post_request(UriPathConstant::CouponClearUse, $data);
    }
}
