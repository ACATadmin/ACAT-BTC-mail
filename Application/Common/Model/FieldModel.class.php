<?php
namespace Common\Model;
class FieldModel extends BaseModel{
	
	protected $_auto = array(
		 array('create_time', 'getCreateTime', self::MODEL_INSERT,'callback'),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT, ),
	);
	
	/**
	 * 创建时间不写则取当前时间
	 * @return int 时间戳
	 * @author huajie <banhuajie@163.com>
	 */
	protected function getCreateTime(){
	    $create_time    =   I('post.create_time');
	    return $create_time?strtotime($create_time):NOW_TIME;
	}
        //格式化
	public function formatList($list){
		if(!$list) return $list;
		for ($i=0; $i <count($list) ;$i++) {
			$list[$i] = $this->format($list[$i]);
		}
		return $list;
	
	}
        
        public function format($info)
	{
            $info['create_time']=date('Y-m-d H:i:s',$info['create_time']);
            //将类型字符数组转换成id数组
            $typeArr = array('pic_ids');
            //$goodsInfo['front_img_exp'],$goodsInfo['reverse_img_exp'],$goodsInfo['decorate_img_exp'],$goodsInfo['artist_signature_img_exp']
            for($i=0;$i<count($typeArr);$i++){
                $info[ $typeArr[$i].'_exp' ] = array();
                if( $info[ $typeArr[$i] ] ){
                    $info[ $typeArr[$i].'_exp' ] = explode(',', $info[ $typeArr[$i] ]);
                }
            }
            return $info;
	}

	
	
}