var UINestable = function () {
	"use strict";
    //function to initiate jquery.nestable
    var updateOutput = function (e) {
        var list = e.length ? e : $(e.target),
            output = $('#nestable-output');
        if (window.JSON) {
            output.text(window.JSON.stringify(list.nestable('serialize')));
            updateVal();
            //, null, 2));
        } else {
            output.text('JSON browser support required for this demo.');
        }
    };
 // 更新排序后的隐藏域的值
    function updateVal() {
    	var sortVal = [];
    	$('#sortUl li').each(function(){
    		sortVal.push($('em',this).text());
    	});
        $("input[name='addons']").val(sortVal.join(','));
    }
    var runNestable = function () {
        // activate Nestable for list 1
        $('#nestable').nestable({
            group: 1
        }).on('change', updateOutput);
        // activate Nestable for list 2
        $('#nestable2').nestable({
            group: 1
        }).on('change', updateOutput);
        // output initial serialised data
        updateOutput($('#nestable').data('output', $('#nestable-output')));
        updateOutput($('#nestable2').data('output', $('#nestable2-output')));
        $('#nestable-menu').on('click', function (e) {
            var target = $(e.target),
                action = target.data('action');
            if (action === 'expand-all') {
                $('.dd').nestable('expandAll');
            }
            if (action === 'collapse-all') {
                $('.dd').nestable('collapseAll');
            }
        });
        $('#nestable3').nestable();
    };
    return {
        //main function to initiate template pages
        init: function () {
            runNestable();
        }
    };
}();