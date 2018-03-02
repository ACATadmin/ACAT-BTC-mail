$(function() { //页面加载
        var windowWidth = $(window).width(); //屏幕宽度
        var windowHeight = $(window).height(); //屏幕高度
        var documentHeight = $(document).height(); //文档高度
        $(".tdSpan").click(function(){
            var hasClass =  $(this).hasClass("active");
            if(hasClass){
               $(this).removeClass("active");
            }else{
                $(this).addClass("active");
            }
        })
        $(".AddSub").click(function(){
            var TrNuber = $(".AddTable tr").size();
            var Add_Tr = '<tr>'+
                            '<td>'+TrNuber+'</td>'+
                            '<td>'+
                                '<div class="'+'InputBox">'+
                                '<input type="'+'text" />'+
                                '</div>'+
                            '</td>'+
                            '<td>'+
                                '<div class="'+'selectBox">'+
                                '<select>'+
                                '<option>自有</option>'+
                                '<option>租赁</option>'+
                                '</select>'+
                                '</div>'+
                            '</td>'+
                            '<td>'+
                                '<div class="'+'selectBox">'+
                                '<select>'+
                                '<option>经营</option>'+
                                '<option>居住</option>'+
                                '</select>'+
                                '</div>'+
                            '</td>'+
                         '</tr>'
    //                  console.log(Add_Tr)
            $(".AddTable tr:last").after(Add_Tr);
        })
    }) //页面加载闭合

