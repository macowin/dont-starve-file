<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/2
 * Time: 19:55
 */
class Install extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $file_name = $_GET['name'];
        $addon_path = $_GET['name'];
        ucfirst($file_name);
        rename(FCPATH."addons/$addon_path/controllers/$file_name.php",APPPATH."controllers/$file_name.php");
        rename(FCPATH."addons/$addon_path/views/$addon_path",APPPATH."views/$addon_path");
        //echo FCPATH.APPPATH;
        //echo $_GET['name'];
    }
}