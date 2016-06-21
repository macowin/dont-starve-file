<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/2
 * Time: 19:55
 */
class Character extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['characters'] = $this->Any_model->table_list('character',array('status'=>1),'desc','name,desc,type,id,head_img');
        $this->load->view('character/index.html',$data);
    }

    public function detail($character_id)
    {
        $data['characters'] = $this->Any_model->info('character',array('id'=>$character_id));
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

    private function _get_characters($param)
    {

    }
}