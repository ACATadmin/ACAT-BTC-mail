<?php

namespace Admin\Widget;
use Think\Action;

/**
 * 分类widget
 * 用于动态调用分类信息
 */

class PicWidget extends Action{
	
	public function init($picKey,$picName,$picId,$suggestSize){
		$this->assign('picKey', $picKey);
		$this->assign('picName', $picName);		
		if(strstr($picId,",")){
			$picIds = explode(',', $picId);
                        $this->assign('picIds', $picIds);
                        $this->assign('do',$picKey);
                }
//		}else{
//			$this->assign('picId', $picId);
//		}
                $this->assign('picId', $picId);
                $this->assign('suggestSize', $suggestSize);
		$this->display('../Widget/View/adminPicUpload');
	}
	
	
	/* 显示指定分类的同级分类或子分类列表 */
	public function lists($cate, $child = false){
		$field = 'id,name,pid,title,link_id';
		if($child){
			$category = D('Category')->getTree($cate, $field);
			$category = $category['_'];
		} else {
			$category = D('Category')->getSameLevel($cate, $field);
		}
		$this->assign('category', $category);
		$this->assign('current', $cate);
		$this->display('Category/lists');
	}
	
}
