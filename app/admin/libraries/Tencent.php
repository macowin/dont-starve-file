<?php

// >= php 5.3.0
spl_autoload_register(function($class){
        $dir = dirname(__FILE__);
        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        if (0 === strpos($class, 'Tencentyun') && file_exists($dir.DIRECTORY_SEPARATOR.$class)) {
        include($dir.DIRECTORY_SEPARATOR.$class); 
        }
        });

use Tencentyun\ImageV2;
use Tencentyun\Auth;
use Tencentyun\Video;

class Tencent {
    public function add($file) {
        // V2增强版空间 带有空间和自定义文件名的示例
        // 上传图片
        $bucket = 'huoniao'; // 自定义空间名称，在http://console.qcloud.com/image/bucket创建
        $fileid = 'img'.time();  // 自定义文件名
        $uploadRet = ImageV2::upload($file, $bucket, $fileid);

        //var_dump('upload',$uploadRet);
        return $uploadRet;

        if (0 === $uploadRet['code']) {
            $fileid = $uploadRet['data']['fileid'];
            $downloadUrl = $uploadRet['data']['downloadUrl'];

            // 查询管理信息
            $statRet = ImageV2::stat($bucket, $fileid);
            var_dump('stat',$statRet);
            // 复制
            $copyRet = ImageV2::copy($bucket, $fileid);
            var_dump('copy', $copyRet);

            // 生成私密下载url
            $expired = time() + 999;
            $sign = Auth::getAppSignV2($bucket, $fileid, $expired);
            $signedUrl = $downloadUrl . '?sign=' . $sign;
            var_dump('downloadUrl:', $signedUrl);

            //生成新的单次签名, 必须绑定资源fileid，复制和删除必须使用，其他不能使用
            $fileid = $fileid.time().rand();  // 自定义文件名
            $expired = 0;
            $sign = Auth::getAppSignV2($bucket, $fileid, $expired);
            var_dump($sign);

            //生成新的多次签名, 可以不绑定资源fileid
            $fileid = '';
            $expired = time() + 999;
            $sign = Auth::getAppSignV2($bucket, $fileid, $expired);
            var_dump($sign);

        } else {
            var_dump($uploadRet);
        }

        //end of script
    }

    public function del($bucket, $fileid) {
        $delRet = ImageV2::del($bucket, $fileid);
        return $delRet;
    }
}
