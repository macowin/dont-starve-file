<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Weixin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Any_model');
        $params = array(
            'token'=>'jungege520', //填写你设定的key
            'encodingaeskey'=>'MUIONGcbFCHyaS26YZpWHgCKBJzdwsklGan94cYiI9C', //填写加密用的EncodingAESKey
            'appid'=>'wx0933e049fcd86918', //填写高级调用功能的app id
            'appsecret'=>'88e1bcd603c33efec118825a752131a6' //填写高级调用功能的密钥
        );
        $this->load->library('wechat', $params);
        ini_set('date.timezone', 'Asia/Shanghai');
    }

    public function index()
    {
        $this->wechat->valid();
        $type = $this->wechat->getRev()->getRevType();
        switch ($type) {
            case Wechat::MSGTYPE_TEXT:
                /*$content = $this->wechat->getRevContent();
                $open_id = $this->wechat->getRevFrom();
                $recontent = $this->Weixin_service->wechat_sign_in($content,$open_id);
                $this->wechat->text($recontent)->reply();*/
                exit;
                break;
            case Wechat::MSGTYPE_EVENT:
                $event = $this->wechat->getRevEvent();
                $open_id = $this->wechat->getRevFrom();
                if ($event['event'] == 'subscribe') {

                } else if ($event['event'] == 'CLICK') {
                    if($event['key'] == 'join_us'){
                        $this->wechat->image('_4lijTsX14FPD9rNL1RhKLs9XePPUrMKskgQEYSHaT6RVNhPCA_Wysq5JnfwDoa9')->reply();
                    }
                } else if ($event['event'] == 'SCAN') {
                    $scene_id = $this->wechat->getRevSceneId();
                    $user_info = $this->wechat->getUserInfo($open_id);
                    if($user_info['subscribe']){
                        $user_id = $this->_add_user($user_info);
                        $this->_set_cycle($scene_id);
                        $this->_set_user_cycle($scene_id,$user_id);
                    }
                }
                exit;
                break;
            case Wechat::MSGTYPE_IMAGE:
                $open_id = $this->wechat->getRevFrom();
                if($open_id == 'oYwZuwZrzd_3nGwyQVlvhLSZu018'){
                    $img = $this->wechat->getRevPic();
                    $this->wechat->text($img['mediaid'])->reply();
                }
                exit;
                break;
            default:
                //$this->wechat->text("我是未知")->reply();
        }
    }

    public function weixin_login()
    {
        redirect("https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx2d66eb3fc8919ecb&redirect_uri=http://wx-dont-starve.zhoujianjun.cn&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect");
    }

    /* 微信登陆回调 */
    public function weixin_sign_in_code()
    {
        if (!$_GET['code']) {
            redirect('/weixin/weixin_login');
        }

        $appid = "wx0933e049fcd86918";
        $secret = "88e1bcd603c33efec118825a752131a6";
        $code = $_GET['code'];
        //拿到用户的access_token
        $ret = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code");
        $token = json_decode($ret, TRUE);
        $user_info = $this->wechat->getOauthUserinfo($token['access_token'],$token['openid']);
        $flag = $this->_add_user($user_info);
        if($flag){
            $user_info['id'] = $flag;
            $this->session->set_userdata('user_info',$user_info);
        }
        $return_url = $this->session->userdata('returnUrl');
        if (!$return_url)
            $return_url = "/server/index";
        else
            $this->session->unset_userdata('returnUrl');
        redirect($return_url);
    }

    public function create_menu()
    {

        $menu = array (
            'button' => array (
                array (
          	        'name' => '扫码',
          	        'type' => 'view',
                    'url' => 'http://buluo.qq.com/p/barindex.html?bid=13696&from=weixin'
                ),
                array(
                    'type' => 'type',
                    'name' => '加入我们',
                    'key' => 'join_us'
                )
            )
        );
        $this->wechat->createMenu($menu);
    }

    public function notify_recharge($recharge_id)
    {
        if(!isset($_GET['KEY']) || $_GET['KEY'] != KEY){
            exit;
        }
        $verify_arr = $_POST;
        $verify_arr['key'] = "vjijgxvWrKf8A2S73HTpAELE49StDKMr";
        $verify_arr['partner'] = "8217146034179662";
        $yunNotify = $this->yunpay->md5_verify($verify_arr);
        if ($yunNotify) {
            $this->Any_model->edit('recharge',array('id'=>$recharge_id),array('status'=>1,'note'=>json_encode($verify_arr)));
        }
    }

    public function getQrcode()
    {
        $scene_id = substr($_GET['scene_id'], 0, 10);
        $qrcode = $this->wechat->getQRCode($scene_id, 0, 60);
        $img_url = $this->wechat->getQRUrl($qrcode['ticket']);
        echo $img_url;
    }

    protected function _set_cycle($scene_id)
    {
        return $this->Any_model->edit('login_qrcode',array('scene_id'=>$scene_id),array('scan_time'=>time()));
    }

    protected function _add_user($user_info)
    {
        $user = array(
            'subscribe'=>$user_info['subscribe'],
            'open_id'=>$user_info['openid'],
            'nickname'=>$user_info['nickname'],
            'sex'=>$user_info['sex'],
            'city'=>$user_info['city'],
            'province'=>$user_info['province'],
            'head_img'=>$user_info['headimgurl'],
            'created_time'=>time()
        );
        $user_flag = $this->Any_model->info('login_user',array('open_id'=>$user['open_id']),'id');
        if($user_flag){
            $this->Any_model->edit('login_user',array('id'=>$user_flag['id']),$user);
            return $user_flag['id'];
        }else{
            return $this->Any_model->add('login_user',$user);
        }
    }

    protected function _set_user_cycle($scene_id,$user_id)
    {
        return $this->Any_model->edit('login_qrcode',array('scene_id'=>$scene_id),array('user_id'=>$user_id));
    }
}