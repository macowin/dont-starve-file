<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/19
 * Time: 19:29
 */
class GetMenu
{
    public function __construct()
    {

    }

    public function get_menu($admin_id)
    {
        $CI = & get_instance();
        $CI->load->model('Any_model');
        $CI->load->library('session');
        $admin_competence = $CI->Any_model->table_list('admin_competence',array('admin_id'=>$admin_id),'asc','subtitle_id');
        $title = $CI->Any_model->table_list('menu_title','','asc');
        foreach($title as &$value){
            foreach($admin_competence as $val){
                $subtitle = $CI->Any_model->info('menu_subtitle',array('id'=>$val['subtitle_id'],'title_id'=>$value['id']));
                if($subtitle){
                    $value['subtitle'][] = $subtitle;
                }
            }
        }
        return $title;
    }
}