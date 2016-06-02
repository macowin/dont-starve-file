<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/26
 * Time: 11:33
 */
class Home extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['data_key'] = array(
            '序号'=>'5%',
            '用户名称'=>'10%',
            '邮箱'=>'10%',
            '手机号码'=>'10%',
            '状态'=>'10%',
            '操作'=>'10%',

        );
        $data['now_page'] = isset($_GET['page'])?$_GET['page']:1;
        $data['maxpage'] = 9;
        $data['page_return_url'] = '/home/index?page=';
        $this->load->view('home/index.html',$data);
    }

    public function user()
    {
        $data['data_key'] = array(
            '序号'=>'5%',
            '用户名称'=>'10%',
            '邮箱'=>'10%',
            '手机号码'=>'10%',
            '状态'=>'10%',
            '操作'=>'10%',

        );
        $data['now_page'] = isset($_GET['page'])?$_GET['page']:1;
        $data['maxpage'] = 9;
        $data['page_return_url'] = '/home/index?page=';
        $this->load->view('home/index.html',$data);
    }
}