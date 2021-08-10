<?php

namespace magein\thinkphp_payment\logic;

use magein\thinkphp_extra\view\DataSecurity;

class PaymentOrderSecurity extends DataSecurity
{
    // 自动创建字段信息
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

    // 使用的业务类
    protected $model = PaymentOrderModel::class;

    //  查询字段以及表达式
    protected $export = [];
    
    // 允许插入的数据
    protected $post = [];
    
    //允许更新的字段
    protected $put = [];
}