function modifyTemplate(id)
{
	$.get('/admin.php?s=/ProductTemplate-templatePrice.html',{template_id:id},function(msg){
		layer.open({
			type: 1,
			skin: 'layui-layer-demo', //样式类名
			closeBtn: 1, //关闭按钮
			shift: 2,
			area: ['600px','500px'],
			shadeClose: true, //开启遮罩关闭
			content: msg,
			btn: ['提交', '关闭'],
			title : '修改缴费标准',
			scrollbar: false,
			yes : function(index){ 
				var data = $('#modifyPrice').serialize();
				$.post('/admin.php?s=/ProductTemplate-templatePrice.html', data, function(msg) {
					if(msg.status){
						layer.msg('修改成功！', {icon: 1, time: 1000});
						setTimeout(function(){
							window.location.reload(true);
						},3000);
					}else{
						layer.msg('修改失败！', {icon: 6, time: 1000});
						layer.close(index);
					}
				},'json');
				
			  },
		});
	},'html')
}


function modifySb(id,location)
{
	var template_id = parseInt(id);
	var classify_len = $('.category_sub').length;
	var sb_category_sub = new Array();
	for(var i=0;i<classify_len;i++)
	{
		sb_category_sub[i] = $('.category_sub').eq(i).val();
	}
	var load = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
	setTimeout(function(){layer.close(load)},3000);
	$.post('/admin.php?s=/ProductTemplate-modifySb.html',{sb_category_sub:sb_category_sub,template_id:template_id},function(msg){
		layer.open({
		type: 1,
		skin: 'layui-layer-demo', //样式类名
		closeBtn: 1, //关闭按钮
		shift: 2,
		area: ['1000px', '600px'],
		shadeClose: true, //开启遮罩关闭
		content: msg,
		btn: ['提交', '关闭'],
		title : '修改社保规则',
		scrollbar: false,
		yes : function(index){ 
			var data = $('#modify_sb').serialize();
			$.post('/admin.php?s=/ProductTemplate-modifySbHandle.html', data, function(msg) {
				layer.close(load);
				if(msg.status==1)
				{
					layer.msg('修改成功！', {icon: 1, time: 1000});
					layer.close(index);
					setTimeout(function(){
						window.location.reload(true);
					},3000)
				}
				else
				{
					layer.msg('修改失败！', {icon: 6, time: 1000});
				}

			},'json');
		 },
	});
	},'html');
}
var sb_other = '<tr><td>费用名称</td><td><input type="text" name="sb_other[name][]"></td><td>企业金额</td><td><input type="text" name="sb_other[company][]"><span class="unit">元</span></td><td>个人金额</td><td><input type="text" name="sb_other[person][]"><span class="unit">元</span></td><td></td><td><div class="btn remove_other">删除</div></td></tr>';
$('.add_other').click(function() {
	$('.other').append(sb_other);
});
var rule_table_html = '<tr>';
rule_table_html+='			<th colspan="14" align="left">能否补缴</th>';
rule_table_html+='		</tr>';
rule_table_html+='		<empty name="sb_rule">';
rule_table_html+='		<tr>';
rule_table_html+='			<td><input type="checkbox" name="bujiao[]" value="0" class="bujiao"></td>';
rule_table_html+='			<td>养老保险 <input type="hidden" name="sb[items][]" value="养老保险"></td>';
rule_table_html+='			<td><input type="text"  name="sb[amount][]"  onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10" class="number"></td>';
rule_table_html+='			<td>元&nbsp;</td>';
rule_table_html+='			<td><span>单位比例</span></td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[company][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10" class="number">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>%</span> +';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[company][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10" class="number">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>元&nbsp;</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>个人比例</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[person][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>%</span> +';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[person][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>元&nbsp;</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td></td>';
rule_table_html+='		</tr>';
rule_table_html+='		<tr>';
rule_table_html+='			<td><input type="checkbox" name="bujiao[]" value="1" class="bujiao"></td>';
rule_table_html+='			<td>医疗保险<input type="hidden" name="sb[items][]" value="医疗保险"></td>';
rule_table_html+='			<td><input type="text"  name="sb[amount][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10" ></td>';
rule_table_html+='			<td>元&nbsp;</td>';
rule_table_html+='			<td><span>单位比例</span></td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[company][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>%</span> +';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[company][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>元&nbsp;</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>个人比例</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[person][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>%</span> +';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[person][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>元&nbsp;</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td></td>';
rule_table_html+='		</tr>';
rule_table_html+='		<tr>';
rule_table_html+='			<td><input type="checkbox" name="bujiao[]" value="2" class="bujiao"></td>';
rule_table_html+='			<td>失业保险<input type="hidden" name="sb[items][]" value="失业保险" ></td>';
rule_table_html+='			<td><input type="text"  name="sb[amount][]"  onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10"></td>';
rule_table_html+='			<td>元&nbsp;</td>';
rule_table_html+='			<td><span>单位比例</span></td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[company][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>%</span> +';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[company][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>元&nbsp;</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>个人比例</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[person][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>%</span> +';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[person][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>元&nbsp;</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td></td>';
rule_table_html+='		</tr>';
rule_table_html+='		<tr>';
rule_table_html+='			<td><input type="checkbox" name="bujiao[]" value="3" class="bujiao"></td>';
rule_table_html+='			<td>生育保险<input type="hidden" name="sb[items][]" value="生育保险"></td>';
rule_table_html+='			<td><input type="text"  name="sb[amount][]"  onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10"></td>';
rule_table_html+='			<td>元&nbsp;</td>';
rule_table_html+='			<td><span>单位比例</span></td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[company][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>%</span> +';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[company][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>元&nbsp;</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>个人比例</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[person][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>%</span> +';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[person][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>元&nbsp;</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td></td>';
rule_table_html+='		</tr>';
rule_table_html+='		<tr>';
rule_table_html+='			<td><input type="checkbox" name="bujiao[]" value="4" class="bujiao"></td>';
rule_table_html+='			<td>工伤保险<input type="hidden" name="sb[items][]" value="工伤保险"></td>';
rule_table_html+='			<td><input type="text"  name="sb[amount][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10" ></td>';
rule_table_html+='			<td>元&nbsp;</td>';
rule_table_html+='			<td><span>单位比例</span></td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[company][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>%</span> +';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[company][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>元&nbsp;</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>个人比例</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[person][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>%</span> +';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[person][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>元&nbsp;</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td></td>';
rule_table_html+='		</tr>';
rule_table_html+='		<tr>';
rule_table_html+='			<td><input type="checkbox" name="bujiao[]" value="5" class="bujiao"></td>';
rule_table_html+='			<td>重大疾病医疗<input type="hidden" name="sb[items][]" value="重大疾病医疗"></td>';
rule_table_html+='			<td><input type="text"  name="sb[amount][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10" ></td>';
rule_table_html+='			<td>元&nbsp;</td>';
rule_table_html+='			<td><span>单位比例</span></td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[company][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>%</span> +';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[company][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>元&nbsp;</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>个人比例</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[person][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>%</span> +';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<input type="text" name="sb[person][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\,]/g,\'\')"  maxlength="10">';
rule_table_html+='			</td>';
rule_table_html+='			<td>';
rule_table_html+='				<span>元&nbsp;</span>';
rule_table_html+='			</td>';
rule_table_html+='			<td></td>';
rule_table_html+='		</tr>';
//存储分类id
var classify_mixed = new Array();
$('.classify_mixed').change(function() {
	var index = $(this).attr('index');
	classify_mixed[index] = $(this).val();
	var template_id = $('#template_id').val();
	$.post('/admin.php?s=/ProductTemplate-modifySb.html', {sb_category_sub: classify_mixed,get_rule:true,template_id:template_id}, function(msg) {
		if(msg){
			$('#rule_id').val(msg.rule_id);
			var str = '<tr><th colspan="14" align="left">能否补缴</th></tr><tr>';
			$.each(msg.rule.items,function(index, el) {
				if(el.rules.replenish==1){
					str += '<td><input type="checkbox" name="bujiao[]" value="'+index+'" class="bujiao" checked=checked></td>';
				}else{
					str += '<td><input type="checkbox" name="bujiao[]" value="'+index+'" class="bujiao" ></td>';
				}
				str += '<td>'+el.name+'<input type="hidden" name="sb[items][]" value="'+el.name+'"></td>';
				str += '<td><input type="text"  name="sb[amount][]"  value="'+el.rules.amount+'" class="number"></td>';
				str += '<td>元&nbsp;</td>';
				str += '<td><span>单位比例</span></td>';
				str += '<td><input type="text" name="sb[company][]" value="'+el.rules.company[0]+'"></td>';
				str += '<td><span>%</span> +</td>';
				str += '<td><input type="text" name="sb[company][]" value="'+el.rules.company[1]+'"></td>';
				str += '<td><span>元&nbsp;</span></td>';
				str += '<td><span>个人比例</span></td>';
				str += '<td><input type="text" name="sb[person][]" value="'+el.rules.person[0]+'"></td>';
				str += '<td><span>%</span> +</td>';
				str += '<td><input type="text" name="sb[person][]" value="'+el.rules.person[1]+'"></td>';
				str += '<td><span>元&nbsp;</span></td></tr>';
			});
			$('.rule_table').html(str);
			$('.sb_mins').val(msg.rule.min);
			$('.sb_maxs').val(msg.rule.max);
			$('.pro_costs').val(msg.rule.pro_cost);
			$('.meterials').val(msg.rule.material);
			if(msg.rule.other){
				var str_other = '';
				$.each(msg.rule.other,function(index, el) {
					str_other += '<tr><td>费用名称</td><td><input type="text" value="'+el.name+'" name="sb_other[name][]"></td><td>企业金额</td><td><input type="text" value="'+el.rules['company']+'" name="sb_other[company][]"><span class="unit">元</span></td><td>个人金额</td><td><input type="text" value="'+el.rules['person']+'" name="sb_other[person][]"><span class="unit">元</span></td></tr>';
				});
				$('.other').html(str_other);
			}else{
				$('.other').html(sb_other);
			}
			$('#sb_rules').show();
			$('.no-record').hide();
		}else{
			$('.rule_table').html(rule_table_html);
			$('.sb_mins').val('');
			$('.sb_maxs').val('');
			$('.pro_costs').val('');
			$('.meterials').val('');
			$('.other').html(sb_other);
		}

	},'json');
});


$('body').on('click', '.remove_other', function() {
	$(this).parent().parent().remove();
});

var sb_rule = '<tr><td><input type="checkbox" name="bujiao[]" value="0" class="bujiao"></td><td><input type="text" name="sb[items][]" value="" style="width:70px"></td><td><input type="text"  name="sb[amount][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"   maxlength="10" ></td><td>元&nbsp;</td><td><span>单位比例</span></td><td><input type="text" name="sb[company][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"  maxlength="10"></td><td><span>%</span> +</td><td><input type="text" name="sb[company][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"   maxlength="10"></td><td><span>元&nbsp;</span></td><td><span>个人比例</span></td><td><input type="text" name="sb[person][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')"   maxlength="10"></td><td><span>%</span> +</td><td><input type="text" name="sb[person][]" onKeyUp="value=value.replace(/[^\\d\\.]/g,\'\')" onafterpaste="value=value.replace(/[^\\d\\.]/g,\'\')" maxlength="10"></td><td><span>元&nbsp;</span></td><td><div class="btn remove_rules" style="padding: 3px;font-size:12px;">删除</div><td></tr>';
$('.add_rules').click(function() {
	$('.rule_table').append(sb_rule);
	var len = $('.bujiao').length;
 	$('.bujiao').eq(len-1).val(len-1);
});
$('body').on('click', '.remove_rules', function(event) {
	$(this).parent().parent().remove();
});
var old_num = 0;
$(".number").bind({
	focus:function(){
		old_num = $(this).val();
	},  
	blur:function(){
		new_num = $(this).val();
		// alert(old_num-new_num);
	}  
});
var template = '<form name="form1" id="add_classify" method="post" action="/admin.php?s=/ProductTemplateClassify-add.html" ><div>';
template += '<input type="hidden" name="modify" class="text input-mid"  value="1">';
template += '<input type="hidden" name="template_id" class="text input-mid" id="classify_template_id" value="">';
template += '<input type="hidden" name="type" class="text input-mid" id="classify_template_id" value="1">';
template += '<div class="form-item">';	
template += '	<div class="table-striped" style="width: 100%px; overflow: hidden; height: auto;">';
template += '<table id="table_tem">';
template += '  <tr>';
template += '   <td width="20">&nbsp;</td>';
template += '<td width="80" align="left">分类名称</td>';
template += '    <td width="50"> <input type="text" name="category" class="text input-small" value="" style="border:1px solid #dedede"></td>';
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
template += '   <td colspan="2" align="left"><input type="text" name="category_sub[]" class="text input-small" value="" style="border:1px solid #dedede"></td>';
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
template_sub += '   <td colspan="2" align="left"><input type="text" name="category_sub[]" class="text input-small" value="" style="border:1px solid #dedede"></td>';
template_sub += '    <td width=""colspan="3"><div class="btn  hidden fl delete" >删除</div></td>';
//template_sub += '   <td >&nbsp;</td>';
template_sub += '  </tr>';
function addClassify(id)
{
	layer.open({
		type: 1,
		skin: 'layui-layer-demo', //样式类名
		closeBtn: 1, //关闭按钮
		shift: 2,
		area: 'auto',
		shadeClose: true, //开启遮罩关闭
		content: template,
		btn: ['提交', '关闭'],
		title : '添加分类',
		scrollbar: false,
		yes : function(classify){ 
			$('#classify_template_id').val(id);
			var data = $("#add_classify").serialize();
			$.post('/admin.php?s=/ProductTemplateClassify-add.html',data, function(msg) {
				if(msg.status!=0){
					var classify_html = '<div class="sb_classify">';
					classify_html += '<label for="" class="classify_name">'+msg.name+'</label>';
					classify_html += '<select name="sb_category_sub[]" class="classify_mixed" index="">';
					classify_html +='<option value="">请选择</option>';
					$.each(msg.category_sub,function(index, el) {
						classify_html +='<option value="'+el.id+'" >'+el.name+'</option>';
					});
					classify_html +='</select></div>';
	 				$('#classify_content').append(classify_html);
					var len = $('.classify_mixed').length;
	 				$('.classify_mixed').eq(len-1).attr('index',len-1);
	 				layer.close(classify);
	 			}else{
	 				layer.msg(msg.info, {icon: 6, time: 1000});
	 			}
			},'json');
		},
	});
	$("#template_sub_add").click(function(){	
		var cate = template_sub;
		$("#table_tem").append(cate);
		$(".delete").click(function(){
			layer.msg('已删除', {icon: 6, time:1000}); 
			 $(this).parent("td").parent("tr").remove();	
		});	
	});
}
/**
 * [modifyClassify 修改社保分类]
 * @param  {[type]} template_id [description]
 * @return {[type]}             [description]
 */
function modifyClassify(template_id)
{
	var fid = $('#classify_name').attr('fid');
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
						$('.classify_mixed').html(option_html);
						layer.msg('修改成功!',{icon:1,time:1000});
						layer.close(classify);
					}else{
						layer.msg(msg.msg,{icon:5,time:1000});
					}
				},'json');
			},
		});
	},'html');
}

/**
 * [modifyGjj 修改公积金]
 * @param  {[type]} template_id [模板id]
 * @return {[type]}             [description]
 */
function modifyGjj(template_id)
{
	$.get('/admin.php?s=/ProductTemplate-modifyGjj.html',{template_id:template_id},function(msg){
		layer.open({
			type: 1,
			skin: 'layui-layer-demo', //样式类名
			closeBtn: 1, //关闭按钮
			shift: 2,
			area: ['1000px', '600px'],
			shadeClose: true, //开启遮罩关闭
			content: msg,
			btn: ['提交', '关闭'],
			title : '修改公积金规则',
			scrollbar: false,
			yes : function(classify){ 
				var data = $('#modify_gjj').serialize();
				console.log(data);
				$.post('/admin.php?s=/ProductTemplate-modifyGjj.html',data,function(msg){
					if(msg.status==1)
					{
						layer.msg(msg.info,{icon:1,time:2000});
						setTimeout(function(){
							window.location.reload(true);
						},3000)
						layer.close(classify);
					}else{
						layer.msg(msg.info,{icon:5,time:1000});
					}
				},'json');
			},
		});
	},'html');
}

/**
 * 修改公积金
 * @param {int} [template_id] [模板id]
 */
function modifyDisabled(template_id)
{
	var czj_html = '<form id="modify_czj_form">';
	//原来公积金跟随者（社保、公积金）
	czj_html += '<input type="hidden" value="'+template_id+'" name="template_id">';
	czj_html += '<div class="form_item_sub"><label for="">残障金</label><input name="czj[disabled]" id="modify-czj">元/人/月</div>';
	czj_html += '<div class="form_item_sub"><label for="">缴纳方式</label><select name="czj[follow]"><option value="1">跟随社保</option><option value="2">跟随公积金</option></select></div>';
	czj_html +='</form>';
	layer.open({
		type: 1,
		skin: 'layui-layer-demo', //样式类名
		closeBtn: 1, //关闭按钮
		shift: 2,
		shadeClose: true, //开启遮罩关闭
		content: czj_html,
		btn: ['提交', '关闭'],
		title : '修改残障金',
		scrollbar: false,
		yes : function(classify){ 
			var czj_amount = parseInt($('#modify-czj').val());
			if(czj_amount<=0 || isNaN(czj_amount))
			{
				layer.msg('金额只能为数字且不小于0!',{icon:5,time:1000});
				return false;
			}
			var data = $('#modify_czj_form').serialize();
			$.post('/admin.php?s=/ProductTemplate-modifyCzj.html',data,function(msg){
				if(msg.status==1)
				{
					layer.msg(msg.info,{icon:1,time:2000});
					setTimeout(function(){
						window.location.reload(true);
					},3000)
					layer.close(classify);
				}else{
					layer.msg(msg.info,{icon:5,time:1000});
				}
			},'json');
		},
	});
}