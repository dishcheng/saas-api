<?php

namespace DishCheng\SaasApi\Services;

use DishCheng\SaasApi\Constant\UriPathConstant;
use DishCheng\SaasApi\Exceptions\SaasApiException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * 服务端请求saas服务端
 * Class ClientRequestService
 * @package App\Http\Service
 */
class SaasCouponClientRequestService
{
    const TimeOutSecond=10;

    public $request_config=[];
    public $coupon_id='';
    public $token='';


    public function getCacheKey()
    {
        $request_config=$this->request_config;
        return config('saas_api.coupon_cache_token_header').
            $request_config['saas_host'].':'.
            $request_config['saas_TAGuid'].':'.
            $request_config['Code'].':'.
            $request_config['PassWord'];
    }

    /**
     * 获取token
     * @return array
     * @throws \DishCheng\SaasApi\Exceptions\SaasApiException
     */
    public function getToken()
    {
        $data=[];
        $request_config=$this->request_config;
        $request_data=[
            'TaGuid'=>$request_config['saas_TAGuid'],
            'Code'=>$request_config['Code'],
            'PassWord'=>$request_config['PassWord'],
        ];
        $tokenCacheKey=$this->getCacheKey();
        $token=Cache::get($tokenCacheKey);
        if (blank($token)) {
            //没token，需要登录获取，有效期120分钟，120*60在减去100秒
            $service=BaseSaasCouponService::getInstance();
            $res=$service->CouponChecking();
            $data['token']=$res['data'];
        } else {
            //有token，直接返回
            $data['token']=$token;
        }
        //设定token----非常重要的一步
        $this->token=$data['token'];
        return $data['token'];
    }

    /**
     * saas post请求
     * @param $path
     * @param array $data
     * @return array
     * @throws SaasApiException
     */
    public function saas_post_request($path, $data=[])
    {
        try {
            if (blank($this->request_config)) {
                $host=config('saas_api.saas_host');
                $default_TaGuid=config('saas_api.saas_TAGuid');
            } else {
                $request_config=$this->request_config;
                $host=$request_config['saas_host'];
                $default_TaGuid=$request_config['saas_TAGuid'];
            }
            $url=$host.$path;
            if ($path!=UriPathConstant::CouponChecking) {
                //只要不是登录接口就需要设定token
                $this->getToken();
            }
            if (!Arr::has($data, 'TaGuid')) {
                //如果请求数据节点中没有TaGuid直接使用配置的TaGuid(其实那边应该可以根据用户鉴权得到的信息来取到TaGuid，不需要传过去)
                $data=array_merge($data, ['TaGuid'=>$default_TaGuid]);//
            }
            $res=$this->post_request($url, $data);
            return $this->formatResString($res);
        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            Log::error('【SAAS GuzzleException ERROR】', [
                '$url'=>isset($url) ? $url : '',
                '$data'=>$data,
                'exceptionCode'=>$exception->getCode(),
                'exceptionMessage'=>$exception->getMessage(),
            ]);
            switch ($exception->getCode()) {
                case 400:
                    throw new SaasApiException('请求失败，请重试');
                    break;
                case 500:
                    throw new SaasApiException('系统错误');
                    break;
                default:
                    throw new SaasApiException('NETWORK ERROR。'.$exception->getCode());
                    break;
            }
        }
    }


    /**
     * 发起post请求
     * @param $url
     * @param array $data
     * @param string $type
     * @return string
     * @throws SaasApiException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post_request($url, $data=[], $type='form_data')
    {
        $client=new \GuzzleHttp\Client();
        switch ($type) {
            case 'json':
                $res=$client->request('post', $url,
                    [
                        'verify'=>false,
                        'headers'=>[
                            'content-type'=>'application/json; charset=UTF-8',
                            'Authorization'=>'Bearer '.$this->token
                        ],
                        'json'=>$data,
                        'connect_timeout'=>self::TimeOutSecond,
                    ]);
                break;
            //这个得默认用form_data请求
            case 'form_data':
                $res=$client->request('post', $url, [
                    'verify'=>false,
                    'headers'=>[
                        'Authorization'=>'Bearer '.$this->token
                    ],
                    'form_params'=>$data,
                    'connect_timeout'=>self::TimeOutSecond,
                ]);
                break;
            default:
                throw new SaasApiException('request请求类型错误');
                break;
        }
        return $res->getBody()->getContents();
    }


    /**
     * 格式化返回
     * @param $string
     * @return array
     * @throws SaasApiException
     */
    public function formatResString($string)
    {
        if (is_string($string)) {
            $data=json_decode($string, true);
            if (!blank($data)) {
                if (!isset($data['status_code'])||!isset($data['message'])) {
                    throw new SaasApiException('未按规定返回指定参数；status_code、message');
                }
                if ($data['status_code']==0) {
                    //请求成功
                    $res_data=[];
                    if (isset($data['token'])) {
                        //登录返回的数据节点是token
                        $res_data=$data['token'];
                    }
                    if (isset($data['data'])) {
                        //其他请求成功返回的数据节点是data
                        $res_data=$data['data'];
                    }
                    $res=['data'=>$res_data];
                    if (isset($data['Page'])) {
                        //如果有分页数据
                        $res['Page']=$data['Page'];
                    }
                    return $res;
                } else {
                    throw new SaasApiException($data['message'], $data['status_code']);
                }
            } else {
                throw new SaasApiException($string);
            }
        } else {
            return $string;
        }

    }
}
