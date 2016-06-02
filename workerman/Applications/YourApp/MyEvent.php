<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Db;
use \Workerman\Lib\Timer;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class MyEvent
{
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $message)
   {
       // 客户端传递的是json数据
       $message_data = json_decode($message, true);
       $message_data['scene_id'] = substr($message_data['scene_id'],0,10);
       var_dump($message_data);
       switch($message_data['type']){
           case 'getQrcode':
               $add_qrcode = array('scene_id'=>$message_data['scene_id'],'created_time'=>time());
               Db::instance('db')->insert('login_qrcode')->cols($add_qrcode)->query();
               $return_data['type'] = 'qrcode';
               $return_data['qrcode'] = file_get_contents('http://l4d2.zhoujianjun.cn/weixin/getQrcode?scene_id='.$message_data['scene_id']);
               var_dump($return_data);
               Gateway::sendToCurrentClient(json_encode($return_data));
               return;
           case 'getStatus':
               $qrcode_info = Db::instance('db')->select('*')->from('login_qrcode')->where('scene_id='.$message_data['scene_id'])->row();
               var_dump($qrcode_info);
               if($qrcode_info['user_id']){
                   $user_info = Db::instance('db')->select('*')->from('login_user')->where('id='.$qrcode_info['user_id'])->row();
                   $user_info['type'] = 'user_info';
                   Gateway::sendToCurrentClient(json_encode($user_info));
                   var_dump($user_info);
               }else if($qrcode_info['scan_time']){
                   if($qrcode_info['scan_time'] > time() - 30){
                       $return_data['type'] = 'scan_start';
                   }else{
                       $return_data['type'] = 'scan_end';
                   }
                   Gateway::sendToCurrentClient(json_encode($return_data));
                   var_dump($return_data);
               }
               return;
       }

       /*$db = Db::instance('db');
       $user_array = Db::instance('db')->select('*')->from('server_info')->where('id=1')->row();
       var_dump($user_array);
        Gateway::sendToAll(json_encode($user_array));
       $obj = json_decode($message);
       var_dump($obj,$obj->name);
       $db->insert('asd')->cols(array('title'=>$obj->name))->query();
       $online_count = Gateway::getAllClientCount();
       Gateway::sendToAll("($online_count)$client_id: $message");*/
   }

    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     */
    public static function onClose($client_id)
    {
        //$nums = Db::instance('db')->delete('login_qrcode')->where('created_time < '.time()-60)->query();
        //Db::instance('db')->query("DELETE FROM `login_qrcode` WHERE created_time < ".time()-60);
        //echo $nums;
    }
}
