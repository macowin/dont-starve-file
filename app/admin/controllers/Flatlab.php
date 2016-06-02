<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/19
 * Time: 16:33
 */
class Flatlab extends CI_Controller
{
    public function index()
    {
        $this->load->view('index.html');
    }

    public function error_404()
    {
        $this->load->view('404.html');
    }

    public function error_500()
    {
        $this->load->view('500.html');
    }

    public function basic_table()
    {
        $this->load->view('basic_table.html');
    }

    public function blank()
    {
        $this->load->view('blank.html');
    }

    public function buttons()
    {
        $this->load->view('buttons.html');
    }

    public function calendar()
    {
        $this->load->view('calendar.html');
    }

    public function charts()
    {
        $this->load->view('charts.html');
    }

    public function dynamic_table()
    {
        $this->load->view('dynamic_table.html');
    }

    public function font_awesome()
    {
        $this->load->view('font_awesome.html');
    }
    public function form_component()
    {
        $this->load->view('form_component.html');
    }
    public function form_validation()
    {
        $this->load->view('form_validation.html');
    }
    public function form_wizard()
    {
        $this->load->view('form_wizard.html');
    }
    public function general()
    {
        $this->load->view('general.html');
    }
    public function grids()
    {
        $this->load->view('grids.html');
    }
    public function inbox()
    {
        $this->load->view('inbox.html');
    }
    public function invoice()
    {
        $this->load->view('invoice.html');
    }
    public function login()
    {
        $this->load->view('login.html');
    }
    public function profile()
    {
        $this->load->view('profile.html');
    }
    public function profile_activity()
    {
        $this->load->view('profile-activity.html');
    }
    public function profile_edit()
    {
        $this->load->view('profile-edit.html');
    }
    public function slider()
    {
        $this->load->view('slider.html');
    }
    public function widget()
    {
        $this->load->view('widget.html');
    }

}