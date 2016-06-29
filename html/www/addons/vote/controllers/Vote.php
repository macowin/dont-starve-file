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
        $vote_data = $this->Any_model->table_list('vote',array('status'=>1),array('votes'=>'desc'),'');
        $record_info = $this->Any_model->info('vote_record',array('user_id'=>$this->user['id']));
        foreach($vote_data as &$value){
            $user = $this->Any_model->info('login_user',array('id'=>$this->user['id']),'nickname');
            $value['nickname'] = $user['nickname'];
        }
        $this->load->view("vote/index.html",array('vote'=>$vote_data,'record'=>$record_info));
    }

    public function add()
    {
        $this->load->view("vote/add.html");
    }

    public function check()
    {
        if($this->user['id'] != 1){
            echo "<script>history.back();</script>";
        }
        $vote_data = $this->Any_model->table_list('vote',array('status'=>0),'','');
        foreach($vote_data as &$value){
            $user = $this->Any_model->info('login_user',array('id'=>$this->user['id']),'nickname');
            $value['nickname'] = $user['nickname'];
        }
        $this->load->view("vote/check.html",array('vote'=>$vote_data));
    }

    public function check_service()
    {
        $vote_id = $this->input->post("vote_id");
        if($this->user['id'] != 1){
            exit;
        }
        $flag = $this->Any_model->edit('vote',array('id'=>$vote_id),array('status'=>1));
        if($flag){
            echo 1;
        }else{
            echo 0;
        }
    }

    public function add_service()
    {
        $info = $this->input->post("info");
        $images = $this->input->post("image");
        $vote = array('user_id'=>$this->user['id'],'info'=>$info,'img_url'=>$images[0],'status'=>0,'created_time'=>time());
        $flag = $this->Any_model->add('vote',$vote);
        if($flag){
            echo 1;
        }else{
            echo 0;
        }
    }

    public function support_service()
    {
        $vote_id = $this->input->post("vote_id");
        if(!$vote_id){
            exit;
        }
        $record_info = $this->Any_model->info('vote_record',array('user_id'=>$this->user['id']));
        if($record_info){
            echo 2;
            exit;
        }
        $vote_info = $this->Any_model->info('vote',array('id'=>$vote_id));
        $flag2 = $this->Any_model->edit('vote',array('id'=>$vote_id),array('votes'=>$vote_info['votes']+1));
        $flag = $this->Any_model->add("vote_record",array('user_id'=>$this->user['id'],'vote_id'=>$vote_id,'created_time'=>time()));
        if($flag && $flag2){
            echo 1;
        }else{
            echo 0;
        }

    }

    public function install()
    {
        if($_GET['key'] != KEY){
            exit;
        }
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
        if($vote_flag && $record_flag){
            echo "<script>alert('安装成功');window.opener=null;window.close();</script>";
        }
    }

    public function uninstall()
    {
        if($_GET['key'] != KEY){
            exit;
        }
        $vote_flag = $this->db->query("drop table vote");
        $record_flag = $this->db->query("drop table vote_record");
        if($record_flag && $vote_flag){
            echo 1;
        }else{
            echo 0;
        }
    }
}