### 支付模块

### 数据库

#### migrate版本

```php
    $table = $this->table('payment_order');
    $table->setComment('订单支付订单表 第三方要求订单编号唯一，所以这里记录商家订单与支付订单编号的信息');
    $table->addColumn('trade_no', 'string', ['comment' => '外部交易编号']);
    $table->addColumn('order_no', 'string', ['comment' => '订单编号']);
    $table->addColumn('platform', 'string', ['comment' => '交易平台']);
    $table->addColumn('money', 'decimal', ['comment' => '支付金额 单位为分，跟第三方保持一致', 'precision' => 10, 'scale' => 2]);
    $table->addColumn('scene', 'string', ['comment' => '业务场景 如客户订单、客户充值、活动等,根据不同弄的场景进行不同的业务逻辑回调']);
    $table->addColumn('extra', 'string', ['limit' => 1500, 'comment' => '额外业务逻辑参数 如备注、交易方式等等,一般是开发人员自己定义，自己解析', 'default' => '']);
    $table->addColumn('transaction_id', 'string', ['comment' => '交易编号', 'default' => '']);
    $table->addColumn('transaction_result', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'comment' => '交易结果 -1 失败 fail 0 等待中 pending 1 成功 success', 'default' => 0]);
    $table->addColumn('complete_time', 'integer', ['comment' => '完成时间', 'default' => 0]);
    $table->addColumn('create_time', 'integer', ['comment' => '创建时间']);
    $table->addColumn('update_time', 'integer', ['comment' => '更新时间']);
    $table->addColumn('delete_time', 'integer', ['comment' => '删除时间', 'default' => 0]);
    $table->create();
```

#### 表结构

```php
CREATE TABLE `payment_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trade_no` varchar(255) NOT NULL COMMENT '外部交易编号',
  `order_no` varchar(255) NOT NULL COMMENT '订单编号',
  `platform` varchar(255) NOT NULL COMMENT '交易平台',
  `money` decimal(10,2) NOT NULL COMMENT '支付金额 单位为分，跟第三方保持一致',
  `scene` varchar(255) NOT NULL COMMENT '业务场景 如客户订单、客户充值、活动等,根据不同弄的场景进行不同的业务逻辑回调',
  `extra` varchar(1500) NOT NULL DEFAULT '' COMMENT '额外业务逻辑参数 如备注、交易方式等等,一般是开发人员自己定义，自己解析',
  `transaction_id` varchar(255) NOT NULL DEFAULT '' COMMENT '交易编号',
  `transaction_result` tinyint(4) NOT NULL DEFAULT '0' COMMENT '交易结果 -1 失败 fail 0 等待中 pending 1 成功 success',
  `complete_time` int(11) NOT NULL DEFAULT '0' COMMENT '完成时间',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单支付订单表 第三方要求订单编号唯一，所以这里记录商家订单与支付订单编号的信息';
```