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
        $this->returnResult($result);
    }

    public function add() {
        $parent_id = $this->_param('parent_id','');
        $alias = $this->_param('alias','');
        $name = $this->_param('name','');
        $notes = $this->_param('notes','');
        $sort = $this->_param('sort',0);
        $iconurl = $this->_param('iconurl','');
        $data_level = $this->_param('data_level',0);

        $entity = [
            'parent_id'=>$parent_id,
            'alias'=>$alias,
            'name'=>$name,
            'notes'=>$notes,
            'sort'=>$sort,
            'iconurl'=>$iconurl,
            'data_level'=>$data_level,
        ];
        $req = new ByDatatreeRequest();
        $result = $req->add($entity);
        $this->returnResult($result);
    }

    /*
     * 更新接口
     * @version 1.0.0
     * @history 1.0.0
     */
    public function update(){
        $id = $this->_param('id','');
        $alias = $this->_param('alias','');
        $name = $this->_param('name','');
        $notes = $this->_param('notes','');
        $sort = $this->_param('sort','');
        $data_level = $this->_param('data_level','');
        $iconurl = $this->_param('iconurl',-1);

        $entity = [
            'alias'=>$alias,
            'name'=>$name,
            'notes'=>$notes
        ];
        if($iconurl != -1){
            $entity['iconurl'] = $iconurl;
        }
        if(strlen($sort) > 0){
            $entity['sort'] = $sort;
        }
        if(strlen($data_level) > 0){
            $entity['data_level'] = $data_level;
        }

        $req = new ByDatatreeRequest();
        $result = $req->update($id,$entity);
        $this->returnResult($result);
    }
}