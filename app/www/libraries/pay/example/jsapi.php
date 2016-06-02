<?php 
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "pay/lib/WxPay.Api.php";
require_once "pay/example/WxPay.JsApiPay.php";
require_once "pay/example/log.php";

//初始化日志
$logHandler= new CLogFileHandler("pay/logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();

$accessToken = $tools->GetAccessToken();
var_dump($accessToken);

$userDetail = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=$accessToken&openid=$openId");
var_dump($userDetail);
exit;

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody("test");
$input->SetAttach("test");
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
$input->SetTotal_fee("1");
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");
$input->SetNotify_url("http://pay.waboon.com/example/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
//printf_info($order);
$jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>微信支付样例-支付</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
	<link rel="stylesheet" href="http://www.waboon.com/css/bootstrap.css"/>
	<link rel="stylesheet" href="http://www.waboon.com/css/main.css"/>
	<style>
		.phone{
			margin-top: 10px;
		}
		.btns {
			display: table;
			margin-top: 5px;
		}
		.btns .item {
			display: table-cell;
			width: 1%;
			vertical-align: middle;
			text-align: center;
		}
		.item>.btn{
			width: 99%;
			background-color: #ddd;
			outline: none;
		}
		.item>.btn:first-child{
			margin-bottom: 5px;
		}
		.item>.btn:focus,
		.item>.btn:hover,
		.item>.btn:active{
			outline: none;
		}
		.money{
			margin-top: 20px;
			margin-bottom: 20px;
		}
		.money .money-amt{
			color: #f00;
			font-size: 16px;
		}
		.btn.myactive{
			box-shadow: inset 0 0 3px #f00;
			color: #f00;
		}
	</style>
	<script type="text/javascript" src="http://www.waboon.com/js/jquery-1.10.2.js"></script>
	<script>
		$(function(){
			$("#btns").find("button").on("click", function () {
				var $this=$(this);
				$this.closest("#btns").find("button").removeClass("myactive");
				$this.addClass("myactive");
			})

		})
	</script>
</head>
<body style="padding-top: 50px;padding-bottom: 0 !important;">
<div class="container">
	<div class="row">
		<nav class="container navbar navbar-default navbar-fixed-top  navbar-custom-header">

			<span class="title">微信支付</span>
		</nav>
		<div class="col-xs-12 phone">
			<input type="text" class="form-control input-lg" placeholder="请输入手机号码"/>
			<hr/>
		</div>

		<div class="col-xs-12">

			<div id="btns" class="btns">
				<div class="item">
					<button type="button" class="btn btn-default">0.01元</button>
					<button type="button" class="btn btn-default myactive">0.01元</button>
				</div>
				<div class="item">
					<button type="button" class="btn btn-default">0.01元</button>
					<button type="button" class="btn btn-default">0.01元</button>
				</div>
				<div class="item">
					<button type="button" class="btn btn-default">0.01元</button>
					<button type="button" class="btn btn-default">0.01元</button>
				</div>
			</div>

			<div class="money">
				售价<span class="money-amt">￥0.01元-￥0.01元</span>
			</div>
			<button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>
		</div>
	</div>
</div>
<script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				alert(res.err_code+res.err_desc+res.err_msg);
			}
		);
	}
	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
			if( document.addEventListener ){
				document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			}else if (document.attachEvent){
				document.attachEvent('WeixinJSBridgeReady', jsApiCall);
				document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			}
		}else{
			jsApiCall();
		}
	}
</script>
</body>
</html>
