$(function() { //页面加载
        var windowWidth = $(window).width()
        var windowHeight = $(window).height();
        var documentHeight = $(document).height();

        /*************** 鼠标经过显示详情*********************/

        $(".user_right_js").click(function() {
            $(this).children(".user_right_down").slideToggle();

        });

        $(".page-content").css("min-height",windowHeight-45);
        $("#sidebar-collapse").click(function(){
            $("#sidebar").toggleClass("menu-min");
        });
    })
        
        /*****************tab 切换***********************/
      