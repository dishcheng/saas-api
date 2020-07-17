<?php

namespace DishCheng\SaasApi\Services;

use DishCheng\SaasApi\Constant\UriPathConstant;
use DishCheng\SaasApi\Exceptions\SaasApiException;
use DishCheng\SaasApi\Traits\SinglePattern;
use Illuminate\Support\Facades\Cache;

/**
 * SaaS系统兑换券接口
 * Class ZwyHotelService
 * @package App\Http\Service\Zwy
 */
class BaseSaasCouponService extends SaasCouponClientRequestService
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
    public function CouponChecking($data=[])
    {
        $data['Code']=$this->request_config['Code'];
        $data['PassWord']=$this->request_config['PassWord'];
        $res=$this->saas_post_request(UriPathConstant::CouponChecking, $data);
        //请求成功
        $tokenCacheKey=$this->getCacheKey();
        Cache::put($tokenCacheKey, $res['data'], 7100);
        return $res;
    }


    /**
     * 9.2 修改兑换券密码
     * @param $data
     * [
     *   'newpassword'=>'888888'
     * ]
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
     * [
     *  "status": true,
     *  'data'=>[
     *    "code": "20200110",
     *    "surplus_amount": 10000,
     *    "overdue": "2022-01-09",
     *    "recharge_amount": 10000,
     *    "use_amount": 0,
     *    "created_at": {
     *    "date": "2020-01-09 16:39:45.000000",
     *    "timezone_type": 3,
     *    "timezone": "UTC"
     *   ]
     * ]
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
     * [
     *  "status": true,
     *  'data'=>[{
     *     "code": "20200110",
     *     "couponType": "recharge",
     *     "amount": 10000,
     *     "created_at": "2020-01-09 16:39:45",
     *     "remark": ""
     *   }]
     * ]
     * @throws SaasApiException
     */
    public function CouponUsedRecordList($data=[])
    {
        return $this->saas_post_request(UriPathConstant::CouponUsedRecordList, $data);
    }

    /**
     * 9.5，使用兑换券
     * @param $data
     * [
     *    'amount'=>'',//使用金额，必填
     *    'remark'=>'',//备注
     * ]
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
