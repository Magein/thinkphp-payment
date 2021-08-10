<?php


namespace magein\thinkphp_payment;


/**
 * 异步通知结果
 * Class Notify
 * @package app\common\extra\pay
 */
abstract class Notify
{
    /**
     * 交易成功
     * @param $data
     * @return bool
     */
    abstract public function success($data): bool;

    /**
     * 交易失败
     * @param $data
     * @return bool
     */
    abstract public function fail($data): bool;

    /**
     * @var Platform
     */
    protected $platform = null;

    /**
     * @var array
     */
    protected $notify_params = [];

    /**
     * 支付平台信息
     * @param Platform $platform
     * @param array $params 第三方回调通知的参数
     * @param array $data 订单参数
     * @return bool
     */
    public function callback(Platform $platform, array $params, array $data)
    {
        $this->platform = $platform;
        $this->notify_params = $params;
        if ($platform->tradeStatus($params) === 'success') {
            return $this->success($data);
        } else {
            return $this->fail($data);
        }
    }
}