<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-24
 * Time: 16:31
 */

namespace app\domain;


use app\src\base\helper\ValidateHelper;
use app\src\datatree\action\DatatreeAddAction;
use app\src\datatree\action\DatatreeDeleteAction;
use app\src\datatree\action\DatatreeUpdateAction;
use app\src\system\logic\DatatreeLogicV2;
use app\src\tool\helper\RadixHelper;

class DatatreeDomain extends BaseDomain {
    
    public function query(){
        $parent_id = $this->_post('parent_id','');
        $level = $this->_post('level','');
        $map = [];
        if(!empty($level)){
            $map['level'] = $level;
        }
        if(strlen($parent_id) > 0){
            $map['parentid'] = $parent_id;
        }
        $fields = "*";
        $order = "id asc";
        $page = $this->getPageParams();
        $result = (new DatatreeLogicV2())->query($map,$page->queryParam(),$order,false,$fields);
        $this->apiReturnSuc($result);
    }
    
    public function add(){
        $parent_id = $this->_post('parent_id','');
        $alias = $this->_post('alias','');
        $name = $this->_post('name','');
        $notes = $this->_post('notes','');
        $sort = $this->_post('sort',0);
        $iconurl = $this->_post('iconurl','');
        $data_level = $this->_post('data_level',0);

        $entity = [
            'parentid'=>$parent_id,
            'alias'=>$alias,
            'name'=>$name,
            'notes'=>$notes,
            'sort'=>$sort,
            'iconurl'=>$iconurl,
            'data_level'=>$data_level,
        ];
        $result = (new DatatreeAddAction())->add($entity);
        $this->returnResult($result);
    }
    
    public function update(){
        $id = $this->_post('id','');
        $alias = $this->_post('alias','');
        $name = $this->_post('name','');
        $notes = $this->_post('notes','');
        $sort = $this->_post('sort',0);
        $data_level = $this->_post('data_level',0);
        $iconurl = $this->_post('iconurl','');

        $entity = [
            'parentid'=>$parent_id,
            'alias'=>$alias,
            'name'=>$name,
            'notes'=>$notes,
            'sort'=>$sort,
            'iconurl'=>$iconurl,
            'data_level'=>$data_level,
        ];
        $result = (new DatatreeUpdateAction())->update($id,$entity);
        $this->returnResult($result);
    }
    
    public function delete(){
        $id = $this->_post('id','');
        $result = (new DatatreeDeleteAction())->delete($id);
        $this->returnResult($result);
    }


}