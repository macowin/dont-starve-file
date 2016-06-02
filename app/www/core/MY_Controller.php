<?php
/**
 * Created by PhpStorm.
 * User: 周建军
 * Date: 2016/5/19
 * Time: 14:06
 */

class MY_Controller extends CI_Controller
{
    public $user;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('pay');
        $this->load->library('session');
        $this->load->model('Any_model');
        ini_set('date.timezone', 'Asia/Shanghai');
        $this->user = $this->session->userdata('user_info');
        $this->user['open_id'] = 1;
        $this->user['nickname'] = '周先生';
        if(!$this->user['open_id'] || !$this->user['nickname']){
            $ret = $this->pay->GetUserInfo();
            $ret = json_decode($ret);
            $ret_arr = array();
            $ret_arr['openid'] = $ret->openid;
            $ret_arr['subscribe'] = $ret->subscribe;
            /* 过滤掉未关注用户取不到信息报错问题 */
            if($ret->subscribe){
                $ret_arr['nickname'] = base64_encode($ret->nickname);
                $ret_arr['sex'] = $ret->sex;
                $ret_arr['city'] = $ret->city;
                $ret_arr['province'] = $ret->province;
                $ret_arr['headimgurl'] = $ret->headimgurl;
            }
            $user_info = $this->Any_model->info('user',array('open_id'=>$ret->openid));
            /* 验证用户是否存在数据，存在数据则更新用户信息，不存在则添加用户信息，同时写入session */
            if($user_info){
                $ret_arr['user_id'] = $user_info['user_id'];
                $this->Any_model->edit('user',array('id'=>$ret_arr['user_id']),$ret_arr);
                $ret_arr['nickname'] = base64_decode($ret_arr['nickname']);
                $this->session->set_userdata('user_info', $ret_arr);
            }else{
                $user_info = $ret_arr;
                $insert_id = $this->Any_model->add('user',$user_info);
                $user_info['user_id'] = $insert_id;
                $user_info['nickname'] = base64_decode($user_info['nickname']);
                $this->session->set_userdata('user_info', $user_info);
            }
            $this->user = $this->session->userdata('cur_user');
        }

    }
}