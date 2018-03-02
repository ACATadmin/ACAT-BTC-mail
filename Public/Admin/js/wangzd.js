$(".index_box01_table tr").hover(function(){
    $(this).toggleClass("active");
})
$(document).on("click",".radio_i", function() {
        var parent_div=$(this).parents(".index_box01_table");
        var parent_div02=$(this).parent();
        parent_div.find(".radio_i").removeClass("on");
        parent_div.find(".light").removeClass('active');
        parent_div.find(".light").removeClass('light');
        $( this).addClass("on");
        parent_div02.parent().addClass("light");
    })


function getRadioCheckedObj(){
    var obj_radio = $('.radio_i.on');

    if( obj_radio.length!=1 ){
        alert('请选择操作对象');
        return false;
    }

//    var obj_id = obj_radio.find('[name="id"]').val();
    var obj_id = obj_radio.find('.primary_field').val();
    if( !obj_id ){
        alert('请选择操作对象');
        return false;
    }else{
        return obj_id;
    }
}

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

        window.location.href = url;
    });

    //重置搜索
    $('#resetSearch').click(function(){
        var obj_form = $('.search-form');

        obj_form.find('input').val('');
        obj_form.find('select').val(0);

    });


    //每页显示数
    $('.pageLeft select').change(function(){
        var rows = $(this).val();
        var rows_arr = {rows:rows};
        var query = $.param(rows_arr);

        var url = window.location.href;

        url = url.replace(/([\/])(rows)([\/])([0-9]*)/gi,'');
        url = url.replace(/(&|)(rows=)([0-9]*)/gi,'');


        if( url.indexOf('?')>0 ){
            url += '&' + query;
        }else{
            url += '?' + query;
        }

        window.location.href = url;
    });
    //

});
