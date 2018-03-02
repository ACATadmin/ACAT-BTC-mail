<?php
namespace Api\Controller;
/**
 * @author wei
 */
class VersionController extends ApiController {
	
	
	public function versionCheck(){
		$type = I('type');
		if($type)
		{
		    $map['type'] = $type;
		}
		else 
		{
		    apiError('应用类型必须',-1);
		}
		
		$info = D('AppVersion')	->where($map)->order('id desc')->field(true)->find();
		$info['AppVersionUp']=$info['appversionup'];
                $info['serverlevel']=$info['serverlevel'];
                $info['serverLevel']=$info['serverlevel'];
                $info['resourceDescription']=$info['resourcedescription'];
                $info['AppVersionUp']=$info['appversionup'];
                $info['AppVersionUp']=$info['appversionup'];
                
		if( $info ){
			
			$info['upDate'] = date('Y-m-d',strtotime($info['up_date']));
			unset($info['up_date']);
			
		}
		else 
		{
		  $info['serverLevel'] = '0';
		    $this->outputJsonData( $info );
		}
		
		if($type == 1)
		{
		    unset($info['resource_id']);
		    unset($info['resource_url']);
		    unset($info['android_type']);
		}
		else if($type == 2)
		{

		    if( $info['resource_id']&&($info['android_type'] == 1) ){
		        $fileInfo = D('File')->where( array('id'=>$info['resource_id']) )->find();
		        $info['resourceUrl'] = 'http://'.$_SERVER['SERVER_NAME'] .'/Uploads/Download/'. $fileInfo['savepath'] . $fileInfo['savename'];
		        $info['resourceSize'] = file_size_format($fileInfo['size']);
		    }
		    else if($info['android_type'] == 2)
		    {
		        $info['resourceUrl'] = $info['resource_url'];
		    }
		    unset($info['resource_id']);
		    unset($info['resource_url']);
		}
		$this->outputJsonData( $info );
	}
	
}