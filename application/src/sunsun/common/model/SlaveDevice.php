<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-05-17
 * Time: 09:02
 */

namespace app\src\sunsun\common\model;


use think\Model;

class SlaveDevice extends Model
{
    protected $table = "sunsun_slave_device";
    protected $insert = ['create_time','update_time'];
    protected $update = ['update_time'];

    public function setCreateTimeAttr(){
        return time();
    }

    public function setUpdateTimeAttr(){
        return time();
    }
}