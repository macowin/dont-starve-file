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
        $this->load->helper("url");
    }

    public function index()
    {
        $addon_path = $_GET['name'];
        $this->recurse_copy(FCPATH."addons/$addon_path/controllers/",APPPATH."controllers/");
        $this->recurse_copy(FCPATH."addons/$addon_path/views/$addon_path",APPPATH."views/$addon_path");
        $this->recurse_copy(FCPATH."addons/$addon_path/public/$addon_path",FCPATH."public/$addon_path");
        redirect("/$addon_path/install?key=".KEY);
    }

    public function uninstall()
    {
        $file_name = ucfirst($_GET['name']);
        $addon_path = $_GET['name'];
        $flag = file_get_contents(base_url("/$addon_path/uninstall?key=".KEY));
        if($flag == 1){
            $flag1 = @unlink (APPPATH."/controller/$file_name.php");
            $flag2 = @unlink (APPPATH."/views/$addon_path");
            $flag3 = @unlink (FCPATH."/public/$addon_path");
            if(!$flag2 && $flag1 && !$flag3){
                echo "<script>alert('卸载成功')</script>";
            }
        }else{
            echo "<script>alert('卸载失败')</script>";
        }
    }

    private function recurse_copy($src,$dst) {  // 原目录，复制到的目录
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}