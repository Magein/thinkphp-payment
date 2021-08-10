<?php

namespace magein\thinkphp_payment\logic;

use magein\thinkphp_extra\Model;

/**
 * Class PaymentOrderModel
 * @package app\components\payment\payment_order
 * @property integer $id
 * @property string $trade_no
 * @property string $order_no
 * @property string $platform
 * @property float $money
 * @property string $scene
 * @property string $extra
 * @property string $transaction_id
 * @property integer $transaction_result
 * @property integer $complete_time
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $delete_time
 */
class PaymentOrderModel extends Model
{
    protected $table = 'payment_order';        
        
    protected $schema = [
        'id' => 'integer',
        'trade_no' => 'string',
        'order_no' => 'string',
        'platform' => 'string',
        'money' => 'float',
        'scene' => 'string',
        'extra' => 'string',
        'transaction_id' => 'string',
        'transaction_result' => 'integer',
        'complete_time' => 'integer',
        'create_time' => 'integer',
        'update_time' => 'integer',
        'delete_time' => 'integer',
    ];
    
    
    /**
     * @param $value
     * @param $data
     * @return array|string
     */                
    protected function getTransactionResultTextAttr($value, $data)
    {
        return PaymentOrder::instance()->transTransactionResult($data['transaction_result'] ?? '');
    }
}    