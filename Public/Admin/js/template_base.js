//$("#gjj_com_scale").change(function(){
$("body").on('change','#gjj_com_scale',function(){	
	var id = $(this).val();
	var name = 'company';
	if(id == 1){//固定	
		var guding = gjj_scale_guding;
		guding = guding.replace("named" ,name);
		guding = guding.replace("named" ,name);
		$(".gjj_com").html(guding);

	}else{
		var fanwei = gjj_scale_fanwei;
		fanwei = fanwei.replace("named" ,name);
		fanwei = fanwei.replace("named" ,name);
		$(".gjj_com").html(fanwei);
	}
});
//$("#gjj_person_scale").change(function(){
$("body").on('change','#gjj_person_scale',function(){	
	var id = $(this).val();
	var name = 'person';
	if(id == 1){//固定	
		var guding = gjj_scale_guding;
		guding = guding.replace("named" ,name);
		guding = guding.replace("named" ,name);
		$(".gjj_person").html(guding);

	}else{
		var fanwei = gjj_scale_fanwei;
		fanwei = fanwei.replace("named" ,name);
		fanwei = fanwei.replace("named" ,name);
		$(".gjj_person").html(fanwei);
	}
});

// $("body").on('click','#template_sub_add', function(){	
// 	var cate = template_sub;
// 	$("#table_tem").append(cate);
// 	$(".delete").click(function(){
// 		layer.msg('已删除', {icon: 6, time:2000}); 
// 		 $(this).parent("td").parent("tr").remove();	
// 	});	
// });
//社保分类
var template = '<form name="form1" id="form1" method="post" action="/admin.php?s=/ProductTemplateClassify-add.html" ><div>';
// template += '<input type="hidden" name="type" class="text input-mid" id="type_layer" value="">';
template += '<input type="hidden" name="location" class="text input-mid" id="location_layer" value="">';
template += '<input type="hidden" name="soc_payment_type" class="text input-mid" id="soc_payment_type_layer" value="">';
template += '<input type="hidden" name="soc_deadline" class="text input-mid" id="soc_deadline_layer" value="">';
template += '<input type="hidden" name="soc_payment_month" class="text input-mid" id="soc_payment_month_layer" value="">';
// template += '<input type="hidden" name="location" class="text input-mid" id="location_layer" value="">';
template += '<input type="hidden" name="pro_payment_type" class="text input-mid" id="pro_payment_type_layer" value="">';
template += '<input type="hidden" name="pro_deadline" class="text input-mid" id="pro_deadline_layer" value="">';
template += '<input type="hidden" name="pro_payment_month" class="text input-mid" id="pro_payment_month_layer" value="">';
template += '<div class="form-item">';	
template += '	<div class="table-striped" style="width: 100%px; overflow: hidden; height: auto;">';
template += '<table id="table_tem">';
template += '  <tr>';
template += '   <td width="20">&nbsp;</td>';
template += '<td width="80" align="left">分类名称</td>';
template += '    <td width="50"> <input type="text" name="category" class="text input-small" value="户籍性质" readonly="readonly"></td>';
template += '    <td width="20">&nbsp;</td>';
template += '    <td width="65"><!--<div class="btn  hidden fl delete" >删除</div>--></td>';
template += '   <td >&nbsp;</td>';
template += '  </tr>';	
template += '  <tr style="height: 5px; line-height: 5px">';
template += '   <td width="20" height="5" >&nbsp;</td>';
template += '    <td width="80"  align="left"style=" font-weight: bold;">分类选项</td>';
template += '    <td width="50"></td>';
template += '    <td width="20">&nbsp;</td>';
template += '   <td width=""></td>';
template += '    <td >&nbsp;</td>';
template += '  </tr>';
template += '    <tr>';
template += '   <td width="20">&nbsp;</td>';
template += '   <td colspan="2" align="left"><input type="text" name="category_sub[]" class="text input-small" value=""></td>';
template += '    <td width=""colspan="3"><div class="btn  hidden fl delete" data="1" >删除</div></td>';
template += '   <td >&nbsp;</td>';
template += '  </tr>';
template += '</table>';
template += '<div class="form-item"><div class="btn  hidden fl" id="template_sub_add" style="margin-left:20px" >增加条数</div></div>';
template += '</div></div>';
template += '</div></form>';

var template_sub = '';
template_sub += '    <tr>';
template_sub += '   <td width="20">&nbsp;</td>';
template_sub += '   <td colspan="2" align="left"><input type="text" name="category_sub[]" class="text input-small" value=""></td>';
template_sub += '    <td width=""colspan="3"><div class="btn  hidden fl delete" >删除</div></td>';
//template_sub += '   <td >&nbsp;</td>';
template_sub += '  </tr>';



//公积金比例
var gjj_scale_fanwei= '';
gjj_scale_fanwei  += '<table width="100%" style="background-color: transparent;">';
gjj_scale_fanwei  += '<tr><td width="10"></td>';
gjj_scale_fanwei  += '<td width="50"><input type="text" name="gjj[named][]" class="text input-mid" value=""  onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"  maxlength="10"></td>';
gjj_scale_fanwei  += '<td width="20"  align="center">~</td>';
gjj_scale_fanwei += '<td width="50"><input type="text" name="gjj[named][]" class="text input-mid" value=""  onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"  maxlength="10"></td><td></td>';
gjj_scale_fanwei  += '</tr>';
gjj_scale_fanwei += '</table>';
var gjj_scale_guding = '';
gjj_scale_guding += '<table width="100%" style="background-color: transparent;">';
gjj_scale_guding  += '<tr><td width="10"></td>';
gjj_scale_guding  += '<td width="150" ><input type="text" name="gjj[named]" class="text input-mid" value=""  onKeyUp="value=value.replace(/[^\\d\\,]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="20"></td><td></td>';
gjj_scale_guding  += '<td  width="20"  align="center">% </td>';
gjj_scale_guding  += '<td width="">如是多个固定值，请用英文逗号隔开</td>';    	
gjj_scale_guding  += '</tr>';
gjj_scale_guding += '</table>';


//other
var sb_other = '';
sb_other +=  '<tr>';
sb_other +=  '<td width="20">&nbsp;</td>';
sb_other +=  '<td width="60">费用名称</td>';
sb_other +=  '<td width="60"><input type="text" name="sb_other[name][]" class="text input-small" value=""  ></td>';
sb_other +=  '<td width="60" align="right">单位金额</td>';
sb_other +=  '<td width="50"><input type="text" name="sb_other[company][]" class="text input-x " value=""  onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"  maxlength="10"></td>';
sb_other +=  '<td width="20">元</td>';
sb_other +=  '<td width="80">个人金额</td>';
sb_other +=  '<td width="50"><input type="text" name="sb_other[person][]" class="text input-x " value=""  onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"  maxlength="10"></td>';
sb_other +=  '<td width="20" >元</td>';
sb_other +=  '<td >&nbsp;</td>';
sb_other +=  '<td><div class="btn  hidden fl sb_other_delete" >删除</div></td>';
sb_other +=  '</tr>';

var gjj_other = '';
gjj_other +=  '<tr>';
gjj_other +=  '<td width="20">&nbsp;</td>';
gjj_other +=  '<td width="60">费用名称</td>';
gjj_other +=  '<td width="60"><input type="text" name="gjj_other[name][]" class="text input-small" value=""  ></td>';
gjj_other +=  '<td width="60" align="right">单位金额</td>';
gjj_other +=  '<td width="50"><input type="text" name="gjj_other[company][]" class="text input-x " value=""  onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"  maxlength="10"></td>';
gjj_other +=  '<td width="20">元</td>';
gjj_other +=  '<td width="80">个人金额</td>';
gjj_other +=  '<td width="50"><input type="text" name="gjj_other[person][]" class="text input-x " value=""  onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"  maxlength="10"></td>';
gjj_other +=  '<td width="20" >元</td>';
gjj_other +=  '<td >&nbsp;</td>';
gjj_other +=  '<td><div class="btn  hidden fl gjj_other_delete" >删除</div></td>';
gjj_other +=  '</tr>';
//社保其他添加
$("#sb_other_add").click(function(){
	$("#sb_other_data").append(sb_other);
	$(".sb_other_delete").click(function(){
		$(this).each(function(i){
			$(this).parent("td").parent("tr").remove();
		});
	})
});

//公积金其他添加
$("#gjj_other_add").click(function(){
	$("#gjj_other_data").append(gjj_other);
	$(".gjj_other_delete").click(function(){
		$(this).each(function(i){
			$(this).parent("td").parent("tr").remove();
		});
	})
});

//城市联动
$("#location").change(function(){
	var location = $(this).val();
	var h = '<option value="">-请选择-</option>';
	var direct = ['11000000','12000000','10000000','13000000'];
	var index = $.inArray(location, direct);
	if(index!==-1)
		var data = {code:location,path:3};
	else
		var data = {code:location};
	if(location){
		$.getJSON('admin.php?s=/ProductTemplate-select_area.html',data,function(data){			
			if(data.status != 0){	
				$.each(data, function(k, v){
					h += '<option value=\"'+v.id+'\">'+v.name+'</option>';
				})
				
				$("#city_name").show();
				$("#city_name").html(h);
				var name = index ===-1 ? 'location2' : 'location';
				$("#city_name").attr('name', name);
				// $("#location").attr('name', 'location1');
			}else{				
				//$("#location").attr('name', 'location');
				$("#city_name").hide();
				$("#city_name").attr('name', 'location2');

			}
			$('#region').hide();
		});

	}
})
$('body').on('change','#city_name',function(){
	var location = $(this).val();
	var direct = ['11000000','12000000','10000000','13000000'];
	var before_location = $('#location').val();
	var index = $.inArray(before_location, direct);
	if(index!==-1)
		return false;
	var h = '<option value="">-请选择-</option>';
	if(location){
		$.getJSON('admin.php?s=/ProductTemplate-select_area.html',{'code':location,path:3},function(data){			
			if(data.status != 0){	
				$.each(data, function(k, v){
					h += '<option value=\"'+v.id+'\">'+v.name+'</option>';
				})
				$("#region").show();
				/*$("#region").attr('name','location');
				$('#city_name').attr('name','location2');*/
				$("#region").html(h);
			}else{				
				/*$("#region").attr('name','location2');
				$('#city_name').attr('name','location');*/
				$("#region").hide();
			}
		});

	}
});
//险种
var baoxian = '';
baoxian +='<tr>';
baoxian+= '<td width="60" align="center"><input type="checkbox" name="bujiao[]" value="0" class="bujiao"></td>';
baoxian +='<td width="100" align="center"><input type="input" name="sb[items][]" value="" class="text"/ style=" width:90px"></td>';		 
baoxian+= '<td width="80" align="right">单位比例</td>';
baoxian+= '<td width="50"><input type="text" name="sb[company][]" class="text input-x " value=""onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"  maxlength="10"></td>';
baoxian+= '<td width="20"  align="ceneter">%</td>';
baoxian+= '<td width="20">+</td>';
baoxian+= '<td width="50"><input type="text" name="sb[company][]" class="text input-x " value=""onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"  maxlength="10"></td>';
baoxian+= '<td width="20">元</td><td width="80" align="right">个人比例</td>';
baoxian+= '<td width="50"><input type="text" name="sb[person][]" class="text input-x " value=""onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"  maxlength="10"></td>';
baoxian +='<td width="20"  align="ceneter">%</td>';
baoxian+= '<td width="20">+</td>';
baoxian +='<td width="50"><input type="text" name="sb[person][]" class="text input-x " value=""onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"  maxlength="10"></td>';
baoxian +='<td width="" colspan="2">元</td>	';
baoxian+='<td width="80"  align="right">基数</td> ';
baoxian+='<td width="50"><input type="text" name="sb[amount][]" class="text input-x " value=""  onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"  maxlength="10"></td>';
baoxian+= '<td width="" colspan="2">元</td> <td><div class="btn  hidden  baoxian_delete" style="width:40px;margin-left:20px;" >删除</div></td>';		  
baoxian +='</tr>';

$(".create_classify").click(function(){
	$(this).each(function(i){
			
	  var  name = '社保分类', type , str = 'sb';
	  type = $(this).attr('data-type');
	  //console.log(type);
	  if(type == 2){name = '公积金分类'; str = 'gjj' }
		layer.open({
		    type: 1,
		    skin: 'layui-layer-demo', //样式类名
		    closeBtn: 1, //关闭按钮
		    shift: 2,
		    shadeClose: true, //开启遮罩关闭
		    content: template,
		    btn: ['提交', '关闭'],
		    title : '创建'+name,
		    area : ['320px', '500px'],
		    scrollbar: false,
		    yes : function(index, layero){
		    	var location = $(":input[name='location']").val();//$('.controls_new').children('select').find("[name='location']").val();//= $("#location").val();
		    	var sb_payment_type = $('#soc_payment_type').val();
		    	var sb_deadline = $('#soc_deadline').val();
		    	var sb_payment_month = $('#soc_payment_month').val();
		    	var gjj_payment_type = $('#pro_payment_type').val();
		    	var gjj_deadline = $('#pro_deadline').val();
		    	//var gjj_payment_month = $('#pro_payment_month').val();
		    	var gjj_payment_month = $('#soc_payment_month').val();
		    	$('#soc_payment_type_layer').val(sb_payment_type);
		    	$('#soc_deadline_layer').val(sb_deadline);
		    	$('#soc_payment_month_layer').val(sb_payment_month);
		    	$('#pro_payment_type_layer').val(gjj_payment_type);
		    	$('#pro_deadline_layer').val(gjj_deadline);
		    	$('#pro_payment_month_layer').val(gjj_payment_month);
		    	$('#location_layer').val(location);
		    	data = $("#form1").serialize();
		    	$.post('/admin.php?s=/ProductTemplateClassify-add.html', data, function(data){		    	
		    		if(data.status != 0){
		    			var html='';
		    			html += '<div id="category_'+data.id+'" class="fl" style="overflow: hidden; width: auto">';
						html += '	<label class="item-label_1">'+data.name+'<span class="check-tips"></span></label>';
						html += '	<div class="controls_new fl">';
						html += '		<select name="'+str+'_category_sub[]"class="input-small category_sub" data-type="'+type+'">';
						html += '		<option value="0"  >请选择</option>';	
						$(data.category_sub).each(function(key, val){
							html += '<option value="'+val.id+'"  >'+val.name+'</option>';
						})			
						html += '		</select>';				
						html += '	</div>';
						html += '	<div class="fl"><div class="btn  hidden fl sb_rule_edit" data-type="'+data.id+'" types="'+type+'" template_id="'+data.template_id+'" >修改</div></div></div>';
		    			
		    			$("#category_list_"+type).append(html);
			    		$('#create_sb_classify').remove();
					layer.msg('添加成功！', {icon: 6, time: 1000});				
				     	layer.close(index);	    			
		    		}else if(data.status == 0){
		    			layer.msg(data.info, {icon: 6, time: 1000});
		    		}
		    			
		    	});
		    	//console.log(b);
		    },
		})
//删除
	$("#template_sub_add").click(function(){	
		var cate = template_sub;
		$("#table_tem").append(cate);
		$(".delete").click(function(){
			layer.msg('已删除', {icon: 6, time:1000}); 
			 $(this).parent("td").parent("tr").remove();	
		});	
	});

	})
	
});


//$("#add_baoxian").click(function(){
$("body").on('click','#add_baoxian',function(){	
	var b = baoxian;
 	$("#baoxian").append(baoxian);
 	var len = $('.bujiao').length;
 	$('.bujiao').eq(len-1).val(len-1);
 	$(".baoxian_delete").click(function(){
 		$(this).parent("td").parent("tr").remove();		
 	})
})

////社保分类修改
$("body").on('click','.sb_rule_edit',function(){
	var fid = $(this).attr('data-type');
	var template_id = $(this).attr('template_id');
	$.get('/admin.php?s=/ProductTemplateClassify-modifyClassify.html',{fid:fid,template_id:template_id},function(msg){
		layer.open({
			type: 1,
			skin: 'layui-layer-demo', //样式类名
			closeBtn: 1, //关闭按钮
			shift: 2,
			area: 'auto',
			shadeClose: true, //开启遮罩关闭
			content: msg,
			btn: ['提交', '关闭'],
			title : '修改分类',
			scrollbar: false,
			yes : function(classify){ 
				var data = $('#modifyClassify').serialize();
				$.post('/admin.php?s=/ProductTemplateClassify-modifyClassify.html',data,function(msg){
					if(msg.status==0)
					{
						var option_html = '';
						$.each(msg.msg.category_sub,function(index,val){
							option_html += '<option value="'+val.id+'"  >'+val.name+'</option>';
						});
						$('.category_sub').html(option_html);
						layer.msg('修改成功!',{icon:1,time:1000});
						layer.close(classify);
					}else{
						layer.msg(msg.msg,{icon:5,time:1000});
					}
				},'json');
			},
		});
	},'html');
});
