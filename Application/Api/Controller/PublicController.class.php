<?php
namespace Api\Controller;
use User\Api\UserApi;

/**
 * 公共接口
 */
class PublicController extends ApiController {
	
	public function getSortList(){

		$res = D('Sort')->getList(array('sell_type_id'=>I('sell_type_id')));

		$this->outputJsonData($res);
	}

	public function tests(){

		$s= $this->https_request('http://app.dspmore.com/index.php?s=/Api/Public/callAuth',array('fromSerNum'=>1));
		vde($s);
	}

	public function https_request($url, $data = null)
	{
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    if (!empty($data)){
	        curl_setopt($curl, CURLOPT_POST, 1);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    }
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($curl);
	    curl_close($curl);
	    return $output;
	}
	/*获取版本号，并返回配置信息
	*
	*id 当前版本号

	*/
	public function getVersion($id){
		$this->check_parameter($id,40015);
		$Version = M('Version');
		$info = M('Version')->where('id='.$id)->find();

		if($info){
			$this->outputJsonData((array)$res);
		}else{
			$info = $Version->find();
			$res['version'] = $info['id'];
			
			$ProjectAttribute  =  D('ProjectAttribute');
			
			$ret['kind'] =$ProjectAttribute->order('sort_order desc')->group('sort_id')->getList(array('sell_type_id'=>0));
			
			$ret['payment'] =$ProjectAttribute->order('sort_order desc')->group('sort_id')->getList(array('sell_type_id'=>1));
			foreach ($ret['kind'] as $key => $value) {
				$ret['kind'][$key]['list'] = $ProjectAttribute->order('min_val asc')->where(array('sort_id'=>$value['sort_id']))->select();
			}
			foreach ($ret['payment'] as $key => $value) {
				$ret['payment'][$key]['list'] = $ProjectAttribute->order('min_val asc')->where(array('sort_id'=>$value['sort_id']))->select();
			}
			$ret['craft'] = D('Sort')->field('title,id')->where(array('sell_type_id'=>2))->select();


			$ret['charge_mode'] =array_values( C('charge_mode'));

			$ret['labour_type'] =array_values( C('labour_type'));


			$ret['plan_pay'] = array_values(C('plan_pay'));

			$ret['guarantee_period'] = array_values(C('guarantee_period'));

			$ret['guarantee_price'] =array_values( C('guarantee_price'));

			$list  = D('Sort')->field('title,id')->where(array('sell_type_id'=>0))->select();
			foreach ($list as $key => $value) {
				$list[$key]['name'] = $value['title'];
			}

			$ret['sortlist'] = $list;

			$res['dataset'] = $ret;

			$this->outputJsonData((array)$res);
		}
	}
	/*正式*/
	public function getVersion2($id){
		$this->check_parameter($id,40015);
		$Version = M('Version');
		$info = M('Version')->where('id='.$id)->find();

		if($info){
			$this->outputJsonData((array)$res);
		}else{
			$info = $Version->find();
			$res['version'] = $info['id'];
			
			$ProjectAttribute  =  D('ProjectAttribute');
			
			$ret['kind'] =C('construct_project');
			
			// $ret['payment'] =$ProjectAttribute->order('sort_order desc')->group('sort_id')->getList(array('sell_type_id'=>1));
			$ret['payment'] = $list =$sort_list = D('Sort')->order('sort asc')->field('id,title,short_name')->where(array('sell_type_id'=>0))->select();
			foreach ($ret['kind'] as $key => $value) {
				$ret['kind'][$key]['sort_title'] = $value['name'];
				foreach ($list as $k => $v) {
					$list[$k]['id'] = $v['id'];
					$list[$k]['name'] = $v['title'];
					if($value['id']==2){
						$lists = D('ProjectAttribute')->field('id,name')->where(array('sell_type_id'=>99,'sort_id'=>0,'construct_project'=>$value['id']))->select();
					}else{
						$lists = D('ProjectAttribute')->field('id,name')->where(array('sell_type_id'=>0,'sort_id'=>$v['id'],'construct_project'=>$value['id']))->select();
					}
					$list[$k]['list'] = $lists;
				}
				$ret['kind'][$key]['list'] = $list;
			}
			$ret['kind'] = array_values($ret['kind']);
			
			$ret['craft'] = $craft = D('Sort')->field('title,id,type')->order('sort asc')->where(array('sell_type_id'=>2))->select();

			foreach ($ret['craft'] as $key => $value) {
				if($value['id']==19){
					$ret['craft'][$key]['grade'] = array(
            			array('id'=>99,'name'=>'搬运、打灰、成品保护等')
            		);
				}else{
					$ret['craft'][$key]['grade'] = array(
						array('id'=>3,'name'=>'初级'),
						array('id'=>2,'name'=>'中级'),
			            array('id'=>1,'name'=>'高级'),
					);
				}
				# code...
			}
			$ret['charge_mode'] =array_values( C('charge_mode'));

			$ret['labour_type'] =array_values( C('labour_type'));


			$ret['plan_pay'] = array_values(C('plan_pay'));

			$ret['guarantee_period'] = array_values(C('guarantee_period'));

			$ret['guarantee_price'] =array_values( C('guarantee_price'));

			$ret['company_intelligence'] =transformToArr('COMPANY_INTELLIGENCE');
			$ret['education'] =transformToArr('EDUCATION');
			$ret['adv_type'] =transformToArr('ADV_TYPE');
			$ret['authenticate'] =transformToArr('AUTHENTICATE');
			
			$ret['onjob_type'] =array_values( C('onjob_type'));
			// vde(C('COMPANY_INTELLIGENCE'));
			// $list  = D('Sort')->field('title,id')->order('sort asc')->where(array('sell_type_id'=>0))->select();
			foreach ($sort_list as $key => $value) {
				$sort_list[$key]['name'] = $value['title'];
			}

			$ret['sortlist'] = $sort_list;

			$ret['craft_map'] = $craft;
			foreach ($ret['craft_map'] as $key => $value) {
				$ret['craft_map'][$key]['name'] = $value['title'];
			}

			$res['dataset'] = $ret;

			
			$map = array();
			$map['region_type'] =   1;
			$work_area = D('Region')->getRegion( $map );
			$res['dataset']['work_area'] = $work_area;
			
			$this->outputJsonData((array)$res);
		}
	}

	/*
	*获取劳务或者工程商评分
	*uid 用户id
	*/

	public function getUserGrade($uid){
		$this->check_parameter($uid,40051);

		$listField = 'is_approve,is_skill_approve,member_contacts,company_name,nickname,synthesize_score,uid,member_type,quality_score,plan_score,degree_score,manage_score,labour_score,good_reputation,med_reputation,bad_reputation,(good_reputation+med_reputation+bad_reputation) as allnum';
		$map = array('uid'=>$uid);
		$info = M('Member')->field($listField)->where( $map )->find();
		$info = D('Member')->calprojectapplynumber($info);
		$info['member_type_path'] = getCname('member_type',$info['member_type']);
		

        if($info['member_type']==1||$info['member_type']==5){
            $info['nickname'] = $info['company_name'];
        }else{
            $info['nickname'] = $info['member_contacts'];
        }
		//好评 中评 差评
		$list = M('GradeNorm')->select();
		foreach ($list as $key => $v) {
			if($v['type']==1){
				$info['quality_score_exp'] = compare($info['quality_score'],$v['score']);
			}else if($v['type']==2){
				$info['plan_score_exp'] = compare($info['plan_score'],$v['score']);
			}else if($v['type']==3){
				$info['degree_score_exp'] = compare($info['degree_score'],$v['score']);
			}else if($v['type']==4){
				$info['manage_score_exp'] = compare($info['manage_score'],$v['score']);
			}else if($v['type']==5){
				$info['labour_score_exp'] = compare($info['labour_score'],$v['score']);
			}
		}
		$this->outputJsonData($info);
	}

	/*获取客服电话*/
	public function getServiceTel(){
//		if(C('ServiceTel')){
//			$res = C('ServiceTel');
//		}else{
//			$res = '400-001-001';
//		}
                $res=D('AboutUs')->where('id=1')->getField('tel');
		$this->outputJsonData($res);
	}

	/*生成二维码*/
	public function createQrcode($uid=''){
		$this->check_parameter($uid,40051);
		$res = createQrcode($uid);

		$this->apiDoNotice($res,'生成二维码');
	}
        
        public function getAboutUs($type){
            if($type==1){
                $res['url']='http://'.$_SERVER['HTTP_HOST'].'/Api/H5/about_us_shop';
                $res['tel']=D('AboutUs')->where('id=1')->getField('tel');
            }else{
                $res['url']='http://'.$_SERVER['HTTP_HOST'].'/Api/H5/about_us_charity';
                $res['tel']=D('AboutUs')->where('id=2')->getField('tel');
            }
            $this->outputJsonData($res);
        }

        //获取协议
        public function getAgreement($type){
            $url= 'http://'.$_SERVER['HTTP_HOST'].'/Api/H5/getAgreement/type/'.$type;
            $this->outputJsonData($url);
        }
        
        
        //获取平台分类列表
        public function getPlatformCategoryList($keyword=''){
            
            if($keyword){
                $map['cate_name'] = array('like','%'.$keyword.'%');
            }
            
            $map['parent_id']=0;
            $map['status']=1;
            $pc=D('PlatformCategory');
            $cate_list=$pc->where( $map )->order('sort asc,id asc')->select();
            
            if($cate_list){
                $cate_list=$pc->formatList($cate_list);
            }else{
                $cate_list=array();
            }
            
            $this->outputJsonData($cate_list);
        }
        
        //记录观看记录
        public function addViewLog($uid='',$aim_type,$aim_id){
//            $this->check_parameter($uid,40051);
            $this->check_parameter($aim_type,46001);
            $this->check_parameter($aim_id,46002);
            
            $ret = D('ViewLog')->createLog($uid,$aim_type,$aim_id);
            
            $this->apiDoNotice($ret,'新增');
        }
        
        //退出观看记录
        public function outWatching($aim_type,$aim_id){
            $this->check_parameter($aim_type,46001);
            $this->check_parameter($aim_id,46002);
            
            $ret = D('ViewLog')->outWatching($aim_type,$aim_id);
            
            $this->apiDoNotice($ret,'操作');
        }
        
        
        //获取轮播图
        public function getSlideList($plate_type){
            $this->check_parameter($plate_type,46008);
            
            $res = D('Slide')->getObjectSelectList(1,1,$plate_type);
            $res = $res?$res:array();
            
            $this->outputJsonData($res);
        }
        
        
        //获取对象付费价格
        public function getAimPayPrice($aim_type,$aim_id){
            switch($aim_type){
                case 1:
                    $model = D('Lesson');
                    $info = $model->find($aim_id);
                    if($info['lesson_type']==1){
                        $price = 0;
                    }else{
                        $price = $info['pay_price'];
                    }
                    break;
                case 2:
                    $model = D('Member');
                    $info = $model->find($aim_id);
                    if($info['ask_type']==2){
                        $price = 0;
                    }else{
                        $price = $info['ask_price'];
                    }
                    break;
            }
            
            
            $this->outputJsonData(array(
                'price' => $price
            ));
        }
        
        
        //获取广告页图片
        public function getStartPageList(){
            $sp = D('StartPage');
            
            $res = $sp->where( array('status'=>1) )->order('sort asc,id asc')->select();
            $res = $sp->formatList($res);
            
            $this->outputJsonData($res);
        }
        
        
}