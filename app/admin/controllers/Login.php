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
        if($this->session->userdata('admin_info')){
            redirect('/home/index');
        }else{
            $this->load->view('login/index.html');
        }
    }

    /**
     * ��¼��Ϣ��֤
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
     * �˳���¼
     */
    public function sign_out()
    {
        $this->session->unset_userdata('admin_info');
        $this->session->unset_userdata('title');
        redirect('/login/index');
    }

    /**
     * @param $admin_info ����Ա��Ϣ
     * @param $remember ��ס��
     * ��֤��Ϣ����
     */
    private function login_service($admin_info,$remember)
    {
        if($admin_info){
            $this->session->set_userdata('admin_info',$admin_info);
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
     * ��ѡ��ס�Ҳ���
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
     * ��ȡ�û�Ȩ�޲˵�����session
     */
    private function set_menu($admin_id)
    {
        $this->load->library('GetMenu');
        $menu = new GetMenu();
        $title = $menu->get_menu($admin_id);
        $this->session->set_userdata('title',$title);
    }
}
