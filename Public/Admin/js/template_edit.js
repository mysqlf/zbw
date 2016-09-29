// ////社保分类修改
// $("body").on('click','.sb_rule_edit',function(){
// 	$(this).each(function(i){
// 		var id = $(this).attr('data-type');
// 		var  name = '社保分类' ,type, str = 'sb';
// 		type = $(this).attr('types');
// 		//console.log(type);
// 		if(type == 2){name = '公积金分类'; str = 'gjj'
// 		 }		
// 	//	var category	 = $(this).parents('div').siblings('label').html();
// 		$.post('/admin.php?s=/ProductTemplateClassify-get_classify_fid.html', {'fid':id,'type': type}, function(data){			

// 			if(data.status != 0){					
// 				var template = '<form name="form1" id="form1" method="post" action="" ><div>';
// 				template += '<input type="hidden" name="location" class="text input-mid" id="location_layer" value="">';
// 				template += '<input type="hidden" name="template_id" class="text input-mid" id="template_id_layer" value="">';
// 				template += '<input type="hidden" name="type" class="text input-mid" id="type_layer" value="">';
// 				template += '<div class="form-item">';	
// 				template += '	<div class="table-striped" style="width: 100%px; overflow: hidden; height: auto;">';
// 				template += '<table id="table_tem">';
// 				template += '  <tr>';
// 				template += '   <td width="20">&nbsp;</td>';
// 				template += '<td width="80" align="left">分类名称</td>';
// 				template += '    <td width="50"> <input type="text" name="category" class="text input-small" value="'+data.name+'"></td>';
// 				template += '    <td width="20">&nbsp;</td>';
// 				template += '    <td width=""><div class="btn  hidden fl edelete" data-type="'+data.id+'" >删除</div></td>';
// 				template += '   <td >&nbsp;</td>';
// 				template += '  </tr>';	
// 				template += '  <tr style="height: 5px; line-height: 5px">';
// 				template += '   <td width="20" height="5" >&nbsp;</td>';
// 				template += '    <td width="80"  align="left"style=" font-weight: bold;">分类选项</td>';
// 				template += '    <td width="50"></td>';
// 				template += '    <td width="20">&nbsp;</td>';
// 				template += '   <td width=""></td>';
// 				template += '    <td >&nbsp;</td>';
// 				template += '  </tr>';
// 				$(data.category_sub).each(function(key, val){
// 				template += '    <tr>';
// 				template += '   <td width="20">&nbsp;</td>';
// 				template += '   <td colspan="2" align="left"><input type="text" name="category_sub[]" class="text input-small" value="'+val.name+'"></td>';
// 				template += '    <td width=""colspan="3"><div class="btn  hidden fl edelete" data-type="'+val.id+'"  >删除</div></td>';
// 				template += '   <td >&nbsp;</td>';
// 				template += '  </tr>';
// 			    });
// 				template += '</table>';
// 				template += '<div class="form-item"><div class="btn  hidden fl" id="template_sub_add" style="margin-left:20px" >增加条数</div></div>';
// 				template += '</div></div>';
// 				template += '</div>';
// 			}

// 				layer.open({
// 				    type: 1,
// 				    skin: 'layui-layer-demo', //样式类名
// 				    closeBtn: 1, //关闭按钮
// 				    shift: 2,
// 				    shadeClose: true, //开启遮罩关闭
// 				    content: template,
// 				    btn: ['提交', '关闭'],
// 				    title : '修改'+name,
// 				    area : ['320px', '500px'],				 
// 				    yes : function(index, layero){					    	
// 				    	//layer.confirm('删除成功', {icon: 6, time: 1000});				    	
// 				    	var location = $(":input[name='location']").val();
// 				    	var payment_type = $("#payment_type").val();
// 				    	$("#template_id_layer").val(payment_type);
// 				    	$("#location_layer").val(location);
// 				    	$("#type_layer").val(type);
// 				    	data= $("#form1").serialize();
// 				    	$.post('/admin.php?s=/ProductTemplateClassify-add.html', data, function(data){				    		
// 				    		if(data.status != 0){
// 				    			//category_list
// 				    			var html='';
// 				    			html += '<div id="category_'+data.id+'" class="fl" style="overflow: hidden; width: auto">';
// 								html += '	<label class="item-label_1">'+data.name+'<span class="check-tips"></span></label>';
// 								html += '	<div class="controls_new fl">';
// 								html += '		<select name="sb_category_sub"class="input-small">';	
// 								$(data.category_sub).each(function(key, val){
// 									html += '<option value="'+val.id+'"  >'+val.name+'</option></volist>';
// 								})			
// 								html += '		</select>';				
// 								html += '	</div>';
// 								html += '	<div class="fl"><div class="btn  hidden fl sb_rule_edit" data-type="'+data.id+'" types="'+type+'" >修改</div></div></div>';
// 				    			//console.log(html);
// 				    			$("#category_"+data.id).remove();
// 				    			$("#category_list_"+type).append(html);	
// 				    			layer.msg('添加成功！', {icon: 6, time: 1000});				
// 				    			layer.close(index);
// 				    		}else if(data.status == 0){
// 				    			layer.msg(data.info, {icon: 6, time: 1000});	
// 				    		}
// 				    	});
				    		    	
// 				    },
// 				})				
			
// 			$(".edelete").click(function(){	
// 				var $that = $(this);
// 				var d = $(this).attr('data-type');	
// 				layer.confirm('删除?', {icon: 6, }, function(index){					
// 					 $.post('/admin.php?s=/ProductTemplateClassify-classify_del.html', {'type':type, 'classify_id': d}, function(data){
// 					 	if(data.status == 0){//删除失败
// 					 		layer.msg(data.info, {icon: 6, time: 1000});
// 					 	}else if(data.status == 2){
// 					 		layer.msg(data.content, {icon: 6, time: 1000});
// 					 	}else if(data.status == 1){
// 					 		layer.msg(data.info, {icon: 6, time: 1000});	
// 					 	    $that.parent("td").parent("tr").remove();
// 					 	    if($("#category_"+d).length > 0 ) {					 	    	
// 								$("#category_"+d).remove();	
// 								layer.closeAll();
// 								} 							
															
// 					 	}else{
// 					 		layer.msg('删除失败！', {icon: 6, time: 1000});
// 					 	}
// 					 });
 					
// 				}); 	

// 			})

// 		$("#template_sub_add").click(function(){	
// 			var cate = template_sub;
// 			$("#table_tem").append(cate);
// 			$(".delete").click(function(){
// 				layer.msg('已删除', {icon: 6, time:1000}); 
// 				 $(this).parent("td").parent("tr").remove();	
// 			});	
// 		});

// 		});




// 		})
// });



//同步调整
//$(".synch").click(function(){
$("body").on('click','.synch',function(){	
	$(this).each(function(i){			
	  var  name = '社保基数', type, str = 'sb';
	  type = $(this).attr('data-type');
	  //console.log(type);
	  if(type == 2){name = '公积金基数',str = 'gjj' }
    	var template_id = $("#template_id").val();
    	var type = type;
    	var payment_type = $("#payment_type").val();
    	$.post('/admin.php?s=/ProductTemplateClassify-show_rules_list.html', {'template_id': template_id, 'type': type, 'payment_type': payment_type}, function(data){
 				var synch = '';
				synch += '<form name="form2" id="form2"><div>';
				synch += '<input type="hidden" name="classify_id" class="text input-mid" id="classify_id_layer" value="">';
				synch += '<input type="hidden" name="template_id" class="text input-mid" id="template_id_layer" value="'+template_id+'">';
				synch += '<input type="hidden" name="type" class="text input-mid" id="type_layer" value="'+type+'">';
				synch += '<input type="hidden" name="payment_type" class="text input-mid" id="payment_type_layer" value="'+payment_type+'">';									
				synch += '<table>';
				synch += '  <tr>';
				synch += '   <td width="20">&nbsp;</td>';
				synch += '    <td width="80">基数范围</td>';
				synch += '    <td width="50"><input type="text" name="min" class="text input-small" value=""  onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"  maxlength="10"></td>';
				synch += '    <td  width="20"  align="center">~</td>';
				synch += '    <td width="50"><input type="text" name="max" class="text input-small" value="" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"  maxlength="10"></td>';
				synch += '    <td >&nbsp;</td>';
				synch += '  </tr>';
				synch += '</table>';
				synch += '<div class="form-item" style=" margin-top: 5px">';	
				synch += ' <label class="item-label_1" style=" margin-left: 20px;"><input class="ids" type="checkbox" name="control" value="1" id="control" />&nbsp;适用于当前已报增及在保的人员</label></div>';
				synch += '<div class="h5">&nbsp;</div>';
				synch += '<div class="form-item" style=" margin-top: 5px">';	
				synch += ' <label class="item-label_1" style=" margin-left: 20px;"><input class="ids" type="checkbox" name="location_id" value="1" id="classify_id" />&nbsp;同步调整该城市以下模板</label></div>';						
				synch += '<div class="form-item">';
				synch += '	<div class="table-striped" style="width: 100%; overflow: hidden; height: auto; display:none" id="location_template_list">';
				synch += '		<table width="800" >';		    		
	    		if(data){
	    			 data = eval("("+data+")"); 
					$.each(data, function(key, val){
					synch += '		  <tr>';
					synch += '		   <td width="30">&nbsp;</td>';
					synch += '		    <td width=""> <label class="item-label_1" style=" margin-left: 20px;"><input class="ids" type="checkbox" checked name="location_template_list[]" value="'+val.id+'" />&nbsp;'+val.name+'</label></td>';			    
					synch += '		  </tr>';
					});		  

	    		}//
				synch += '		 </table>';
				synch += '	</div>';
				synch += ' </div>'; 
				synch += '</div></form>';
				
			layer.open({
			    type: 1,
			    skin: 'layui-layer-demo', //样式类名
			    closeBtn: 1, //关闭按钮
			    shift: 2,
			    shadeClose: true, //开启遮罩关闭
			    content: synch,
			    btn: ['提交', '关闭'],
			    title : '同步调整'+name,
			    area : ['500px', '500px'],
			    scrollbar: false,
			    yes : function(index){ 
			    	var category_sub = [];
					$("#category_list_"+type+" select[name$='category_sub[]']").each(function(i){
						if($(this).val() > 0){
							category_sub.push($(this).val());
						}				    			 
					});
			    	$("#classify_id_layer").val(category_sub);
			    	data= $("#form2").serialize();		    	
			    	$.post('/admin.php?s=/ProductTemplate-synchro_control.html', data, function(data){
			    		if(data.status == 0 ){
							layer.msg(data.info, {icon: 6, time: 3000});
			    		}else if(data.status == 1){
			    			layer.msg(data.info, {icon: 6, time: 1000});
			    			layer.close(index);
			    		}else{
			    			layer.msg('错误！', {icon: 6, time: 1000});
			    		}
			    	});
			    	
			    },
			});	
			$("#classify_id").click(function(){ $("#location_template_list").show();})	    		
    	});



	})
});


///分类下拉事件

//$(".category_sub").change(function(){
$("body").on('change','.category_sub',function(){	
	var type = $(this).attr('data-type');
	var str = 'gjj';
	if(type == 1){ str = 'sb'}
	var category_sub = [];
	$("#category_list_"+type+" select[name$='category_sub[]']").each(function(i){
		if($(this).val() > 0){
			category_sub.push($(this).val());
		}				    			 
	});
	//if(category_sub.length == 0)  return false;	
	var template_id = $("#template_id").val();
	//console.log(category_sub)
		$.get('/admin.php?s=ProductTemplate-show_classify_rules.html', {'template_id': template_id, 'type': type, 'category_sub': category_sub}, function(data){
			 if(data.status == 0){
				layer.msg(data.info, {icon: 6, time: 1000});
			 }else{
				$("#"+str+"_content").html(data);
			 }
		},
		'html'
		);

	
});