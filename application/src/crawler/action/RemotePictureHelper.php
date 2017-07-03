<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-03
 * Time: 16:51
 */

namespace app\src\crawler\action;


use app\src\base\helper\ResultHelper;
use app\src\file\logic\UserPictureLogic;

class RemotePictureHelper
{
    public static function download($url){
        $ext=strrchr($url,'.');
        $save_dir = "./upload/r_pic";
        $month = date("ymd");
        $save_dir = $save_dir.'/'.$month;
        $filename = md5($url).$ext;
        $result = (self::getImage($url,$save_dir,$filename,1));
        if(!$result['status']){
            return $result;
        }
        $info = $result['info'];
        $size = filesize($info['save_path']);
        $md5 = md5_file($info['save_path']);
        $sha1 = sha1_file($info['save_path']);
        $entity = [
            'uid'=>0,
            'path'=> ltrim($info['save_path'],"."),
            'savename'=>$filename,
            'ori_name'=>$url,
            'size'=>$size,
            'url'=>$info['save_path'],
            'md5'=>$md5,
            'sha1'=>$sha1,
            'status'=>1,
            'create_time'=>time(),
            'type'=>'remote2local',
            'ext'=>$ext,
            'porn_prop'=>0
        ];
        $result = (new UserPictureLogic())->add($entity);
        return $result;
    }

    public static function getImage($url,$save_dir='',$filename='',$type=0){
        if(trim($url)==''){
            return ResultHelper::error('图片地址为空');
        }
        if(trim($save_dir)==''){
            $save_dir='./';
        }
        if(trim($filename)==''){//保存文件名
            $ext = strrchr($url,'.');

            if(!in_array($ext,['.gif',".jpg",".png"])){
                return ResultHelper::error('图片后缀非法(支持jpg,gif,png)');
            }
            $filename=time().$ext;
        }
        if(0!==strrpos($save_dir,'/')){
            $save_dir.='/';
        }
        //创建保存目录
        if(!file_exists($save_dir) && !mkdir($save_dir,0777,true)){
            return ResultHelper::error('图片保存路径不存在或无写入权限');
        }
        //获取远程文件所采用的方法
        if($type){
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $img = curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $img=ob_get_contents();
            ob_end_clean();
        }
        // $size=strlen($img);
        $path =  $save_dir.$filename;
        if(file_exists($path)){
            return ResultHelper::error('图片已存在');
        }
        //文件大小
        $fp2 = @fopen($path,'a');
        if($fp2 === false){
            return ResultHelper::error('图片保存到本地失败');
        }
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);
        return ResultHelper::success(['file_name'=>$filename,'save_path'=>$save_dir.$filename]);
    }
}