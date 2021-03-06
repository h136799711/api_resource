<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace app\admin\controller;

use app\src\category\logic\CategoryLogic;
use app\src\category\logic\CategoryPropLogic;
use think\Log;

class Category extends Admin{

	public function index(){
        $params = [];
		$parent    = $this->_param('parent',0);
		$preparent = $this->_param('preparent',-1);
		$level     = $this->_param('level/d',0);
		$map = array(
			'parent'=>$parent
		);
		$name = $this->_param('name','');
		if(!empty($name)){
			$map['name'] = array('like',"%$name%");
			$params['name'] = $name;
		}

		$result =(new CategoryLogic()) ->getInfo(['id'=>$parent]);

		if(!$result['status']){
			$this->error($result['info']);
		}
		$parent_vo = $result['info'];
        $result =(new CategoryLogic()) ->getInfo(['id'=>$preparent]);
        $prepreparent = "";
        if($result['status']){
            $prepreparent = $result['info']['parent'];
        }

        $page = array('curpage' => $this->_param('p', 0), 'size' => config('LIST_ROWS'));

		$order = " display_order desc ";
		//
		$result = (new CategoryLogic()) ->queryWithPagingHtml($map,$page,$order,$params);
		//
		if($result['status']){
            $this->assign('level',$level);
            $this->assign('parent_vo',$parent_vo);
            $this->assign('prepreparent',$prepreparent);
            $this->assign('preparent',$preparent);
            $this->assign('parent',$parent);
            $this->assign('name',$name);
            $this->assign('show',$result['info']['show']);
            $this->assign('list',$result['info']['list']);
			return $this->boye_display();
		}else{
            Log::record('INFO:'.$result['info'],'[FILE] '.__FILE__.' [LINE] '.__LINE__);
			$this->error(L('UNKNOWN_ERR'));
		}
	}

	/**
	 * 一级类目添加
	 */
	public function add(){
		if(IS_GET){
			$parent = $this->_param('parent',0);
			$preparent = $this->_param('preparent',-1);
			$level = $this->_param('level/d',0);
//			$icon = $this->_param('icon',0);

			$this->assign("parent",$parent);
			$this->assign("preparent",$preparent);
			$this->assign("level",$level);
//			$this->assign("icon",$icon);
//			$this->assign("lang_list",LangHelper::getLangSupport());
			return $this->boye_display();
		}else{
			$parent = $this->_param('parent',0);
			$level = $this->_param('level',0);
			$root_id = $this->getRootId($parent);

			if($level+1 > 3) $this->error('最多允许3级类目');

			$entity = array(
				'name'=>$this->_param('name'),
                'img_id' => $this->_param('img_id'),
				'code'=>$this->_param('code'),
				'scope'=>$this->_param('scope'),
				'taxa_rate'=>$this->_param('taxrate'),
				'display_order'=>$this->_param('display_order'),
				'parent'=>$parent,
				'level'=>$level+1,
				'root_id'=>$root_id
			);
			$result =(new CategoryLogic())->add($entity);

            if($root_id == 0){
                $root_id = -1;
            }
			if($result){
				$this->success("添加成功！",url('Admin/Category/index',['level'=>$level,'parent'=>$parent,'preparent'=>$root_id]));
			}else{
				$this->error($result['info']);
			}
		}
	}

    /**
     * 获取根节点下的一级节点
     * @author hebidu <email:346551990@qq.com>
     */
    private function getRootId($id)
    {
        $result = (new CategoryLogic())->getInfo(['id' => $id]);

        if ($result['status'] && !empty($result['info']) && is_array($result['info'])) {
            $cate_info = $result['info'];
            if ($cate_info['parent'] == 0) {
                return $cate_info['id'];
            } elseif ($cate_info['parent'] > 0) {
                $result = (new CategoryLogic())->getInfo(['id' => $cate_info['parent']]);
                if ($result['status'] && !empty($result['info']) && is_array($result['info'])) {

                    $cate_info = $result['info'];
                    if ($cate_info['parent'] == 0) {
                        return $cate_info['id'];
                    }
                }

            }
        }

        return 0;
    }

	/**
	 * 编辑
	 */
	public function edit(){
		if(IS_GET){

			$parent = $this->_param('parent',-1);
			$preparent = $this->_param('preparent',0);
			$id = $this->_param('id',0);
			$map = ['id'=>$id];
			$result = (new CategoryLogic())->getInfo($map);

            $this->assign("parent",$parent);
            $this->assign("preparent",$preparent);
            $this->assign("cate",$result['info']);
            return $this->boye_display();

		}else{
			$id = $this->_param('id',0);
			$entity = [
                'name'=>$this->_param('name'),
                'code'=>$this->_param('code'),
                'scope'=>$this->_param('scope'),
                'taxa_rate'=>$this->_param('taxrate'),
                'display_order'=>$this->_param('display_order'),
                'img_id' => $this->_param('img_id', ''),
            ];
			$result = (new CategoryLogic())->saveByID($id,$entity);
			if(!empty($result)){
				$this->success("编辑成功！",url('Admin/Category/index',['id'=>$id]));
			}else{
				$this->error($result['info']);
			}
		}
	}

	public function delete(){
		$id = $this->_param('id',0);
		$map = array('parent'=>$id);
		$result = (new CategoryLogic())->query($map);
		if($result['status']){
			if(count($result['info']['list']) > 0){
				$this->error("存在子类目，无法删除此类目！");
			}

			$result = (new CategoryLogic())->delete(array('id'=>$id));
			if($result['status']){
				$this->success("删除成功！");
			}else{
				$this->error($result['info']);
			}

		}else{
            $this->error('类目查询错误！');
		}
	}

	/**
	 * 某一类目的所有属性
	 * @author hebidu <email:346551990@qq.com>
	 */
	public function cateAllProp(){

		if (IS_AJAX) {
			$cate_id = $this->_param('cate_id', 0);
			$map = array('cate_id' => $cate_id);

			$result = (new CategoryLogic())->getInfo(['id' => $cate_id]);

			if (!$result['status'] || empty($result['info'])) {
				$this->error('cate_id参数错误!');
			}

			$cate_info = $result['info'];

			$result = (new CategoryPropLogic())->queryPropTable($map);
			if (!$result['status']) {
				$this->error($result['info']);
			}

			$prop_table = $result['info'];
			//2. 父级类目获取
			$cate_id = intval($cate_info['parent']);
			$map = array('cate_id' => $cate_id);
			if ($cate_id > 0) {
				$result = (new CategoryPropLogic())->queryPropTable($map);
				if ($result['status'] && !empty($result['info'])) {
					$prop_table = array_merge($prop_table, $result['info']);
				}
				//3. 祖父级类目获取
				$result = (new CategoryLogic())->getInfo(['id' => $cate_id]);
				if ($result['status'] && is_array($result['info'])) {
					$cate_info = $result['info'];
					$cate_id = $cate_info['parent'];
					$map = array('cate_id' => $cate_id);
					if ($cate_id > 0) {
						$result = (new CategoryPropLogic())->queryPropTable($map);
						if ($result['status'] && !empty($result['info'])) {
							$prop_table = array_merge($prop_table, $result['info']);
						}
					}
				}
			}
			$this->success($prop_table);
		}
	}
}

