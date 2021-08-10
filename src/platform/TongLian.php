<?php


namespace magein\thinkphp_extra\payment\platform;

use magein\thinkphp_extra\payment\Platform;

use magein\tools\common\UnixTime;
use think\Exception;

class TongLian extends Platform
{
    /**
     * 配置参数
     * @var string[]
     */
    protected $config = [
        // 商户号
        'cusid' => '56136105399HNJR',
        'appid' => '00212068',
        // 交易方式
        'paytype' => 'W06',
        // 有效时间
        'validtime' => 1800,
        // 版本号
        'version' => 11,
        // 微信小程序ID
        'sub_appid' => 'wx4f40833108e719fa'
    ];

    /**
     * rsa私钥
     * @var string
     */
    private $private_key = 'MIIEowIBAAKCAQEAve/ED3Hl4W1F9mL2exSMwWI2GSr0AipmjdTAep9Q/XIPgLEjW9r/grM0fg8hvkAPLmDzSF/tz0iYnepWbkhVaojsVBFu6CWaMYlPSwwMtj/3WnsSLRkTen/TBZZ95Gjr1xjnEoJoTjfYdc+d2ZyqBEr/nHpnZPHAQR3DBwIgvQ919aFqd9+vMZVs1/Gi57XCvjDBxkM4WaSNoZHkxwRLRpBXy9brvjTIVd4ArGPuZYPNLC76YmbewQE3L3aH0ZrV0sSolS8+AR5kDvN3rjaneoFNL7b3eVJe09j1/Li+nsGjxpe04gX1Mx6SR2zm6lGDK/EUBhEDujqOeMjKB3ci0wIDAQABAoIBABsQ3rSAu4xrIOwiYBNb807fauwdNdZVKnYNmjwfFdB3/4OMOoitZXm/hbxs9ZcB0+f9As4OdXnzU2Q1b4mZnypmRp8YOOC18woaEgWUuv8BkIMBRK7OgvsS2gRS1K/Gz8zADLWThb+Xr63iHxFShNvFwDURMEivNoFQ92i5QecO3Psh1Qvc+712ERtTtFiEE9JcVmfQPPCBQnpfz9SA6Ymx0wsf+fTZFXxip+AKXSoCwWXIOzKREWmh2meG3Im9opKPlYHttRafuCV0Dy4ha+4vKICVV04chOgKgDAZoY6B9iokZBdRBK0whwLfZUcdkKqtwXzJfCWGlKFBmKj9otkCgYEA60MLmRlVuVsOMVrBUcBiReFq8Gkn+ee9OrvPIR676B6qJSTmZXzXES9CSwPBYDbAu4X0vpa05QhEBJJd/ZCaw78lwVCTC59fDZwazrMsuOMz7mTzgBfs8ALeZPDtLGjutKS/VhXv20J8kBQ5bMRPeM56Eo2qP/UJQUDFqilF1q8CgYEAzq3nJObbjJBi7GUSSFIozFFL5V7Jz1LzOJeS88i319Tw10Ma0Z2UJXiQ5vQl9muCT47h97zGngJxRAKDQL77ErOlsx3Q/eD/tQreA/SipGXSPpcRpfDgBL6E9pA6KQbhaqXDedxds5QAmgYR5LZIAG9vfYWWWokc9S0Ylx8Dfx0CgYBe/uiM/nTl7oWRmkLbXuKPsxD6WIvC1jdLoxBlIELh3Jf5fafmrP0uvDV5kt+fmRxD3DhSSQpsPOUyL2PyglRr3CBDqeRmWgkIttMAScsyMNQEXBES1RZgZ9gjTEGIoCpbGPcFrL/4eiVQDEqPx8kLT9xs53UqdCgziBMIvbeskwKBgFGnWv61RmAOW0bR15tC+mNeawG/pTOHrhfZqnEuB5P5yP0CwgG2o+T58SWNp8HnT6Bm7teVeVLL3DvmUjyuNzt4RiqxyPPEhMHd/UDK0cbpg1q/DHXm6Fp3esh+yruSwFQGEWh/q3VbL7GqXD9j1KZ3YDDTKkeyjAJBQsnRsuvxAoGBAIQMubX+3OglJEwzWaaglylWFRIKGZLKSxH31n8tHoC3G7VJtSCc7BfPvuSIXTJYaEsHbup7mYvhoAb7wEFe7CJwZgksG2CXRBSPDh5NJMUe86jVHDWwW90rX0QLSgpMaiuoKleGH6GEDpwuGOlr/UY0c01dipR/umDU0HX2AkgH';

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'allinpay';
    }

    /**
     * 请求的主机地址
     * @return string
     */
    public function orderHost(): string
    {
        if ($this->isDebug()) {
            return 'https://test.allinpaygd.com/apiweb/unitorder/pay';
        } else {
            return 'https://vsp.allinpay.com/apiweb/unitorder/pay';
        }
    }

    /**
     * @return string
     */
    public function queryHost(): string
    {
        if ($this->isDebug()) {
            return 'https://test.allinpaygd.com/apiweb/unitorder/query';
        } else {
            return 'https://vsp.allinpay.com/apiweb/unitorder/query';
        }
    }

    /**
     * @return string
     */
    public function refundHost(): string
    {
        if ($this->isDebug()) {
            return 'https://test.allinpaygd.com/apiweb/unitorder/refund';
        } else {
            return 'https://vsp.allinpay.com/apiweb/unitorder/refund';
        }
    }

    /**
     * 基础的参数配置
     * @return array
     */
    public function getConfig()
    {
        $this->config['randomstr'] = uniqid();
        $this->config['signtype'] = 'RSA';
        if ($this->config['paytype'] == 'W06') {
            $this->config['notify_url'] = $this->notifyUrl();
        }

        if ($this->isDebug()) {
            $this->config['appid'] = '00000051';
            $this->config['cusid'] = '990581007426001';
            $this->config['paytype'] = 'W01';
            $this->config['sub_appid'] = '';
            $this->private_key = 'MIICdQIBADANBgkqhkiG9w0BAQEFAASCAl8wggJbAgEAAoGBAJgHMGYsspghvP+yCbjLG43CkZuQ3YJyDcmEKxvmgblITfmiTPx2b9Y2iwDT9gnLGExTDm1BL2A8VzMobjaHfiCmTbDctu680MLmpDDkVXmJOqdlXh0tcLjhN4+iDA2KkRqiHxsDpiaKT6MMBuecXQbJtPlVc1XjVhoUlzUgPCrvAgMBAAECgYAV9saYTGbfsdLOF5kYo0dve1JxaO7dFMCcgkV+z2ujKtNmeHtU54DlhZXJiytQY5Dhc10cjb6xfFDrftuFcfKCaLiy6h5ETR8jyv5He6KH/+X6qkcGTkJBYG1XvyyFO3PxoszQAs0mrLCqq0UItlCDn0G72MR9/NuvdYabGHSzEQJBAMXB1/DUvBTHHH4LiKDiaREruBb3QtP72JQS1ATVXA2v6xJzGPMWMBGQDvRfPvuCPVmbHENX+lRxMLp39OvIn6kCQQDEzYpPcuHW/7h3TYHYc+T0O6z1VKQT2Mxv92Lj35g1XqV4Oi9xrTj2DtMeV1lMx6n/3icobkCQtuvTI+AcqfTXAkB6bCz9NwUUK8sUsJktV9xJN/JnrTxetOr3h8xfDaJGCuCQdFY+rj6lsLPBTnFUC+Vk4mQVwJIE0mmjFf22NWW5AkAmsVaRGkAmui41Xoq52MdZ8WWm8lY0BLrlBJlvveU6EPqtcZskWW9KiU2euIO5IcRdpvrB6zNMgHpLD9GfMRcPAkBUWOV/dH13v8V2Y/Fzuag/y5k3/oXi/WQnIxdYbltad2xjmofJ7DbB7MJqiZZD8jlr8PCZPwRNzc5ntDStc959';
        }

        return $this->config;
    }

    public function getQueryConfig()
    {
        $this->config = $this->getConfig();
        unset(
            $this->config['sub_appid'],
            $this->config['paytype'],
            $this->config['validtime'],
            $this->config['notify_url'],
        );
        return $this->config;
    }

    public function getRefundConfig()
    {
        $this->config = $this->getConfig();
        unset(
            $this->config['sub_appid'],
            $this->config['paytype'],
            $this->config['validtime'],
            $this->config['notify_url'],
        );
        return $this->config;
    }

    /**
     * 签名
     * @param $config
     * @return string
     */
    public function autograph($config): string
    {
        ksort($config);

        $string = $this->toUrlQuery($config);

        $private_key = chunk_split($this->private_key, 64, "\n");

        $key = "-----BEGIN RSA PRIVATE KEY-----\n" . wordwrap($private_key) . "-----END RSA PRIVATE KEY-----";

        try {
            openssl_sign($string, $signature, $key);
        } catch (Exception $exception) {

        }
        if ($signature) {
            return base64_encode($signature);
        }

        return '';
    }

    /**
     * 验签
     * @param string $params
     * @return bool
     */
    public function verify($params): bool
    {
        if (is_string($params)) {
            parse_str($params, $params);
        }
        if (empty($params) || !is_array($params)) {
            return false;
        }
        $sign = $params['sign'];
        unset($params['sign']);
        $bufSignSrc = $this->toUrlQuery($params);
        $public_key = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCm9OV6zH5DYH/ZnAVYHscEELdCNfNTHGuBv1nYYEY9FrOzE0/4kLl9f7Y9dkWHlc2ocDwbrFSm0Vqz0q2rJPxXUYBCQl5yW3jzuKSXif7q1yOwkFVtJXvuhf5WRy+1X5FOFoMvS7538No0RpnLzmNi3ktmiqmhpcY/1pmt20FHQQIDAQAB';
        $public_key = chunk_split($public_key, 64, "\n");
        $key = "-----BEGIN PUBLIC KEY-----\n$public_key-----END PUBLIC KEY-----\n";
        try {
            $result = openssl_verify($bufSignSrc, base64_decode($sign), $key);
        } catch (Exception $exception) {
            $result = 0;
        }
        return $result === 1 ? true : false;
    }

    /**
     * @return void
     */
    public function success(): void
    {
        echo 'success';
        exit();
    }

    public function setMoney($money): void
    {
        $this->config['trxamt'] = floatval($money) * 100;
    }

    /**
     * 外部交易编号
     * @param string $trade_out_no
     * @return mixed
     */
    public function setTradeOutNo(string $trade_out_no = ''): void
    {
        $this->config['reqsn'] = $trade_out_no;
    }

    /**
     *设置通过商家订单编号退款
     * @param string $refund_no
     */
    public function setMerchantRefundNo($refund_no = '')
    {
        $this->config['oldreqsn'] = $refund_no;
    }

    /**
     * 支付平台生成的第三方交易订单号
     * @param string $refund_no
     */
    public function setPlatformRefundNo($refund_no = '')
    {
        $this->config['oldtrxid'] = $refund_no;
    }

    /**
     * 设置用户标识
     * @param string $id
     */
    public function setUserId(string $id): void
    {
        $this->config['acct'] = $id;
    }

    /**
     * @return string
     */
    public function getTransactionIdField(): string
    {
        return 'trxid';
    }

    /**
     * @return string
     */
    public function getTransactionResultField(): string
    {
        return 'trxstatus';
    }

    /**
     * @return string
     */
    public function getNotifyTradeNo(): string
    {
        return 'cusorderid';
    }

    /**
     * @param $data
     * @return bool|mixed|string
     */
    public function tradeStatus($data = [])
    {
        if (empty($data)) {
            $data = $this->response;
        }

        $status = $data['trxstatus'] ?? '';

        if ($status === '0000') {
            return 'success';
        }

        return $status;
    }

    /**
     * @return array
     */
    public function queryResult(): array
    {
        return [
            $this->concatQueryResult('交易类型', $this->response['trxcode'] ?? ''),
            $this->concatQueryResult('交易金额(分)', $this->response['trxamt'] ?? 0),
            $this->concatQueryResult('手续费', $this->response['fee'] ?? ''),
            $this->concatQueryResult('交易完成时间', ($this->response['fintime'] ?? '') ? date('Y-m-d H:i', UnixTime::instance()->unix($this->response['fintime'])) : ''),
            $this->concatQueryResult('交易状态', ($this->response['trxstatus'] ?? '0') === '0000' ? 'success' : 'fail'),
            $this->concatQueryResult('失败原因', $this->response['errmsg'] ?? ''),
        ];
    }
}