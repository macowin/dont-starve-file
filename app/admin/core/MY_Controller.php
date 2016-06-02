<?php
/**
 * Created by PhpStorm.
 * User: 周建军
 * Date: 2016/5/19
 * Time: 14:06
 */

class MY_Controller extends CI_Controller
{
    public $user;
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->user = $this->session->userdata('user_info');
        if(!$this->user)
            redirect('/login/index');
    }
}