<?php
/**
 * Copyright (c) 2016.  hangzhou BOYE .Co.Ltd. All rights reserved
 */

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-16
 * Time: 16:22
 */

namespace app\src\system\model;


use think\Model;

class Datatree extends Model
{
    protected $table = "common_datatree";

    protected $auto   = ['update_time'];
    protected $insert = ['create_time'];

    protected function setUpdateTimeAttr(){
      return time();
    }

    protected function setCreateTimeAttr(){
      return time();
    }
}