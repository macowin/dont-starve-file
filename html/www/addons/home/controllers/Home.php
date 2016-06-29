<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/2
 * Time: 19:55
 */
class Home extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = array();
        $data['menu'] = $this->Any_model->table_list('menu',array('type'=>2),array('id'=>'asc'));
        $this->load->view("home/index.html",$data);
    }

    public function install()
    {
        if ($_GET['key'] != KEY) {
            exit;
        }
        $home = "create table home_picture (".
            "id int not null auto_increment primary key comment '主键(自增长)',".
            "img_url varchar(200) not null comment '图片链接',".
            "link varchar(200) not null comment '点击链接',".
            "created_time int not null comment '创建时间')";
        $flag = $this->db->query($home);
        if($flag){
            echo "<script>alert('安装成功');window.opener=null;window.close();</script>";
        }
    }

    public function uninstall()
    {
        if($_GET['key'] != KEY){
            exit;
        }
        $flag = $this->db->query("drop table home_picture");
        if($flag){
            echo 1;
        }else{
            echo 0;
        }
    }
}