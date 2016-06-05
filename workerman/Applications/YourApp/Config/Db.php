<?php
namespace Config;
/**
 * mysql����
 * @author walkor
 */
class Db
{
    /**
     * ���ݿ��һ��ʵ�����ã���ʹ��ʱ����������ʹ��
     * $user_array = Db::instance('db1')->select('name,age')->from('users')->where('age>12')->query();
     * �ȼ���
     * $user_array = Db::instance('db1')->query('SELECT `name`,`age` FROM `users` WHERE `age`>12');
     * @var array
     */
    public static $db = array(
        'host'    => '5724798d2c6cf.sh.cdb.myqcloud.com',
        'port'    => 8103,
        'user'    => 'cdb_outerroot',
        'password' => 'huoniaojungege@',
        'dbname'  => 'dont-starve-file',
        'charset'    => 'utf8',
    );
    /*public static $db = array(
        'host'    => '182.254.132.50',
        'port'    => 3306,
        'user'    => 'root',
        'password' => 'jungege520',
        'dbname'  => 'weixin_l4d2',
        'charset'    => 'utf8',
    );*/
}