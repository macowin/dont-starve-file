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
}