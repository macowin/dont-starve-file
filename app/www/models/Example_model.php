<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/19
 * Time: 14:36
 */
class Example_model extends MY_Model
{

    protected $table;

    public function __construct(){
        parent::__construct();
        $this->table = 'example';
    }

    public function add($data){
        return $this->_add($this->table,$data);
    }

    public function info($search=array(), $select='*'){
        return $this->_info($this->table,$search,$select);
    }

    public function edit($search=array(),$data){
        return $this->_edit($this->table,$search,$data);
    }

    public function nums($search = array()){
        return $this->_count($this->table,$search);
    }

    public function table_list($search=array(),$orderby='desc',$field='*',$limit=array()){
        return $this->_list($this->table,$search,$orderby,$field,$limit);
    }

    public function join($search=array(),$orderby='desc',$field='*',$limit=array()){
        $table_arr = array(
            'user as a',
            array(
                'user_base as b',
                'b.user_id = a.id',
                'left'
            ),
            array(
                'user_info as c',
                'c.user_id = a.id',
                'left'
            )
        );
        return $this->_list($table_arr,$search,$orderby,$field,$limit);
    }
}