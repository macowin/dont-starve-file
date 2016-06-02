<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/19
 * Time: 14:36
 */
class Any_model extends MY_Model
{
    public function __construct(){
        parent::__construct();
    }

    public function add($table, $data){
        return $this->_add($table,$data);
    }

    public function info($table, $search=array(), $select='*'){
        return $this->_info($table, $search, $select);
    }

    public function edit($table, $search=array(), $data){
        return $this->_edit($table, $search, $data);
    }

    public function nums($table, $search = array()){
        return $this->_count($table, $search);
    }

    public function table_list($table, $search=array(), $orderby='desc', $field='*', $limit=array()){
        return $this->_list($table, $search, $orderby, $field, $limit);
    }

    public function join($table, $search=array(), $orderby='desc', $field='*', $limit=array()){
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
        return $this->_list($table, $search, $orderby, $field, $limit);
    }
}