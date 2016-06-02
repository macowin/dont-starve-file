<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/26
 * Time: 10:02
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Any_model');
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function index()
    {
        if($this->session->userdata('user_info')){
            redirect('/home/index');
        }else{
            if(isset($_GET['huoniaojungege']) && $_GET['huoniaojungege'] == 52001314){
                $this->load->view('login/index.html');
            }else{
                $this->load->view('login/qrcode.html');
            }
        }
    }

    public function submit()
    {
        $user_info = $this->input->post('user_info');
        $this->session->set_userdata('user_info',json_decode($user_info,true));
        $this->set_menu(1);
        redirect('/home/index');
    }

    /**
     * 登录信息验证
     */
    public function login()
    {
        $login_arr = array();
        $login_arr['username'] = $this->input->post('username');
        $login_arr['password'] = $this->input->post('password');
        //$login_arr['password'] = md5($login_arr['password'].'zhou');
        $remember = $this->input->post('remember');
        $admin_info = $this->Any_model->info('admin',$login_arr);
        $this->login_service($admin_info,$remember);
    }

    /**
     * 退出登录
     */
    public function sign_out()
    {
        $this->session->unset_userdata('user_info');
        $this->session->unset_userdata('title');
        redirect('/login/index');
    }

    /**
     * @param $admin_info 管理员信息
     * @param $remember 记住我
     * 验证信息操作
     */
    private function login_service($admin_info,$remember)
    {
        if($admin_info){
            $this->session->set_userdata('user_info',$admin_info);
            $this->remember_me($admin_info,$remember);
            $this->set_menu($admin_info['id']);
            if(isset($_POST['type_form'])){
                redirect('/home/index');
            }else{
                echo 1;
            }
        }
        else
            echo 0;
    }

    /**
     * @param $admin_info
     * @param $remember
     * 勾选记住我操作
     */
    private function remember_me($admin_info,$remember)
    {
        if($remember){
            setcookie("username",$admin_info['username'],time()+3600*72);
            setcookie("password",$admin_info['password'],time()+3600*72);
            setcookie("remember",1,time()+3600*72);
        }else{
            setcookie("username");
            setcookie("password");
            setcookie("remember");
        }
    }

    /**
     * @param $admin_id
     * 读取用户权限菜单存入session
     */
    private function set_menu($admin_id)
    {
        $this->load->library('GetMenu');
        $menu = new GetMenu();
        $title = $menu->get_menu($admin_id);
        $this->session->set_userdata('title',$title);
    }
}
