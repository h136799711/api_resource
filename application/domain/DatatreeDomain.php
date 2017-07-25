<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-24
 * Time: 16:31
 */

namespace app\domain;


use app\src\system\logic\DatatreeLogicV2;

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
        $order = "create_time desc";
        $page = $this->getPageParams();
        $result = (new DatatreeLogicV2())->query($map,$page->queryParam(),$order,false,$fields);
        $this->apiReturnSuc($result);
    }
    
    public function add(){
        
    }
    
    public function update(){
        
    }
    
    public function delete(){
        
    }
    
    
}