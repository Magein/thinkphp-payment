<?php

namespace magein\thinkphp_payment\logic;

use think\Validate;

class PaymentOrderValidate extends Validate
{
    protected $rule = [
        'trade_no' => 'require|length:1,255',
        'order_no' => 'require|length:1,255',
        'platform' => 'require|length:1,255',
        'money' => 'require|float',
        'scene' => 'require|length:1,255',
        'extra' => 'length:1,1500',
        'transaction_id' => 'length:1,255',
        'transaction_result' => 'integer|in:-1,0,1',
        'complete_time' => 'integer',
    ];

    protected $message = [
        'trade_no.require' => '请输入外部交易编号',
        'trade_no.length' => '外部交易编号长度不正确,允许的长度1~255',
        'order_no.require' => '请输入订单编号',
        'order_no.length' => '订单编号长度不正确,允许的长度1~255',
        'platform.require' => '请输入交易平台',
        'platform.length' => '交易平台长度不正确,允许的长度1~255',
        'money.require' => '请输入支付金额',
        'money.float' => '支付金额格式错误',
        'scene.require' => '请输入业务场景',
        'scene.length' => '业务场景长度不正确,允许的长度1~255',
        'extra.length' => '额外业务逻辑参数长度不正确,允许的长度1~1500',
        'transaction_id.length' => '交易编号长度不正确,允许的长度1~255',
        'transaction_result.integer' => '交易结果格式错误',
        'transaction_result.in' => '交易结果可选值错误',
        'complete_time.integer' => '完成时间格式错误',
    ];
}