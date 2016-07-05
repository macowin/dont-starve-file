<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/8
 * Time: 14:55
 */
class Yunpay
{
    public function pay_submit($parameter, $key)
    {
        $sign_str = '';
        foreach ($parameter as $pars) {
            $sign_str .= $pars;
        }
        $sign = md5($sign_str . 'i2eapi' . $key);
        $mycodess = "<form name='yunsubmit' action='http://pay.yunpay.net.cn/i2eorder/yunpay/' accept-charset='utf-8' method='get'><input type='hidden' name='body' value='" . $parameter['body'] . "'/><input type='hidden' name='out_trade_no' value='" . $parameter['out_trade_no'] . "'/><input type='hidden' name='partner' value='" . $parameter['partner'] . "'/><input type='hidden' name='seller_email' value='" . $parameter['seller_email'] . "'/><input type='hidden' name='subject' value='" . $parameter['subject'] . "'/><input type='hidden' name='total_fee' value='" . $parameter['total_fee'] . "'/><input type='hidden' name='nourl' value='" . $parameter['nourl'] . "'/><input type='hidden' name='reurl' value='" . $parameter['reurl'] . "'/><input type='hidden' name='orurl' value='" . $parameter['orurl'] . "'/><input type='hidden' name='orimg' value='" . $parameter['orimg'] . "'/><input type='hidden' name='sign' value='" . $sign . "'/></form><script>document.forms['yunsubmit'].submit();</script>";
        return $mycodess;
    }

    public function md5_verify($data)
    {
        $prestr = $data['i1'] . $data['i2'] . $data['partner'] . $data['key'];
        $mysgin = md5($prestr);
        if ($mysgin == $data['i3']) {
            return true;
        } else {
            return false;
        }
    }
}