<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-24
 * Time: 16:31
 */

namespace app\domain;


use app\src\datatree\action\DatatreeAddAction;
use app\src\datatree\action\DatatreeDeleteAction;
use app\src\datatree\action\DatatreeUpdateAction;
use app\src\system\logic\DatatreeLogicV2;
use think\Db;

class DatatreeDomain extends BaseDomain {
    
    public function query(){
        $parent_id = $this->_post('parent_id','');
        $level = $this->_post('level','');
        $map = [];
        if(!empty($level)){
            $map['level'] = $level;
        }
        if(strlen($parent_id) > 0){
            $map['parent_id'] = $parent_id;
        }
        $fields = "*";
        $order = "id asc";
        $page = $this->getPageParams();
        $result = (new DatatreeLogicV2())->query($map,$page->queryParam(),$order,false,$fields);
        $this->apiReturnSuc($result);
    }

    /**
     * 数据字典添加接口
     *
     * @version 1.0.0
     * @history 1.0.0 创建该接口
     */
    public function add(){
        $parent_id = $this->_post('parent_id','');
        $alias = $this->_post('alias','');
        $name = $this->_post('name','');
        $notes = $this->_post('notes','');
        $sort = $this->_post('sort',0);
        $iconurl = $this->_post('iconurl','');
        $data_level = $this->_post('data_level',0);

        $entity = [
            'parent_id'=>$parent_id,
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

    /**
     * 数据字典更新接口
     *
     * @version 1.0.0
     * @history 1.0.0 创建该接口
     */
    public function update(){
        $id = $this->_post('id','');
        $alias = $this->_post('alias','');
        $name = $this->_post('name','');
        $notes = $this->_post('notes','');
        $sort = $this->_post('sort','');
        $data_level = $this->_post('data_level','');
        $iconurl = $this->_post('iconurl',-1);

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

        $result = (new DatatreeUpdateAction())->update($id,$entity);
        $this->returnResult($result);
    }

    public function delete(){
        $id = $this->_post('id','');
        $result = (new DatatreeDeleteAction())->delete($id);
        $this->returnResult($result);
    }

    /**
     * 批量删除
     *
     * @version 1.0.0
     * @history 1.0.0 创建该接口
     */
    public function bulkDelete(){
        $ids = $this->_post('id','');
        $idArr = explode(",",$ids);
        Db::startTrans();
        foreach ($idArr as $id){
            if(!empty($id)){
                $result = (new DatatreeDeleteAction())->delete($id);
                if(!$result['status']){
                    Db::rollback();
                    $this->apiReturnErr($result['info']);
                }
            }
        }
        Db::commit();
        $this->apiReturnSuc(lang('success'));
    }


}