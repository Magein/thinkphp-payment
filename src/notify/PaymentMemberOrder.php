<?php

namespace magein\thinkphp_payment\notify;

use magein\thinkphp_payment\Notify;

class PaymentMemberOrder extends Notify
{
    /**
     * @param $data
     * @return bool
     */
    public function success($data): bool
    {
        $order_no = $data['order_no'];

        /**
         * 用户支付订单的逻辑
         */

        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    public function fail($data): bool
    {
        return true;
    }
}