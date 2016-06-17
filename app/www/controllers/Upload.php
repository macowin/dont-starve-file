<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/2
 * Time: 19:55
 */
class Upload extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function image()
    {
        $file_base64 = $_POST['image'];
        $file_base64 = preg_replace('/data:.*;base64,/i', '', $file_base64);
        $file_base64 = base64_decode($file_base64);
        $img_name = 'data/'.time().'.jpg';
        file_put_contents($img_name, $file_base64);
        $file = $this->uploadimage(array('tmp_name'=>$img_name,'name'=>time()));
        if(isset($file['url'])){
            @unlink ($img_name);
            echo $file['url'];
        }else{
            echo 0;
        }
    }

    private function uploadimage($uploadfile)
    {
        $this->load->library('tencent');
        $file = $uploadfile['tmp_name'];
        $result = $this->tencent->add($file);

        if (!$result['code']){
            //成功
            $arr = array(
                'state' => 'SUCCESS',
                'url' => $result['data']['downloadUrl'],
                'tilte' => $uploadfile['name']);
        } else{
            //失败
            $arr = array(
                'state' => $result['message'],
            );
        }

        return $arr;
    }

}