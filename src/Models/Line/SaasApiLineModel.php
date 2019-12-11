<?php

namespace DishCheng\SaasApi\Models\Line;

use DishCheng\SaasApi\Exceptions\SaasApiException;
use DishCheng\SaasApi\Services\SaasRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * 不关联数据表,laravel-admin数据来源外部api-
 * https://laravel-admin.org/docs/zh/model-grid-data
 * Class ZwyParkCity
 * @package App\Models\ZwyPark
 */
class SaasApiLineModel extends Model
{
    public $request_config=[];
    public $saas_service;

    public function __construct(array $attributes=[])
    {
        $this->saas_service=SaasRequestService::getInstance();
        parent::__construct($attributes);
    }

    /**
     * 调用详情接口
     * @param $PackageID
     * @return SaasApiLineModel
     * @throws SaasApiException
     */
    public function findOrFail($PackageID)
    {
        if (!blank($this->request_config)) {
            $this->saas_service->request_config=$this->request_config;
        };
        $res=$this->saas_service->GetPackageInfo($PackageID);
        if (!$res['status']) {
            throw new SaasApiException($res['msg']);
        }
        $data=$res['data'];
        return self::newFromBuilder($data);
    }


    /**
     * 调用线路行程接口
     * @param $PackageID
     * @return SaasApiLineModel
     * @throws SaasApiException
     */
    public function getPackageItinerary($PackageID)
    {
        if (!blank($this->request_config)) {
            $this->saas_service->request_config=$this->request_config;
        };
        $res=$this->saas_service->GetPackageItinerary($PackageID);
        if (!$res['status']) {
            throw new SaasApiException($res['msg']);
        }
        $data=$res['data'];
        return self::hydrate($data);
    }


    /**
     * 调用线路价格接口
     * @param $PackageID
     * @return SaasApiLineModel
     * @throws SaasApiException
     */
    public function getPrice($PackageID)
    {
        if (!blank($this->request_config)) {
            $this->saas_service->request_config=$this->request_config;
        };
        $res=$this->saas_service->GetTeamDatePrice($PackageID);
        if (!$res['status']) {
            throw new SaasApiException($res['msg']);
        }
        $data=$res['data'];
        return self::newFromBuilder($data);
    }


    /**
     * @param null $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return LengthAwarePaginator
     * @throws SaasApiException
     */
    public function paginate($perPage=null, $columns=['*'], $pageName='page', $page=null)
    {
        $currentPage=$page ?: Paginator::resolveCurrentPage($pageName);
        $perPage=$perPage ?: $this->perPage;
        //获取数据数组
        if (!blank($this->request_config)) {
            $this->saas_service->request_config=$this->request_config;
        };
        $request_arr=Request::except(['page']);
        Log::info('qingqiu',$request_arr);
        $res=$this->saas_service->GetPackageList($currentPage, $perPage, $request_arr);
        if (!$res['status']) {
            throw new SaasApiException($res['msg']);
        }
        if (isset($res['data']['Page']['total'])){
            $data=[];
            $totalCount=(int)$res['data']['Page']['total'];
        }else{
            $data=$res['data'];
            $totalCount=(int)$res['Page']['total'];
        }
        $dataList=static::hydrate($data);
        return new LengthAwarePaginator($dataList, $totalCount, $perPage, $currentPage);
    }

    public static function with($relations)
    {
        return new static;
    }

    public function where()
    {
        return $this->paginate();
    }
}
