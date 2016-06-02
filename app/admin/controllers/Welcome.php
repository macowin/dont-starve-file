<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
		$this->load->library('GetMenu');
		$menu = new GetMenu();
		$title = $menu->get_menu(1);
		$this->session->set_userdata('title',$title);


		$this->load->view('test/test.html');
		/*$data = array(
			'table',
			array(
				'name',
				'a.id = b.id',
				'left'
			),
			array(
				'name',
				'a.id = b.id',
				'left'
			),
			array(
				'name',
				'a.id = b.id',
				'left'
			),
			array(
				'name',
				'a.id = b.id',
				'left'
			)
		);
		foreach($data as $key=>$value){
			if($key == 0){
				echo $value."\n";
			}else{
				echo $value[0].$value[1].$value[2]."\n";
			}
		}*/
	}

	public function test()
	{
		$this->load->view('test/test.html');
	}
}
