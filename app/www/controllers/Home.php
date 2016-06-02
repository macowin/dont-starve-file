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
        $this->load->view('index.html');
    }

    public function goods_data()
    {
        $data = $this->_get_goods(array('page'=>$_GET['page'],'status'=>$_GET['status'],'num'=>15));
        echo "{".json_encode($data)."}";
    }

    public function goods_already()
    {
        $data = $this->_get_already_goods(array('page'=>$_GET['page'],'status'=>2,'num'=>15));
        echo "{".json_encode($data)."}";
    }

    /* 封装 进行中 和 带揭晓 的商品信息 */
    private function _get_goods($param)
    {
        /**
         * $param['page'] 当前页数
         * $param['status'] 商品状态
         * $param['num'] 每页商品数量
         */
        $param['page'] = empty($param['page']) ? 1 : $param['page'];
        $max = $this->Any_model->nums('goods', array('status'=>$param['status']));
        $data['maxpage'] = ceil($max / $param['num']);
        $limit = array(($param['page'] - 1) * $param['num'], $param['num']);
        $goods_list = $this->Any_model->table_list('goods', array('status'=>$param['status']), 'desc', 'id,nums,selled_nums,name', $limit);
        foreach($goods_list as &$value){
            $picture = $this->Any_model->info('goods_picture', array('goods_id'=>$value['id']), 'img_url');
            $value['picture'] = $picture['img_url'];
        }
        $data['data'] = $goods_list;
        return $data;
    }

    /* 封装 已揭晓 的商品信息 */
    private function _get_already_goods($param)
    {
        /**
         * $param['page'] 当前页数
         * $param['status'] 商品状态
         * $param['num'] 每页商品数量
         */
        $param['page'] = empty($param['page']) ? 1 : $param['page'];
        $max = $this->Any_model->nums('announce', '');
        $data['maxpage'] = ceil($max / $param['num']);
        $limit = array(($param['page'] - 1) * $param['num'], $param['num']);
        $announce_list = $this->Any_model->table_list('announce', '', 'desc', 'goods_id,user_id,indiana,sse_numerical,created_time', $limit);
        foreach($announce_list as &$value){
            $picture = $this->Any_model->info('goods_picture', array('goods_id'=>$value['goods_id']), 'img_url');
            $user = $this->Any_model->info('user', array('id'=>$value['user_id']), 'nickname');
            $value['goods_info'] = $this->Any_model->info('goods', array('id'=>$value['goods_id']), 'serial_nums,name,selled_nums');
            $value['goods_info']['picture'] = $picture['img_url'];
            $value['nickname'] = $user['nickname'];
            $value['created_time'] = date("Y-m-d H:i:s",$value['created_time']);
        }
        $data['data'] = $announce_list;
        return $data;
    }

}