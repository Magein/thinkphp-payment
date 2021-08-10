<?php

namespace magein\thinkphp_payment\logic;

use think\db\exception\DbException;
use magein\thinkphp_extra\traits\Instance;
use magein\thinkphp_extra\Logic;

class PaymentOrder extends Logic
{

    use Instance;

    /**
     * 失败
     * @var int
     */
    const TRANSACTION_RESULT_FAIL = -1;

    /**
     * 等待中
     * @var int
     */
    const TRANSACTION_RESULT_PENDING = 0;

    /**
     * 成功
     * @var int
     */
    const TRANSACTION_RESULT_SUCCESS = 1;

    /**
     * @var array
     */
    protected $fields = [
        'id',
        'trade_no',
        'order_no',
        'platform',
        'money',
        'scene',
        'extra',
        'transaction_id',
        'transaction_result',
        'complete_time',
        'create_time',
    ];

    /**
     * @param mixed $transaction
     * @return array|string
     */
    public function transTransaction($transaction = null)
    {
        $data = [
            self::TRANSACTION_RESULT_FAIL => '失败',
            self::TRANSACTION_RESULT_PENDING => '等待中',
            self::TRANSACTION_RESULT_SUCCESS => '成功',
        ];

        if (null !== $transaction) {
            return $data[$transaction] ?? '';
        }

        return $data;
    }


    /**
     * @param $trade_no
     * @return false|\think\Collection|null
     */
    public function getListByTradeNo($trade_no)
    {
        if (empty($trade_no)) {
            return false;
        }

        try {
            $model = $this->model();
            $records = $model->where('trade_no', $trade_no)->select();
        } catch (DbException $exception) {
            $records = null;
        }

        return $records;
    }

    /**
     * @param $order_no
     * @return false|\think\Collection|null
     */
    public function getListByOrderNo($order_no)
    {
        if (empty($order_no)) {
            return false;
        }

        try {
            $model = $this->model();
            $records = $model->where('order_no', $order_no)->select();
        } catch (DbException $exception) {
            $records = null;
        }

        return $records;
    }

}