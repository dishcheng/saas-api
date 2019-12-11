<?php

namespace DishCheng\SaasApi\Services;

use DishCheng\SaasApi\Constant\UriPathConstant;
use DishCheng\SaasApi\Exceptions\SaasApiException;
use DishCheng\SaasApi\Traits\SinglePattern;

/**
 * Class ZwyHotelService
 * @package App\Http\Service\Zwy
 */
class SaasRequestService extends ClientRequestService
{
    use SinglePattern;
    //存放实例对象
    protected static $_instance=[];
    public $request_config=[];


    /**
     * 线路主分类
     * @return array
     * array:2 [▼
     * "status" => true
     * "data" => array:10 [▼
     * 0 => array:4 [▼
     * "PackageTypeid" => 1
     * "TypeName" => "国内线路"
     * "pvn" => ""
     * "imgUrl" => ""
     * ]
     * 1 => array:4 [▶]
     * 2 => array:4 [▶]
     * 3 => array:4 [▶]
     * 4 => array:4 [▶]
     * 5 => array:4 [▶]
     * 6 => array:4 [▶]
     * 7 => array:4 [▶]
     * 8 => array:4 [▶]
     * 9 => array:4 [▶]
     * ]
     * ]
     * @throws SaasApiException
     */
    public function GetPackageType()
    {
        $path=UriPathConstant::GetPackageType;
        $res_string=$this->saas_post_request($path, []);
        return $this->formatResString($res_string);
    }


    /**
     * 线路子分类
     * @param $PackageTypeID
     * @return array
     * 请求成功
     * array:2 [▼
     * "status" => true
     * "data" => array:3 [▼
     * 0 => array:4 [▼
     * "PackageSubTypeid" => 2
     * "typeName" => "北京河北"
     * "pvn" => ""
     * "imgUrl" => ""
     * ]
     * 1 => array:4 [▶]
     * 2 => array:4 [▶]
     * ]
     * ]
     * @throws SaasApiException
     */
    public function GetPackageSubTypeByTypeID($PackageTypeID)
    {
        $path=UriPathConstant::GetPackageSubTypeByTypeID;
        $res_string=$this->saas_post_request($path, ['PackageTypeID'=>$PackageTypeID]);
        return $this->formatResString($res_string);
    }


    /**
     * 线路列表（注意这个接口不分页，，，蛋疼）
     * @param $PageStart
     * @param $PageSize
     * @param $params
     * [
     * 'startDate'=>'2019-11-01',//起始团期（默认当天）
     * 'endDate'=>'2019-11-01',//截止团期（默认6个月）
     * 'PackageTypeid'=>'',//线路主分类id
     * 'PackageSubTypeid'=>'',//线路子分类id
     * 'DepartureCity'=>'',//行程天数
     * 'PlanType'=>'',//计划类型，1常规，2汽车团，3单项
     * ]
     * @return array
     * ^ array:2 [▼
     * "status" => true
     * "data" => array:3 [▼
     * 0 => array:15 [▼
     * "PackageID" => "afc23cf55b978cf5ed33463679fa7582"
     * "PackageTypeid" => "3"
     * "PackageSubTypeid" => "18"
     * "PackageName" => "【测试线路，请不要入单】深圳东部华侨城二天【纯品·乐翻天】YJ－GZ002"
     * "PkgplanType" => "2"
     * "PkglenType" => "2"
     * "Days" => 2
     * "DepartureCity" => "江门,新会,鹤山"
     * "PkgFeature" => """
     * <p><span style="font-size: 10pt;"><img src="http://www.dafangtour.net/upload/ueditorimg/c04ff22c-3291-4a3b-8597-bc8bc11c773a/10/14/09/2019101409264853.jpg" alt= ▶
     * <p><span style="font-size: 10pt;">●大方旅游&middot;放心游公开承诺：</span></p>
     * <p><span style="font-size: 10pt;">1.保证入住行程约定的酒店，绝不降低住宿标准或以次充好！否则给予赔偿！</span></p>
     * <p><span style="font-size: 10pt;">2.保证行程约定的团餐餐标，绝不克扣餐费！否则给予赔偿！</span></p>
     * <p><span style="font-size: 10pt;">3.严格执行行程的补充协议，绝不违约！否则给予赔偿！</span></p>
     * <p><span style="font-size: 10pt;">4.保证导游服务热情周到，绝不强迫旅游者加点或购物！否则给予赔偿！</span></p>
     * <p><span style="font-size: 10pt;">5.接受旅游者的监督，对旅游团首位提出投诉并经查实为有效投诉的旅游者奖励人民币500元！</span></p>
     * <p style="margin-right: 29px; line-height: 16px;"><span style="font-size: 10pt;">【行程特色】</span></p>
     * <p style="margin-right: 29px; line-height: 16px;"><span style="font-size: 10pt;">●大侠谷：云海索道、丛林缆车、亚洲最长峡湾漂流、游海菲德小镇、咆哮山洪、亚洲唯一木质过山车等</span></p>
     * <p style="margin-right: 29px; line-height: 16px;"><span style="font-size: 10pt;">●茶溪谷：森林小火车空中赏景、 &ldquo;茶、禅、花、竹&rdquo;元素、色彩缤纷的升空气球、 29座形态各异的景观桥&nbsp; ！&nbsp; &n ▶
     * """
     * "LeaseQuantity" => 20
     * "Maxorder" => 92
     * "OrderDeadline" => 0
     * "OrdertimeLimit" => 1
     * "isOwnTeam" => "0"
     * "PackageImg" => "upload/pimg/sang/2018-01/2d/c4/packageimg5a4c80518b268.jpg"
     * ]
     * 1 => array:15 [▶]
     * 2 => array:15 [▶]
     * ]
     * ]
     * @throws SaasApiException
     */
    public function GetPackageList($PageStart, $PageSize, $params=[])
    {
        $path=UriPathConstant::GetPackageList;
        $params['PageStart']=$PageStart;
        $params['PageSize']=$PageSize;
        $res_string=$this->saas_post_request($path, $params);
        return $this->formatResString($res_string);
    }


    /**
     * 5，线路团期价格：
     * @param $PackageID
     * @return array
     * "status": true,
     * "data": {
     * "PackageID": 4,
     * "TeamDatePrice": [
     * {
     * "TeamDate": "2019-11-30",
     * "DatePrice": [
     * {
     * "AdultPrice": "641",
     * "AdultRebate": "20"
     * },
     * {
     * "ChildPrice": "475",
     * "ChildRebate": "9"
     * },
     * {
     * "BabyPrice": "0",
     * "BabyRebate": "0"
     * }
     * ],
     * "OtherFee": []
     * },
     *
     *"insuranceData": [
     * {
     * "insuranceID": "50",
     * "insuranceName": "美亚“万国游踪”境外旅行保障计划(全球完美计划 71－80岁)",
     * "insurancePrice": "0",
     * "Coverage": "",
     * "insuranceExplain": "美亚万国游踪之全球完美保险计划是AIG美亚公司推出的险种，由江门市大方旅游代售。其主要是承保在中国大陆的常住居民出发去全球其他国家的出境旅游的意外险。\n主要承保项目包括：1、旅行延误（每延误5小时赔300元，封顶赔1800元），2、随身财产损失封顶赔20000元，3、医疗运送无封顶实报实销，4、意外身故及伤残封顶赔100万，5、医药补偿（含住院及门诊）封顶赔100万。\n注意：\n1，本计划不承保实际前往或途径古巴，伊朗，叙利亚，苏丹，朝鲜，克里米亚地区。\n2，单次旅行最长为182天。\n3，本计划仅承保在中国内地常居的居民\n4，本计划不承保已经身处境外的被保险人\n5，我社有本计划的详细官方说明单张及保险条款，如有需要请向业务员索取。"
     * }
     * ]
     * }
     * }
     */
    public function GetTeamDatePrice($PackageID)
    {
        $path=UriPathConstant::GetTeamDatePrice;
        $res_string=$this->saas_post_request($path, ['PackageID'=>$PackageID]);
        return $this->formatResString($res_string);
    }

    /**
     * 6，线路行程：
     * @param $PackageID
     * @return array
     * {
     * "status": true,
     * "data": [
     * {
     * "Day": 1,
     * "Title": "珠海拱北口岸/横琴口岸—澳门✈芽庄 参考航班：QH583 1725-1835 飞行时间1小时，时差-1小时",
     * "Description": "<p align=\"left\">各位贵宾请于指定时间在拱北口岸集合；我社领队带领过关手续，专车送至澳门机场，搭乘航班前往飞往越南“东方马尔代夫”之称的海滨城市芽庄。抵达芽庄金兰湾国际机场（时差：比北京时间慢1小时）由优秀导游接机。让我们从现在开始收拾心情度过您浪漫精彩的海岛邂逅之旅吧！后入住酒店休息！</p>",
     * "Breakfast": "自理",
     * "Lunch": "自理",
     * "Dinner": "自理",
     * "Accommodation": "芽庄芒青酒店、VDB芽庄酒店 、芽庄佳丽娜酒店等同档次酒店"
     * }
     * ]
     * }
     */
    public function GetPackageItinerary($PackageID)
    {
        $path=UriPathConstant::GetPackageItinerary;
        $res_string=$this->saas_post_request($path, ['PackageID'=>$PackageID]);
        return $this->formatResString($res_string);
    }


    /**
     * 7，线路详情：
     * @param $PackageID
     * @return array
     *
     */
    public function GetPackageInfo($PackageID)
    {
        $path=UriPathConstant::GetPackageInfo;
        $res_string=$this->saas_post_request($path, ['PackageID'=>$PackageID]);
        return $this->formatResString($res_string);
    }


    /**
     * 8，下单接口
     * @param $orderData
     *
     *  'OrderData'=>[
     * 'TaGuid'=>'11111111-1111-1111-1111-111111111111',
     * 'PackageID'=>'24f8903514adfc817afd902d07bd83dc',
     * 'TeamDate'=>'2019-12-19',
     * 'TeamDateID'=>'b8f9ad90a6aa9de4b9125825a246ca7b',
     * 'ContactName'=>'开发测试',
     * 'ContactNumber'=>'13722222222',
     * 'AdultQuantity'=>1,
     * 'AdultPrice'=>100,
     * ]
     *
     *
     * @return array
     * {
     * "status": true,
     * "data": {
     * "Msg": "ok",
     * "OrderID": "a5588d8a7f4fdc193be59356c9476fef",
     * "OrderNumber": "WL1912190003",
     * "Totalsum": 0,
     * "TotalQuantity": 1
     * }
     * }
     */
    public function OrderReserve($orderData)
    {
        $path=UriPathConstant::OrderReserve;
        $res_string=$this->saas_post_request($path, ['OrderData'=>$orderData]);
        return $this->formatResString($res_string);
    }

    /**
     * 8.取消订单
     * @param $OrderId
     * @return array
     */
    public function OrderCancel($OrderId)
    {
        $path=UriPathConstant::OrderCancel;
        $res_string=$this->saas_post_request($path, ['OrderID'=>$OrderId]);
        return $this->formatResString($res_string);
    }

    /**
     * 9，订单列表
     * @param $data
     * @return array
     */
    public function GetOrderListByContact($data)
    {
        $path=UriPathConstant::GetOrderListByContact;
        $res_string=$this->saas_post_request($path, $data);
        return $this->formatResString($res_string);
    }
}
