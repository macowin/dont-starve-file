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

        ini_set('date.timezone','Asia/Shanghai');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model("Any_model");
        /*登录检测session机制*/
        $this->user = $this->session->userdata('user_info');
        $this->user['id'] = 1;
        if(!$this->user) {
            //非正常登录重定向
            $return_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->session->set_userdata('returnUrl',$return_url);
            redirect('/weixin/weixin_login');
        }
    }
}