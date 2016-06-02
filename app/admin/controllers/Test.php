<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function table()
    {
        $this->load->view('test/table.html');
    }

    public function table2()
    {
        $this->load->view('test/table2.html');
    }

    public function button()
    {
        $this->load->view('test/button.html');
    }

    public function button2()
    {
        $this->load->view('test/button2.html');
    }
}