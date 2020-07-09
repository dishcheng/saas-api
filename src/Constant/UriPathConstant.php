<?php

namespace DishCheng\SaasApi\Constant;

class UriPathConstant
{
    //注意api/login拿到的token只能在3-9使用
    const Login='api/login';//获取token（用于接口的授权访问）
    const GetPackageType='api/GetPackageType';//3，线路主分类：
    const GetPackageSubTypeByTypeID='api/GetPackageSubTypeByTypeID';//4，线路子分类：
    const GetPackageList='api/GetPackageList';//5，线路列表：
    const GetTeamDatePrice='api/GetTeamDatePrice';//5,线路团期价格
    const GetPackageItinerary='api/GetPackageItinerary';//6，线路行程：
    const GetPackageInfo='api/GetPackageInfo';//7，线路详情：
    const OrderReserve='api/OrderReserve';//8，下单接口：
    const OrderCancel='api/OrderCancel';//8.1，下单取消接口：
    const GetOrderListByContact='api/GetOrderListByContact';//9，订单列表(好像没了)
    //SaaS系统兑换券接口说明(注意9.1拿到的token在9.2-9.6使用)
    const CouponChecking='api/CouponChecking';//9.1，兑换券登录验证
    const CouponPasswordModify='api/CouponPasswordModify';//9.2，修改兑换券密码
    const CouponInfo='api/CouponInfo';//9.3，获取兑换券信息
    const CouponUsedRecordList='api/Couponlist';//9.4，获取兑换券充值使用列表
    const CouponUse='api/CouponUse';//9.5，使用兑换券
    const CouponClearUse='api/CouponClearUse';//9.6，撤销兑换券使用



//    const GetTouristListByOrderID='api/GetTouristListByOrderID';//10，订单的游客列表：
//    const UpdateOrderTouristInfo='api/UpdateOrderTouristInfo';//11，更新游客信息：
//    const OrderTouristCancel='api/OrderTouristCancel';//订单游客退团：
//    const GetOrderListByDate='api/GetOrderListByDate';//12、根据出团日期或者下单日期，获取订单列表
//    const OrderReserveDF='api/OrderReserveDF';//13，（空港快线）下单接口说明：
//    const AutoOrderReceipt='api/AutoOrderReceipt';//14, 自动财务外联收款方法:
//    const AutoOrderToteam='api/AutoOrderToteam';//15, 自动编团方法:

}
