<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/2
 * Time: 19:55
 */
class Vote extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->view("vote/index.html");
    }

    public function add()
    {
        $this->load->view("vote/add.html");
    }

    public function add_service()
    {
        echo 1;exit;
        $info = $this->input->post("info");
        $images = $this->input->post("image");
        var_dump($info,$images);
    }

    public function install()
    {
        $vote = "create table vote (".
                "id int not null auto_increment primary key comment '主键(自增长)',".
                "user_id int not null comment '投票发起人ID',".
                "votes int not null comment '投票数量',".
                "info varchar(100) not null comment '投票描述信息',".
                "img_url varchar(200) not null comment '投票图片链接',".
                "status tinyint not null comment '审核状态：0待审核，1已审核，2审核不通过',".
                "created_time int not null comment '创建时间')";
        $vote_flag = $this->db->query($vote);

        $record = "create table vote_record (".
            "id int not null auto_increment primary key comment '主键(自增长)',".
            "user_id int not null comment '投票人ID',".
            "vote_id int not null comment '投票ID',".
            "created_time int not null comment '创建时间')";
        $record_flag = $this->db->query($record);
        if($record_flag && $record_flag){
            echo "<script>alert('安装成功,可通过/vote/index进入主页，通过/vote/add进入活动添加页面');window.opener=null;window.close();</script>";
        }
    }

    public function uninstall()
    {
        $vote_flag = $this->db->query("drop table vote");
        $record_flag = $this->db->query("drop table vote_record");
        if($record_flag && $vote_flag){
            echo "<script>alert('卸载成功');window.opener=null;window.close();</script>";
        }
    }
}