$(function(){
    //搜索功能
    $("#search").click(function(){
    var url = $(this).attr('url');
    var query  = $('.search-form').find('input,select').serialize();
    query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g,'');
    query = query.replace(/^&/g,'');
    if( url.indexOf('?')>0 ){
        url += '&' + query;
    }else{
        url += '?' + query;
    }
    //console.log(url);
            window.location.href = url;
    });
    
    //回车搜索
    $(".search-input").keyup(function(e){
            if(e.keyCode === 13){
                    $("#search").click();
                    return false;
            }
    });
    
    //导出功能
    $("#export").click(function(){
        var url = $(this).attr('url');
        var query  = $('.search-form').find('input,select').serialize();
        query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g,'');
        query = query.replace(/^&/g,'');
        if( url.indexOf('?')>0 ){
            url += '&export=1&' + query;
        }else{
            url += '?export=1&' + query;
        }
        window.location.href = url;
    });
});

/* 上传图片预览弹出层 */
$(function(){
    $(window).resize(function(){
        var winW = $(window).width();
        var winH = $(window).height();
        
        
        
        $('.upload-img-box').delegate('img','click',function(){
        	//如果没有图片则不显示
        	if($(this).attr('src') === undefined){
        		return false;
        	}
        		
        	
            // 创建弹出框以及获取弹出图片
            var imgPopup = "<div id=\"uploadPop\" class=\"upload-img-popup\"></div>"
            var imgItem = $(this).parent(".upload-pre-item").html();

            //如果弹出层存在，则不能再弹出
            var popupLen = $(".upload-img-popup").length;
            if( popupLen < 1 ) {
                $(imgPopup).appendTo("body");
                $(".upload-img-popup").html(
                    imgItem + "<a class=\"close-pop\" href=\"javascript:;\" title=\"关闭\"></a>"
                );
            }

            // 弹出层定位
            var uploadImg = $("#uploadPop").find("img");
            var popW = uploadImg.width();
            var popH = uploadImg.height();
            var left = (winW -popW)/2;
            var top = (winH - popH)/2 + 70;
            $(".upload-img-popup").css({
                "max-width" : winW * 0.9,
                "max-height" : winH * 0.9,
                "left": left,
                "top": top
            });
        }); 
        
        
        /*
        $(".upload-img-box").click(function(){
        	//如果没有图片则不显示
        	if($(this).find('img').attr('src') === undefined){
        		return false;
        	}
            // 创建弹出框以及获取弹出图片
            var imgPopup = "<div id=\"uploadPop\" class=\"upload-img-popup\"></div>"
            var imgItem = $(this).find(".upload-pre-item").html();

            //如果弹出层存在，则不能再弹出
            var popupLen = $(".upload-img-popup").length;
            if( popupLen < 1 ) {
                $(imgPopup).appendTo("body");
                $(".upload-img-popup").html(
                    imgItem + "<a class=\"close-pop\" href=\"javascript:;\" title=\"关闭\"></a>"
                );
            }

            // 弹出层定位
            var uploadImg = $("#uploadPop").find("img");
            var popW = uploadImg.width();
            var popH = uploadImg.height();
            var left = (winW -popW)/2;
            var top = (winH - popH)/2 + 50;
            $(".upload-img-popup").css({
                "max-width" : winW * 0.9,
                "left": left,
                "top": top
            });
        });
		*/
        // 关闭弹出层
        $("body").on("click", "#uploadPop .close-pop", function(){
            $(this).parent().remove();
        });
    }).resize();

    // 缩放图片
    function resizeImg(node,isSmall){
        if(!isSmall){
            $(node).height($(node).height()*1.2);
        } else {
            $(node).height($(node).height()*0.8);
        }
    }
})

//dom加载完成后执行的js
;$(function(){
	//全选的实现
	$(".check-all").click(function(){
		$(".ids").prop("checked", this.checked);
	});
	$(".ids").click(function(){
		var option = $(".ids");
		option.each(function(i){
			if(!this.checked){
				$(".check-all").prop("checked", false);
				return false;
			}else{
				$(".check-all").prop("checked", true);
			}
		});
	});

    //ajax get请求
    $('.ajax-get').click(function(){
        var target;
        var that = this;
        if ( $(this).hasClass('confirm') ) {
            if(!confirm('确认要执行该操作吗?')){
                return false;
            }
        }
        if ( (target = $(this).attr('href')) || (target = $(this).attr('url')) ) {
            $.get(target).success(function(data){
                if (data.status==1) {
                    if (data.url) {
                        updateAlert(data.info + ' 页面即将自动跳转~','success');
                    }else{
                        updateAlert(data.info,'success');
                    }
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else if( $(that).hasClass('no-refresh')){
                        }else{
                            location.reload();
                        }
                    },1500);
                }else{
                    updateAlert(data.info,'error');
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else{
                        }
                    },1500);
                }
            });

        }
        return false;
    });

    //ajax post submit请求
    $('.ajax-post').click(function(){
        var target,query,form;
        var target_form = $(this).attr('target-form');
        var that = this;
        var nead_confirm=false;
        if( ($(this).attr('type')=='submit') || (target = $(this).attr('href')) || (target = $(this).attr('url')) ){
            form = $('.'+target_form);

            if ($(this).attr('hide-data') === 'true'){//无数据时也可以使用的功能
            	form = $('.hide-data');
            	query = form.serialize();
            }else if (form.get(0)==undefined){
            	return false;
            }else if ( form.get(0).nodeName=='FORM' ){
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                if($(this).attr('url') !== undefined){
                	target = $(this).attr('url');
                }else{
                	target = form.get(0).action;
                }
                query = form.serialize();
            }else if( form.get(0).nodeName=='INPUT' || form.get(0).nodeName=='SELECT' || form.get(0).nodeName=='TEXTAREA') {
                form.each(function(k,v){
                    if(v.type=='checkbox' && v.checked==true){
                        nead_confirm = true;
                    }
                })
                if ( nead_confirm && $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.serialize();
            }else{
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }
            $(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);
            $.post(target,query).success(function(data){
                if (data.status==1) {
                    if (data.url) {
                        updateAlert(data.info + ' 页面即将自动跳转~','success');
                    }else{
                        updateAlert(data.info ,'success');
                    }
                    setTimeout(function(){
                    	$(that).removeClass('disabled').prop('disabled',false);
                        if (data.url) {
                            location.href=data.url;
                        }else if( $(that).hasClass('no-refresh')){
                        }else{
                            location.reload();
                        }
                    },1500);
                }else{
                    updateAlert(data.info,'error');
                    setTimeout(function(){
                    	$(that).removeClass('disabled').prop('disabled',false);
                        if (data.url) {
                            location.href=data.url;
                        }else{
                        }
                    },1500);
                }
            });
        }
        return false;
    });

	/**顶部警告栏*/

    window.updateAlert = function (text,c) {
		text = text||'default';
		
		toastr.options = {
				  "closeButton": true,
				  "positionClass": "toast-top-right",
				  "onclick": null,
				  "showDuration": "1000",
				  "hideDuration": "1000",
				  "timeOut": "5000",
				  "extendedTimeOut": "1000",
				  "showEasing": "swing",
				  "hideEasing": "linear",
				  "showMethod": "fadeIn",
				  "hideMethod": "fadeOut"
				}
		
		toastr[c](text, "系统提示")
		return
	};
});
/* 上传图片预览弹出层 */

//标签页切换(无下一步)
function showTab() {
    $(".tab-nav li").click(function(){
        var self = $(this), target = self.data("tab");
        self.addClass("current").siblings(".current").removeClass("current");
        window.location.hash = "#" + target.substr(3);
        $(".tab-pane.in").removeClass("in");
        $("." + target).addClass("in");
    }).filter("[data-tab=tab" + window.location.hash.substr(1) + "]").click();
}

//标签页切换(有下一步)
function nextTab() {
     $(".tab-nav li").click(function(){
        var self = $(this), target = self.data("tab");
        self.addClass("current").siblings(".current").removeClass("current");
        window.location.hash = "#" + target.substr(3);
        $(".tab-pane.in").removeClass("in");
        $("." + target).addClass("in");
        showBtn();
    }).filter("[data-tab=tab" + window.location.hash.substr(1) + "]").click();

    $("#submit-next").click(function(){
        $(".tab-nav li.current").next().click();
        showBtn();
    });
}

// 下一步按钮切换
function showBtn() {
    var lastTabItem = $(".tab-nav li:last");
    if( lastTabItem.hasClass("current") ) {
        $("#submit").removeClass("hidden");
        $("#submit-next").addClass("hidden");
    } else {
        $("#submit").addClass("hidden");
        $("#submit-next").removeClass("hidden");
    }
}

//导航高亮
function highlight_subnav(url){
    $('.side-sub-menu').find('a[href="'+url+'"]').closest('li').addClass('current');
}



//============================图片上传插件============================//
/* 初始化上传插件 */
function initPicUpload( picKey ,type){
    $("#"+picKey+"_upload_picture").uploadify( retFrontDataObj(function(file, data){
    	uploadPicture(file, data ,picKey ,type);
    }));
}

//上传图片配置
var dataObj =  {
    "height"          : 30,
    "swf"             : "/Public/static/uploadify/uploadify.swf",
    "fileObjName"     : "download",
    "buttonText"      : "上传图片",
    "uploader"        : "{:U('File/uploadPicture',array('session_id'=>session_id()))}",
    "width"           : 120,
    'removeTimeout'	  : 1,
    'fileTypeExts'	  : '*.jpg; *.png; *.gif;',
    "onUploadSuccess" : null,
    'onFallback' : function() {
        alert('未检测到兼容版本的Flash.');
    }
};
function retFrontDataObj( UploadFunction ){
	dataObj.onUploadSuccess = UploadFunction;
	dataObj.uploader = "/index.php?s=/Admin/File/uploadPictureSquare/session_id/"+Think.SESSION_ID;
	return dataObj;
}

function retDataObj( UploadFunction ){
	dataObj.onUploadSuccess = UploadFunction;
	dataObj.uploader = "/index.php?s=/Admin/File/uploadPictureSquare/session_id/"+Think.SESSION_ID;
	return dataObj;
}
function uploadPicture(file, data, img_id ,type){
   	var data 	= $.parseJSON(data);
   	var src 	= '';
       if(data.status){
           
        if(type==1){
            $("#"+img_id).val('');
            $("#"+img_id).parent().parent().find('.upload-img-box .upload-pre-item').remove();
        }
           
       	var ids = $("#"+img_id).val();
       	var add_id = data.id;
       	if(ids){
       		var lastChar = ids.charAt(ids.length - 1);
       		if(lastChar!=','){
       			add_id = ids+','+add_id;     	
       		}
       	}
        
       	$("#"+img_id).val( add_id );

       	src = data.url || Think.ROOT + data.path;
        
        $("#"+img_id).parent().parent().find('.upload-img-box').append(
            '<tr class="template-upload fade in upload-pre-item"><td><span class="preview"><img style="width:80px;height:80px;" src="' + src + '"/></span></td><td ><a class="btn btn-warning cancel pic_del" pic_id="'+data.id+'"><i class="glyphicon glyphicon-ban-circle"></i><span>删除</span></a></td></tr>'
        );
        
       	
       } else {
       	updateAlert(data.info);
       	setTimeout(function(){
               $('#top-alert').find('button').click();
               $(that).removeClass('disabled').prop('disabled',false);
           },1500);
       }
}
//删除图片
$('.upload-img-box').delegate('.pic_del','click',function(){
	
	var pic_id 		= $(this).attr('pic_id');
	var input_img 	= $(this).parent().parent().parent().parent().parent().find('.input_img');
	var input_text 	= input_img.val();
	
	input_text = input_text.replace(pic_id+",","");
	input_text = input_text.replace(","+pic_id,"");
	input_text = input_text.replace(pic_id,"");
	input_img.val(input_text);
	
	//alert(pic_id +'==='+ input_text);return;
	
	$(this).parent().parent('.upload-pre-item').remove();
});
//==================================//