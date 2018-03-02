<?php
return array(
		/*
	'random'=>array(//配置在表单中的键名 ,这个会是config[random]
		'title'=>'是否开启随机:',//表单的文字
		'type'=>'radio',		 //表单的类型：text、textarea、checkbox、radio、select等
		'options'=>array(		 //select 和radion、checkbox的子选项
			'1'=>'开启',		 //值=>文字
			'0'=>'关闭',
		),
		'value'=>'1',			 //表单的默认值
	),
	*/
		'video_type'=>array(
				'title'=>'默认播放器类型:',
				'type'=>'select',		
				'options'=>array(		 
						'0'=>'VideoJs',
				),
				'value'=>'0',			 
		),
		'video_cover'=>array(
				'title'=>'播放器封面:',
				'type'=>'select',		 
				'options'=>array(		 
						'yuanshan.png'=>'远山',
						'haiyang.png'=>'海洋',
				),
				'value'=>'0',			 
		),
		'use_zdy_video_cover'=>array(
				'title'=>'使用自定义封面:',
				'type'=>'radio',
				'options'=>array(		
					'0'=>'否',
					'1'=>'是',		 
				),
				'value'=>'0',
		),
		'zdy_video_cover'=>array(
				'title'=>'自定义播放器封面:',
				'type'=>'picture_union',		
				'value'=>'',			
		),
		'width'=>array(
				'title'=>'播放器默认宽度:',
				'type'=>'text',		 
				'value'=>'800',			 
		),
		'height'=>array(
				'title'=>'播放器默认高度:',
				'type'=>'text',		
				'value'=>'500',			
		),
		
		
		
);
					