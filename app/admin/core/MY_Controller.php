<?php
/**
 * Created by PhpStorm.
 * User: 周建军
 * Date: 2016/5/19
 * Time: 14:06
 */

class MY_Controller extends CI_Controller
{
    public $admin;
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->admin = $this->session->userdata('admin_info');
    }
}