<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/2
 * Time: 19:55
 */
class Alert extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->view("alert/index.html");
    }

    public function install()
    {
        if ($_GET['key'] != KEY) {
            exit;
        }
        echo "<script>alert('安装成功,可通过/vote/index进入主页，通过/vote/add进入活动添加页面');window.opener=null;window.close();</script>";
    }

    public function uninstall()
    {
        if($_GET['key'] != KEY){
            exit;
        }
        echo 1;
    }
}