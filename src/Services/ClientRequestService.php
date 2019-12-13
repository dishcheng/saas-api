<?php

namespace DishCheng\SaasApi\Services;

use App\Exceptions\ApiCommonException;
use DishCheng\SaasApi\Constant\UriPathConstant;
use DishCheng\SaasApi\Exceptions\SaasApiException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 服务端请求saas服务端
 * Class ClientRequestService
 * @package App\Http\Service
 */
class ClientRequestService
{
    const SAAS_ERROR_TITLE='【SAAS ERROR】';
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
        //设定token
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
        if (is_string($res)) {
            return $res;//token
        }
        if (is_array($res)) {
            //登录失败
            if (Arr::has($res,'msg')){
                throw new SaasApiException($res['msg']);
            }else{
                throw new SaasApiException(self::SAAS_ERROR_TITLE.'GET SAAS TOKEN ERROR');
            }
        }
    }

    public function saas_post_request($path, $data=[])
    {
        $err_header=self::SAAS_ERROR_TITLE;
        try {
//            dd($this->request_config);
            if (blank($this->request_config)) {
                $host=config('saas_api.saas_host');
                $default_TaGuid=config('saas_api.saas_TAGuid');
            } else {
//                dd('s');
                $request_config=$this->request_config;
//                dd($request_config);
                $host=$request_config['saas_host'];
                $default_TaGuid=$request_config['saas_TAGuid'];
            }
            $url=$host.$path;
//            dd($url);
            if ($path!=UriPathConstant::Login) {
                //只要不是登录接口就需要设定token
                $this->getToken();
            }
            if (!Arr::has($data, 'TaGuid')) {
                //如果请求数据节点中没有TaGuid直接使用配置的TaGuid(其实那边应该可以根据用户鉴权得到的信息来取到TaGuid，不需要传过去)
                $data=array_merge($data, ['TaGuid'=>$default_TaGuid]);//
            }
            $res=$this->post_request($url, $data);
            return $res;
        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
//            dd($exception);
            Log::error($err_header, [
                '$url'=>isset($url) ? $url : '',
                '$path'=>$path,
                '$data'=>$data,
                'exceptionCode'=>$exception->getCode(),
                'exceptionMessage'=>$exception->getMessage(),
            ]);
            switch ($exception->getCode()) {
                case 400:
                    return [
                        'status'=>false,
                        'msg'=>$err_header.'请求失败，请重试'
                    ];
                    break;
                case 500:
                    return [
                        'status'=>false,
                        'msg'=>self::SAAS_ERROR_TITLE.'系统错误'
                    ];
                default:
                    return [
                        'status'=>false,
                        'msg'=>self::SAAS_ERROR_TITLE.'NETWORK ERROR。'
                    ];
                    break;
            }
        }
    }

    /**
     * 发起post请求
     * @param $url
     * @param array $data
     * @param string $token
     * @param string $type
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
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
                throw new \Exception('请求类型错误');
                break;
        }
        return $res->getBody()->getContents();
    }


    /**
     * 千万不要在saas_post_request()中调用，登录成功接口确实返回的是字符串
     * @param $string
     * @return array
     */
    public function formatResString($string)
    {
        if (is_string($string)) {
            $data=json_decode($string, true);
            if (!blank($data)) {
                if (!Arr::has($data, 'data')) {
                    //返回数据成功的话如果没有data节点
                    return ['status'=>true, 'data'=>$data];
                } else {
                    //返回数据成功的话如果有data节点
                    $res=array_merge(['status'=>true], $data);
                    return $res;
                }
            } else {
                return ['status'=>false, 'msg'=>self::SAAS_ERROR_TITLE.$string];
            }
        } else {
            return $string;
        }

    }
}
