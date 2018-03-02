
//下拉加载
;(function($) {
	//var canLoad = 1;
	$.fn.extend({
		//下拉加载
		dropDownLoad:function(options, callback,checkFunc) {
		    var range 		= 300;             //距下边界长度/单位px  
		    var totalheight = 0;   
		    var self 		= $(this);
		    if( self.html() == undefined ){
		    	var id = 'loadId_' + parseInt( Math.random()*1000000 );
		    	var html = '<div class="load-more" id="'+id+'" style="display:none;"><span>加载更多</span></div>';
		    	$("."+options.boxClass+":last").after( html );
		    	self = $("#"+id);
		    }
		    
		    self.attr('canLoad',1);
		    
            $(document).on("scroll",function(){

            	var checkCan = 1;
            	if( typeof(checkFunc)=='function' ){
            		checkCan = checkFunc();
            	}

            	

		        var srollPos = $(window).scrollTop();    //滚动条距顶部距离(页面超出窗口的高度)  
//		        console.log("滚动条到顶部的垂直高度: "+$(document).scrollTop());  
//		        console.log("页面的文档高度 ："+$(document).height());  
//		        console.log('浏览器的高度：'+$(window).height());  
		        totalheight = parseFloat($(window).height()) + parseFloat(srollPos);  
				var opts 	= self.createPostOpts(options);
				canLoad 	= self.attr('canLoad');
				
				//alert( canLoad +'-'+ checkCan);
				
				
		        if( ($(document).height()-range) <= totalheight && canLoad*1 && checkCan ) {
		        	self.sendLoadingPost(opts, callback);
		        }
            });
		},
		
		//点击下载
		loadingMore : function(options, callback) {
			 
			var self 	= $(this); 
			var opts 	= self.createPostOpts(options);
			
			//点击加在
			self.click(function(){
				self.sendLoadingPost(opts, callback);
			})			
		},
		
		//创建请求参数
		createPostOpts:function(options){
			// Our plugin implementation code goes here.
			/* url					请求地址
			 * boxClass				分页显示的区块（新的HTML将加在到此区块的最后一个之后）
			 * pageNum				分页每页数量
			 * more_condition_id	加载更多时需要带入的条件form表单ID
			 * 
			 * callback				加载成功后执行的回调函数
			 */
			var defualts 	= { type:'GET',pageNum:1,more_condition_id:'',loadingBoxId:'load-more' };
			var opts 		= $.extend({}, defualts, options);
			
			//
			opts.beforeHtml = $("#"+opts.loadingBoxId).find("span").html();
			
			return opts;
		},

		//发送加载请求
		sendLoadingPost:function(opts, callback){
			var self 	= $(this); 
			//canLoad 		= 0;
			self.attr('canLoad',0);
			
			var obj 		= $(this);
			var sendData 	= {};
			//当前数据量
			var listCount 	= $("."+ opts.boxClass).length;
			var pageNow 	= listCount / opts.pageNum;
                        
			var pageNext	=  Math.ceil(pageNow) + 1;
									
			//
			var sendData = '';
			if( opts.more_condition_id!='' ){
				sendData = $("#"+opts.more_condition_id).serialize(); 
			}
                        
                        
			if( sendData != '' )sendData += "&";
			sendData += 'load_more=1&p='+pageNext;
			opts.sendData = sendData;
//			console.log( listCount +'--'+ opts.url +'--'+  pageNext +'====='+ sendData );
			//console.log();
			
			$.ajax({
				type : opts.type,
				url : opts.url,
				data : opts.sendData,
				beforeSend:function(){
					//alert(66);
					$("#"+opts.loadingBoxId).find("span").html('<img alt="" src="/Public/Mobile/images/loading.gif">');
				},
				success : function(data) {
					// alert(data);
					if( trim(data) == '' ){
						// toast.info('没有更多了~', '温馨提示');
						//obj.remove();
						if(self.hasClass('parent')){
							self.parent().hide();
						}
						self.hide();
						return false;
					}
					//console.log(data);
					$("."+opts.boxClass+":last").after( data );
					
					self.attr('canLoad',1);
					
					if( typeof(callback)=='function' ){
						setTimeout(callback,100);
					}
					$("#"+opts.loadingBoxId).find("span").html( opts.beforeHtml );
				}
			})	
		}	
	})
})(jQuery); 

function trim(str){ //删除左右两端的空格
    return str.replace(/(^\s*)|(\s*$)/g, "");
}








