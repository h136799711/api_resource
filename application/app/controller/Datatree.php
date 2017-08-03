<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-24
 * Time: 16:38
 */

namespace app\app\controller;


use app\sdk\req\ByDatatreeRequest;

class Datatree extends App
{
    public function query(){
        $parent_id = $this->_param('parent_id','');
        $level = $this->_param('level','');
        $order = $this->_param('order','');
        $map = [];
        if(!empty($level)){
            $map['level'] = $level;
        }
        if(strlen($parent_id) > 0){
            $map['parent_id'] = $parent_id;
        }
        if($order == 1){
            $order = "sort desc";
        }elseif($order == 2){
            $order = "sort asc";
        }else{
            $order = "sort desc";
        }

        $fields = $this->_param('fields','parents,parentid,notes,create_time,update_time,level,iconurl,data_level,sort,alias,id,code,name');
        $pageHelper = $this->getPageHelper();
        $req = new ByDatatreeRequest();
        $result = $req->query($map,$pageHelper,$order,$fields);
        $this->returnResult($result);
    }

    public function delete(){
        $id = $this->_param('id','','缺少id');
        $req = new ByDatatreeRequest();
        $result = $req->delete($id);
        var_dump($result);
        $this->returnResult($result);
    }
}