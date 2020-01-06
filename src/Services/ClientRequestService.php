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
class ClientRequestService
{
    const TimeOutSecond=10;

    public $request_config=[];
    public $token='';

    /**
     * 获取token
     * @return array
     * @throws \DishCheng\SaasApi\Exceptions\SaasApiException
     */
    public function getToken()
    {
        $data=[];
        if (blank($this->request_config)) {
            $request_data=[
                'TaGuid'=>config('saas_api.TAGuid'),
                'UserID'=>config('saas_api.saas_userId'),
                'PassWord'=>config('saas_api.saas_password'),
            ];
        } else {
            $request_config=$this->request_config;
            $request_data=[
                'TaGuid'=>$request_config['saas_TAGuid'],
                'UserID'=>$request_config['saas_userID'],
                'PassWord'=>$request_config['saas_password'],
            ];
        }
        $tokenCacheKey=config('saas_api.cache_token_header').$request_data['TaGuid'].':'.$request_data['UserID'];
        $token=Cache::get($tokenCacheKey);
        if (blank($token)) {
            //没token，需要登录获取，有效期120分钟，120*60在减去100秒
            $res_token=$this->login($request_data);//直接返回的token字符串不能json_decode()
            Cache::put($tokenCacheKey, $res_token, 7100);
            $data['token']=$res_token;
        } else {
            //有token，直接返回
            $data['token']=$token;
        }
        //设定token----非常重要的一步
        $this->token=$data['token'];
        return $data['token'];
    }


    /**
     * 发起登录，直接返回token字符串
     * @param $request_data
     * @return array|string
     * @throws SaasApiException
     */
    public function login($request_data)
    {
        $path=UriPathConstant::Login;
        $res=$this->saas_post_request($path, $request_data);
        if ($res['status']) {
            //请求成功
            return $res['data'];
        } else {
            //失败
            throw new SaasApiException('鉴权失败');
        }
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
            if ($path!=UriPathConstant::Login) {
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
     */
    public function post_request($url, $data=[], $type='json')
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

                    $res=['status'=>true, 'data'=>$res_data];
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
