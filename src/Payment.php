<?php


namespace magein\thinkphp_payment;

use GuzzleHttp\Client;
use magein\thinkphp_extra\payment\platform\TongLian;
use magein\thinkphp_payment\logic\PaymentOrder;

class Payment
{
    /**
     * @var Platform|null
     */
    public $platform = null;

    /**
     * Payment constructor.
     * @param Platform|null $platform
     */
    public function __construct(Platform $platform = null)
    {
        if ($platform === null || !$platform instanceof Platform) {
            $this->platform = new TongLian();
        }
    }

    /**
     * 通过名称设置平台
     * @param $name
     */
    public function setPlatformByName($name): void
    {
        if (in_array($name, ['TongLian', 'tonglian', 'tong_lian'])) {
            $this->platform = new TongLian();
        }
    }

    /**
     * @param $host
     * @param $config
     * @return Platform|TongLian
     */
    private function request($host, $config)
    {
        $sign = $this->platform->autograph($config);
        if ('' === $sign) {
            return $this->platform;
        }
        $config['sign'] = urlencode($sign);
        // 生成字符串
        $params = $this->platform->toUrlQuery($config);
        $client = new Client();
        $res = $client->post($host,
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
                ],
                // 这里一开始使用的是form_params,但是汉字会被urlencode掉，导致sign签名失败
                'body' => $params,
            ]
        );

        // 响应成功
        if ($res->getStatusCode() === 200) {
            $response = $res->getBody()->getContents();
            if ($response) {
                $this->platform->setResponse(json_decode($response, true));
            }
        }

        return $this->platform;
    }

    /**
     * 统一下单接口
     * @param string $trade_no 交易的订单编号
     * @param float|string $money 交易金额
     * @param string $body 交易说明
     * @param array $other 其他参数
     * @return Platform|TongLian
     */
    public function order(string $trade_no, $money, string $body = '', array $other = [])
    {
        $this->platform->debug = false;
        $this->platform->setMoney($money);
        $this->platform->setBody($body);
        $this->platform->setOther($other);
        $this->platform->setTradeOutNo($trade_no);

        return $this->request($this->platform->orderHost(), $this->platform->getConfig());
    }

    /**
     * @param $params
     * @param string $namespace
     */
    public function notify($params, string $namespace = 'magein\thinkphp_payment\notify'): void
    {
        // 验签正确后，则进行先关的业务逻辑
        $result = $this->platform->verify($params);

        // 验签错误，来源非法
        if (false === $result) {
            $this->platform->success();
        }

        if (is_string($params)) {
            parse_str($params, $params);
        }

        $trade_no = $params[$this->platform->getNotifyTradeNo()] ?? '';
        $record = PaymentOrder::instance()->getByTradeNo($trade_no);
        // 订单编号不存在或者已经完成
        if (empty($record) || $record['complete_time'] > 0) {
            $this->platform->success();
        }

        // 先标记为完成，防止重复通知，至于是否支付成功，暂时不做处理，如果出现了支付完成，但是尚未支付的情况，核实后查询即可
        PaymentOrder::instance()->save(
            [
                'id' => $record['id'],
                'complete_time' => time(),
                'transaction_id' => $params[$this->platform->getTransactionIdField()] ?? '',
                'transaction_result' => $params[$this->platform->getTransactionResultField()] ?? '',
            ]
        );

        // 业务逻辑场景
        $namespace .= '\\' . $record['scene'];
        if (class_exists($namespace) && method_exists($namespace, 'callback')) {
            call_user_func_array([new $namespace(), 'callback'], [$this->platform, $params, $record]);
            $this->platform->success();
        }
    }

    /**
     * @return Platform|TongLian
     */
    private function query()
    {
        $this->platform->debug = false;
        return $this->request($this->platform->queryHost(), $this->platform->getQueryConfig());
    }

    /**
     * @param string $trade_no
     * @return Platform
     */
    public function queryByPlatformTradeNo(string $trade_no)
    {
        $this->platform->setOther(
            [
                $this->platform->getTransactionIdField() => $trade_no
            ]
        );

        return $this->query();
    }

    /**
     * @param string $trade_no
     * @return Platform
     */
    public function queryByMerchantTradeNo(string $trade_no)
    {
        $this->platform->setTradeOutNo($trade_no);
        return $this->query();
    }

    /**
     * @param string $refund_trade_no
     * @param $money
     * @param $other
     * @return Platform
     */
    private function refund(string $refund_trade_no, $money, $other)
    {
        $this->platform->debug = false;
        $this->platform->setTradeOutNo($refund_trade_no);
        $this->platform->setMoney($money);
        $this->platform->setOther($other);
        return $this->request($this->platform->refundHost(), $this->platform->getRefundConfig());
    }

    /**
     * @param string $refund_trade_no 退款单的单号
     * @param string $trade_no 需要退款的订单编号（第三方平台生成的，意思就是已经发生过的交易）
     * @param $money
     * @param array $other
     * @return Platform
     */
    public function refundByPlatformTradeNo(string $refund_trade_no, string $trade_no, $money, array $other = [])
    {
        $this->platform->setTradeOutNo($refund_trade_no);
        $this->platform->setPlatformRefundNo($trade_no);

        return $this->refund($refund_trade_no, $money, $other);
    }

    /**
     * @param string $refund_trade_no
     * @param string $trade_no
     * @param $money
     * @param array $other
     * @return Platform
     */
    public function refundByMerchantTradeNo(string $refund_trade_no, string $trade_no, $money, array $other = [])
    {
        $this->platform->setTradeOutNo($refund_trade_no);
        $this->platform->setMerchantRefundNo($trade_no);

        return $this->refund($refund_trade_no, $money, $other);
    }
}