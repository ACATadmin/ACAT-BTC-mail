//省市联动
function selRegionInit(countrie_id,province_id,city_id,area_id){
	
	//alert(countrie_id +'---'+ province_id +'---'+ city_id +'---'+ area_id);
	
	//下拉框初始化
	selRegion($("#selCountries"),0,0,countrie_id);
	selRegion($("#selProvinces"),$("#selCountries").val(),1,province_id);
	selRegion($("#selCities"),$("#selProvinces").val(),2,city_id);
	
	//alert( $("#selCities").val() );
	
	selRegion($("#selArea"),$("#selCities").val(),3,area_id);
	
	//改变国家
	$("#selCountries").change(function(){
		selRegion($("#selProvinces"),$("#selCountries").val(),1);
		selRegion($("#selCities"),$("#selProvinces").val(),2);
		selRegion($("#selArea"),$("#selCities").val(),3);
	})
	//改变省份
	$("#selProvinces").change(function(){
		selRegion($("#selCities"),$("#selProvinces").val(),2);
		selRegion($("#selArea"),$("#selCities").val(),3);			//同步更改地区
	})
	//改变市
	$("#selCities").change(function(){
		
		selRegion($("#selArea"),$("#selCities").val(),3);
	})
}
function selRegion(obj,parent_id,region_type,sel_id){
	$.ajax({
		url:"/index.php?s=/Api/Basedata/selRegion",
		data:{ parent_id: parent_id,region_type:region_type },
		async:false,
		success: function(data){
			
			//alert( parent_id +'----'+ region_type +'----'+ data +'----'+ sel_id );
			
			obj.html(data);
			if(sel_id){
				
				//判断当前元素列表中是否存在需要的元素，有则选中，无则选为0

				//选中默认值
				obj.val(sel_id);
			}
		}
	});
}