// jquery事件   该文件被footer.html包含

/*
 * 添加按钮操作
 */
$('#button-add').click(function(){
	//dialog.success('点击添加');
	var url = SCOPE.add_url;
	window.location.href=url;
});

/*
 * 提交form表单数据   添加、更新操作
 */
$('#singcms-button-submit').click(function(){
	//dialog.success('点击提交');
	// 获取表单的值 
	var data = $('#singcms-form').serializeArray();
	//console.log(data);exit;
	postData = {};
	$(data).each(function(i){
		postData[this.name]=this.value;
	});
	
	url=SCOPE.save_url;
	//通过Ajax方式 post给服务器
	$.post(url,postData,function(result){
		if(result.status == 1){
			return dialog.success(result.message,SCOPE.jump_url);
		}else if(result.status == 0){
			return dialog.error(result.message);
		}
	},'JSON');
	
});

/*
 * 编辑模型
 * .on() 绑定一个或多个事件的事件处理函数
 */
$('.singcms-table #singcms-edit').on('click',function(){
//$('.singcms-table #singcms-edit').click(function(){
	var id=$(this).attr('attr-id');
	var url=SCOPE.edit_url+'&id='+id;
	window.location.href=url;
});


// 删除操作
$('.singcms-table #singcms-delete').on('click',function(){
	dialog.success('点击删除');
	var id=$(this).attr('attr-id');
	var a=$(this).attr('attr-a');
	var message=$(this).attr("attr-message");
	var url=SCOPE.set_status_url;
	
	data={};
	data['id']=id;
	data['status']=-1;
	
	layer.open({
		type:0,
		title:'是否提交？',
		btn:['yes','no'],
		icon:3,
		closeBtn:2,
		content:'是否确定'+message,
		scrollbar:true,
		yes:function(){
			todelete(url,data);
		},
	});
	
});

function todelete(url, data){
	$.post(url,data,function(s){
		if(s.status == 1){
			return dialog.success(s.message,'');
		}else{
			return dialog.error(s.message);
		}
	},"JSON");
}

//排序操作
$('#button-listorder').click(function(){
	//dialog.success('点击排序');
    //获取listorder内容   serializeArray返回的JSON对象由对象数组组成，格式：
	//[   {name: 'listorder[{$menu.menu_id}]', value: '{$menu.listorder}'}, 
	//	  {name: 'listorder[{$menu.menu_id}]', value: '{$menu.listorder}'},
	//	  {name: 'listorder[{$menu.menu_id}]'}, // 值为空                           ]
	var data=$('#singcms-listorder').serializeArray();
	postData={};
	//each 以每一个匹配的元素作为上下文来执行一个函数
	$(data).each(function(i){
		postData[this.name]=this.value;
	});
	//postData = { listorder[14]: "11", listorder[6]: "9", listorder[8]: "6", listorder[1]: "1", listorder[13]: "0", listorder[11]: "0" }
	var url=SCOPE.listorder_url; 
	$.post(url,postData,function(result){
		if(result.status==1){
			return dialog.success(result.message,result['data']['jump_url']);
		}else if(result.status==0){
			return dialog.error(result.message,result['data']['jump_url']);
		}
	},"JSON");
});

$('.singcms-table #singcms-on-off').on('click',function(){
	var id = $(this).attr('attr-id');
	var status=$(this).attr('attr-status');
	var url = SCOPE.set_status_url;

	data={};
	data['id']=id;
	data['status']=status;

	layer.open({
		type:0,
		title:'是否提交？',
		btn:['yes','no'],
		icon:3,
		closeBtn:2,
		content:"是否确定更改状态",
		scrollbar:true,
		yes:function(){
			//相关跳转
			todelete(url,data);
		},
	});
});

// 推送JS相关
$("#singcms-push").click(function(){
	var id = $("#select-push").val();
	//alert(id);
	if(id==0){
		return dialog.error('请选择推荐位');
	}
	push={};
	postData={};
	$("input[name='pushcheck']:checked").each(function(i){
		push[i]=$(this).val();
	});
	postData['push']=push;
	postData['position_id']=id;
	//console.log(postData);return;
	var url=SCOPE.push_url;
	$.post(url,postData,function(result){
		if(result.status==1){
			return dialog.success(result.message,result['data']['jump_url']);
		}
		if(result.status==0){
			return dialog.error(result.message);
		}
	},"JSON");
})



















