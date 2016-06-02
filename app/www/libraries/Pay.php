<?php
require_once "pay/lib/WxPay.Api.php";
require_once "pay/lib/WxPay.Notify.php";

/**
 *
 * JSAPI支付实现类
 * 该类实现了从微信公众平台获取code、通过code获取openid和access_token、
 * 生成jsapi支付js接口所需的参数、生成获取共享收货地址所需的参数
 *
 * 该类是微信支付提供的样例程序，商户可根据自己的需求修改，或者使用lib中的api自行开发
 *
 * @author widy
 *
 */
class Pay
{
    public $curl_timeout = 10;
    /**
     *
     * 网页授权接口微信服务器返回的数据，返回样例如下
     * {
     *  "access_token":"ACCESS_TOKEN",
     *  "expires_in":7200,
     *  "refresh_token":"REFRESH_TOKEN",
     *  "openid":"OPENID",
     *  "scope":"SCOPE",
     *  "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
     * }
     * 其中access_token可用于获取共享收货地址
     * openid是微信支付jsapi支付接口必须的参数
     * @var array
     */
    public $data = null;

    /**
     *
     * 通过跳转获取用户的openid，跳转流程如下：
     * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
     * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
     *
     * @return 用户的openid
     */
    public function GetOpenid()
    {
        //通过code获得openid
        if (!isset($_GET['code'])) {
            //触发微信返回code码
            $baseUrl = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . $_SERVER['QUERY_STRING']);
            $url = $this->__CreateOauthUrlForCode($baseUrl);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $openid = $this->getOpenidFromMp($code);
            return $openid;
        }
    }

    public function GetOpenidTmp()
    {
        //通过code获得openid
        if (!isset($_GET['code'])) {
            //触发微信返回code码
            $baseUrl = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . $_SERVER['QUERY_STRING']);
            $url = $this->__CreateOauthUrlForCodeTmp($baseUrl);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $openid = $this->getOpenidFromMpTmp($code);
            return $openid;
        }
    }

	public function GetUserInfoTmp() {
        $ret = $this->GetOpenidTmp();
        //$u = "https://api.weixin.qq.com/sns/auth?access_token=".$ret['access_token']."&openid=".$ret['openid'];
        //$url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$this->data['access_token']."&openid=".$this->data['openid']."&lang=zh_CN";
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->data['access_token']."&openid=".$this->data['openid']."&lang=zh_CN";
        return json_decode(file_get_contents($url));
	}

	public function GetAccessToken() {
		$ret = file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=". WxPayConfig::APPID . "&secret=". WxPayConfig::APPSECRET);
		$ret = json_decode($ret);
		return $ret->access_token;
	}

	public function GetUserInfo($openid=0) {
		if($openid) {
			$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=". $this->GetAccessToken() . "&openid=". $openid;
		}else {
			$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=". $this->GetAccessToken() . "&openid=". $this->GetOpenid();
		}
		return file_get_contents($url);
	}

    /**
     *
     * 获取jsapi支付的参数
     * @param array $UnifiedOrderResult 统一支付接口返回的数据
     * @throws WxPayException
     *
     * @return json数据，可直接填入js函数作为参数
     */
    public function GetJsApiParameters($UnifiedOrderResult)
    {
        if (!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == ""
        ) {
            throw new WxPayException("参数错误");
        }
        $jsapi = new WxPayJsApiPay();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(WxPayApi::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());
        $parameters = json_encode($jsapi->GetValues());
        return $parameters;
    }

    /**
     *
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     *
     * @return openid
     */
    public function GetOpenidFromMp($code)
    {
        $url = $this->__CreateOauthUrlForOpenid($code);
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if (WxPayConfig::CURL_PROXY_HOST != "0.0.0.0"
            && WxPayConfig::CURL_PROXY_PORT != 0
        ) {
            curl_setopt($ch, CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
        }
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res, true);
        $this->data = $data;
        $openid = $data['openid'];
        return $openid;
    }

    public function GetOpenidFromMpTmp($code)
    {
        $url = $this->__CreateOauthUrlForOpenid($code);
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if (WxPayConfig::CURL_PROXY_HOST != "0.0.0.0"
            && WxPayConfig::CURL_PROXY_PORT != 0
        ) {
            curl_setopt($ch, CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
        }
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res, true);
        $this->data = $data;
        return $data;
        $openid = $data['openid'];
        return $openid;
    }

    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     *
     * 获取地址js参数
     *
     * @return 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
     */
    public function GetEditAddressParameters()
    {
        $getData = $this->data;
        $data = array();
        $data["appid"] = WxPayConfig::APPID;
        $data["url"] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $time = time();
        $data["timestamp"] = "$time";
        $data["noncestr"] = "1234568";
        $data["accesstoken"] = $getData["access_token"];
        ksort($data);
        $params = $this->ToUrlParams($data);
        $addrSign = sha1($params);

        $afterData = array(
            "addrSign" => $addrSign,
            "signType" => "sha1",
            "scope" => "jsapi_address",
            "appId" => WxPayConfig::APPID,
            "timeStamp" => $data["timestamp"],
            "nonceStr" => $data["noncestr"]
        );
        $parameters = json_encode($afterData);
        return $parameters;
    }

    /**
     *
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     *
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = WxPayConfig::APPID;
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE" . "#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }

    private function __CreateOauthUrlForCodeTmp($redirectUrl)
    {
        $urlObj["appid"] = WxPayConfig::APPID;
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_userinfo";
        $urlObj["state"] = "STATE" . "#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }

    /**
     *
     * 构造获取open和access_toke的url地址
     * @param string $code ，微信跳转带回的code
     *
     * @return 请求的url
     */
    private function __CreateOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = WxPayConfig::APPID;
        $urlObj["secret"] = WxPayConfig::APPSECRET;
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
    }
}


class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input); 
		//Log::DEBUG("query:" . json_encode($result));
		log_message("debug", "query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
		&& array_key_exists("result_code", $result)
		&& $result["return_code"] == "SUCCESS"
		&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}

	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		log_message("debug", "call back:" . json_encode($data));
		$notfiyOutput = array();

		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}


		$ci = &get_instance();
		$ci->db->trans_start();
        /*
		//开发测试环境可用余额放大1000倍
        if($_SERVER['CI_ENV'] != "production") {
            $data['total_fee'] = $data['total_fee'] * 1000;
        }
        */
		//增加用户可用余额
        $ci->load->model('User_model');
		$user = $ci->User_model->info(array('openid'=>$data['openid']));
		log_message("debug", "user:" .json_encode($user));
        $user_info = array();
        $user_info['amount_free'] = $user['amount_free'] + $data['total_fee'];
        $user_info['user_id'] = $user['user_id'];
        $ci->User_model->edit($user_info);

        //添加充值记录
		$ci->load->model('Recharge_model');
        $recharge_arr = $ci->Recharge_model->recharge_list(array('transaction_id'=>$data['transaction_id']));
        if(count($recharge_arr)>0) {
            return true;
        }
        $recharge = array();
        $recharge['user_id'] = $user['user_id'];
        $recharge['transaction_id'] = $data['transaction_id'];
        $recharge['amount'] = $data['total_fee'];
        $recharge['created_time'] = time();
        $recharge['type'] = 0;
        $recharge['note'] = json_encode($data);
		$ci->Recharge_model->add($recharge);
		log_message("debug", "recharge:" .json_encode($recharge));

        //添加资金记录
        $record = array();
        $record['user_id'] = $user['user_id'];
        $record['type'] = 0;
        $record['status'] = 2;
        $record['amount'] = $data['total_fee'];
        $record['remain_amount'] = $user_info['amount_free'];
		$ci->load->model('Record_model');
		$ci->Record_model->add($record);
        /*插入提示信息记录
        $msg_info['content'] = "您于".date('Y年m月d日',time())."，成功充值 ".number_format($data['total_fee']/100,2)."元";
        $msg_info['type'] = 3;
        $msg_info['create_time'] = time();
        $ci->load->model('Message_model');
        $msg_info_id = $ci->Message_model->add_info($msg_info);*/
        /*插入关联用户
        $msg_user['msg_id'] = $msg_info_id;
        $msg_user['user_id'] = $user['user_id'];
        $msg_user['isread'] = 1;
        $ci->Message_model->add_user($msg_user);

		log_message("debug", "record:" .json_encode($record));*/
		$ci->db->trans_complete();

		return true;
	}
}


class OrderDoneCallBack extends WxPayNotify
{
    public $order;

    public function __construct($order_info)
    {;
        $this->order = $order_info;
    }

	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input); 
		//Log::DEBUG("query:" . json_encode($result));
		log_message("debug", "query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
		&& array_key_exists("result_code", $result)
		&& $result["return_code"] == "SUCCESS"
		&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}

	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		//Log::DEBUG("call back:" . json_encode($data));
		log_message("debug", "call back:" . json_encode($data));
		$notfiyOutput = array();

		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
        $order_pay_type = 4;
		$ci = &get_instance();
		$ci->db->trans_begin();
        //如果用戶有開啟积分支付，那么消除用户积分
		$ci->load->model('User_model');
        $ci->load->model('Integral_model');
		$user = $ci->User_model->info(array('openid'=>$data['openid']));
        //更新用户的支付状态
        $ci->load->model('Order_model');
        if(is_array($this->order['order_id'])){
            foreach($this->order['order_id'] as $value){
                if($this->order['integral']){
                    $order_info = $ci->Order_model->info($value);
                    $integral_pay = $this->Order_model->order_nums(array('goods_id'=>$order_info['goods_id'],'user_id'=>$order_info['user_id'],'get_integral_pay'=>1));
                    if($integral_pay <= 0){
                        if($user['integral_free'] >= INTEGRAL_RATE){
                            $user['integral_free'] = $user['integral_free'] - INTEGRAL_RATE;
                            $ci->User_model->edit($user);
                            $ci->Integral_model->add(array('user_id'=>$user['user_id'],'point'=>INTEGRAL_RATE,'type'=>2));
                            $order_pay_type = 2;
                        }
                    }
                }
                $ci->Order_model->edit(array('id'=>$value,'status'=>1,'pay_type'=>$order_pay_type));
            }
        }else{
            if($this->order['integral']){
                $order_info = $ci->Order_model->info($this->order['order_id']);
                $integral_pay = $this->Order_model->order_nums(array('goods_id'=>$order_info['goods_id'],'user_id'=>$order_info['user_id'],'get_integral_pay'=>1));
                if($integral_pay <= 0){
                    if($user['integral_free'] >= INTEGRAL_RATE){
                        $user['integral_free'] = $user['integral_free'] - INTEGRAL_RATE;
                        $ci->User_model->edit($user);
                        $order_pay_type = 2;
                    }
                }
            }
            $ci->Order_model->edit(array('id'=>$this->order['order_id'],'status'=>1,'pay_type'=>$order_pay_type));
        }
        log_message("debug", "order_id:" .json_encode($this->order['order_id']));
        log_message("debug", "user:" .json_encode($user));

        //资金记录（认购）
        $record = array();
        $record['user_id'] = $user['user_id'];
        $record['type'] = 5;
        $record['status'] = 2;
        $record['amount'] = -$data['total_fee'];
        $record['remain_amount'] = $user['amount_free'];
        $ci->load->model('Record_model');
        $ci->Record_model->add($record);
        log_message("debug", "record:" .json_encode($record));

        if ($ci->db->trans_status() === FALSE) {
            $ci->db->trans_rollback();
            return false;
        }
        else {
            $ci->db->trans_commit();
            return true;
        }

	}
}
class HongbaoDoneCallBack extends WxPayNotify
{
    public $hongbao_id;
    public $type;

    public function __construct($hongbao_id, $type)
    {
        $this->hongbao_id = $hongbao_id;
        $this->type = $type;
    }

    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);
        //Log::DEBUG("query:" . json_encode($result));
        log_message("debug", "query:" . json_encode($result));
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS"
        ) {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
        //Log::DEBUG("call back:" . json_encode($data));
        log_message("debug", "call back:" . json_encode($data));
        $notfiyOutput = array();
        if (!array_key_exists("transaction_id", $data)) {
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if (!$this->Queryorder($data["transaction_id"])) {
            $msg = "订单查询失败";
            return false;
        }
        $ci = &get_instance();
        $ci->db->trans_start();
        //增加用户可用余额
        $ci->load->model('User_model');
        $user = $ci->User_model->info(array('openid' => $data['openid']));
        log_message("debug", "user:" . json_encode($user));
        //充值成功，更新红包的订单状态
        $update_hongbao = array('id' => $this->hongbao_id, 'wx_pay' => 1);
        if ($this->type == 1) {
            $ci->load->model('Avgenvelope_model');
            $ci->Avgenvelope_model->edit($update_hongbao);
        } else if ($this->type == 2) {
            $ci->load->model('Ranenvelope_model');
            $ci->Ranenvelope_model->edit($update_hongbao);
        } else if ($this->type == 3) {
            $ci->load->model('Guessinfo_model');
            $ci->Guessinfo_model->edit($update_hongbao);
        }

        //添加充值记录
        $ci->load->model('Recharge_model');
        $recharge_arr = $ci->Recharge_model->recharge_list(array('transaction_id' => $data['transaction_id']));
        if (count($recharge_arr) > 0) {
            return true;
        }
        $recharge = array();
        $recharge['user_id'] = $user['user_id'];
        $recharge['transaction_id'] = $data['transaction_id'];
        $recharge['note'] = json_encode($data);
        $ci->Recharge_model->add($recharge);
        log_message("debug", "recharge:" . json_encode($recharge));

        //添加资金记录
        $record = array();
        $record['user_id'] = $user['user_id'];
        $record['type'] = 2;
        $record['status'] = 2;
        $record['amount'] = -$data['total_fee'];
        $record['remain_amount'] = $user['amount_free'];
        $ci->load->model('Record_model');
        $ci->Record_model->add($record);

        /*插入提示信息记录*/
        /*$msg_info['content'] = "您于".date('Y年m月d日',time())."，成功充值 ".number_format($data['total_fee']/100,2)."元";
        $msg_info['type'] = 3;
        $msg_info['create_time'] = time();
        $ci->load->model('Message_model');
        $msg_info_id = $ci->Message_model->add_info($msg_info);*/
        /*插入关联用户*/
        /*$msg_user['msg_id'] = $msg_info_id;
        $msg_user['user_id'] = $user['user_id'];
        $msg_user['isread'] = 1;
        $ci->Message_model->add_user($msg_user);*/

        log_message("debug", "record:" . json_encode($record));
        $ci->db->trans_complete();

        return true;
    }
}
