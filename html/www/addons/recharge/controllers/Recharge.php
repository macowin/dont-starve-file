<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/2
 * Time: 19:55
 */
class Recharge extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        /* 云支付3方库 */
        $this->load->library('Yunpay');
        $this->pay_config = array(
            'partner' => '8217146034179662',//合作者ID
            'key' => 'vjijgxvWrKf8A2S73HTpAELE49StDKMr',//MD5加密KEY
            'seller_email' => '773729704@qq.com'//平台账户邮箱号
        );
    }

    public function index()
    {
        $this->load->view("recharge/index.html");
    }

    public function done($recharge_id)
    {
        if(!isset($_GET['KEY']) || $_GET['KEY'] != KEY){
            exit;
        }
        $info = $this->Any_model->info('recharge',array('id'=>$recharge_id),'id,cash,status');
        $this->load->view("recharge/done.html",$info);
    }

    public function recharge_service()
    {
        $data['cash'] = $_POST['price'];
        $data['user_id'] = $this->user['id'];
        $data['serial_number'] = 'ds-'.time();
        $data['status'] = 0;
        $data['created_time'] = time();
        $data['recharge_id'] = $this->Any_model->add('recharge',$data);
        $order_info = self::order_info($data);
        if($data['order_id']){
            $submit_pay = $this->yunpay->pay_submit($order_info,$this->pay_config['key']);
            echo $submit_pay;
        }else{
            echo "<script>alert('服务器异常,请重试');history.back();</script>";
        }
    }

    protected function order_info($data)
    {
        $order_info = array(
            "partner" => trim($this->pay_config['partner']),
            "seller_email"	=> $this->pay_config['seller_email'],
            "out_trade_no"	=> $data['serial_number'],
            "subject"	=> '社区账户充值',
            "total_fee" => $data['cash'],
            "body"	=> '账户充值',
            "nourl"	=> base_url('/weixin/notify_recharge/'.$data['recharge_id'].'?key='.KEY),
            "reurl"	=> base_url('/weixin/close'),
            "orurl"	=> '',
            "orimg"	=> ''
        );
        return $order_info;
    }

    public function install()
    {
        if($_GET['key'] != KEY){
            exit;
        }
        $vote = "create table recharge (".
                "id int not null auto_increment primary key comment '主键(自增长)',".
                "serial_number varchar(20) not null comment '充值编号',".
                "user_id int not null comment '充值用户ID',".
                "cash int not null comment '充值金额（元）',".
                "note varchar(100) not null comment '充值日志',".
                "status int not null comment '支付状态0为失败，1为成功',".
                "created_time int not null comment '创建时间')";
        $flag = $this->db->query($vote);
        if($flag){
            echo "<script>alert('安装成功');window.opener=null;window.close();</script>";
        }
    }

    public function uninstall()
    {
        if($_GET['key'] != KEY){
            exit;
        }
        $flag = $this->db->query("drop table recharge");
        if($flag){
            echo 1;
        }else{
            echo 0;
        }
    }
}