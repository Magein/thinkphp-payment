<?php

namespace magein\thinkphp_payment;


abstract class Platform
{

    /**
     * null 表示自动判断  true表示debug模式  false表示正式模式
     * @var null|bool
     */
    public $debug = null;

    /**
     * 基础的配置参数
     * @var array
     */
    protected $config = [];

    /**
     * 响应参数
     * @var array
     */
    protected $response = [];

    /**
     * 创建订单请求地址
     * @return string
     */
    abstract public function orderHost(): string;

    /**
     * 交易订单查询地址
     * @return string
     */
    abstract public function queryHost(): string;

    /**
     * 退款接口
     * @return string
     */
    abstract public function refundHost(): string;

    /**
     * 基础的参数配置
     * @return mixed
     */
    abstract public function getConfig();

    /**
     * 查询参数
     * @return mixed
     */
    abstract public function getQueryConfig();

    /**
     * 退款参数
     * @return mixed
     */
    abstract public function getRefundConfig();

    /**
     * 验签
     * @param array $params
     * @return mixed
     */
    abstract public function verify(array $params);

    /**
     * 设置用户id
     * 如微信支付的 openid 支付宝用户user_id
     * @return void
     */
    abstract public function setUserId(string $id);

    /**
     * 收到通过，需要给第三方服务器发送结果通知
     * @return mixed
     */
    abstract public function success();

    /**
     * 平台名称
     * @return string
     */
    abstract public function getName(): string;

    /**
     * @return bool
     */
    protected function isDebug()
    {
        if ($this->debug === null) {
            $this->debug = true;
        }
        return $this->debug;
    }

    /**
     * 设置响应数据
     * @param array $response
     */
    public function setResponse(array $response)
    {
        $this->response = $response;
    }

    /**
     * 获取响应数据
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response ?: [];
    }

    /**
     * 传递的参数信息转化成 键值对的形式 ?xx=xx&xx=xx
     * @param array $params
     * @return string
     */
    public function toUrlQuery(array $params): string
    {
        ksort($params);
        $query = '';
        foreach ($params as $k => $v) {
            if ($v != '' && !is_array($v)) {
                $query .= $k . '=' . $v . '&';
            }
        }

        return trim($query, "&");
    }

    /**
     * 设置交易的金额
     * @return mixed
     */
    public function setMoney($money): void
    {
        $this->config['money'] = floatval($money) * 100;
    }

    /**
     * 设置交易的body
     * @param string $body
     * @return mixed
     */
    public function setBody(string $body): void
    {
        $this->config['body'] = $body;
    }

    /**
     * 设置其他参数信息
     * @param array $other
     */
    public function setOther(array $other = []): void
    {
        if ($other) {
            $this->config = array_merge($this->config, $other);
        }
    }

    /**
     * 外部交易编号
     *
     * 这里要特别注意是提交给支付平台的订单编号，不能重复
     *
     * @param string $trade_out_no
     */
    public function setTradeOutNo(string $trade_out_no = '')
    {
        $this->config['trade_out_no'] = $trade_out_no;
    }

    /**
     * 设置平台交易退款单号
     * @param string $refund_no
     */
    public function setPlatformRefundNo(string $refund_no = '')
    {
        $this->config['refund_no'] = $refund_no;
    }

    /**
     * 设置通过商家订单编号退款
     * @param string $refund_no
     */
    public function setMerchantRefundNo(string $refund_no = '')
    {
        $this->config['refund_no'] = $refund_no;
    }

    /**
     * 获取由第三方交易生成的交易编号字段信息
     * @return string
     */
    public function getTransactionIdField(): string
    {
        return 'transaction_id';
    }

    /**
     * 获取由第三方交易生成的交易结果字段信息
     * @return string
     */
    public function getTransactionResultField(): string
    {
        return 'trade_status';
    }

    /**
     * 获取异步通知中的订单编号，此编号为交易单号，而非实际的订单编号，通过交易编号去查询订单编号
     * @return string
     */
    public function getNotifyTradeNo(): string
    {
        return 'transaction_id';
    }

    /**
     * 交易结果
     * @return mixed
     */
    public function tradeStatus($data)
    {
        return true;
    }

    /**
     * @param $name
     * @param $value
     * @return array
     */
    protected function concatQueryResult($name, $value): array
    {
        return [
            'name' => $name,
            'value' => $value
        ];
    }

    /**
     * 查询交易结果
     * @return array
     */
    public function queryResult(): array
    {
        return [
            $this->concatQueryResult('交易金额', 10),
        ];
    }

    /**
     * 签名
     * @param $config
     * @return string
     */
    public function autograph($config): string
    {
        ksort($config);

        return $this->toUrlQuery($config);
    }

    /**
     * 异步通知url
     * @return string
     */
    protected function notifyUrl(): string
    {
        return '';
    }
}