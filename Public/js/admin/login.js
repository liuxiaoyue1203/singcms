/*
 * 	前端登录业务类 jQuery 
 *  在login页面的登录按钮触发事件使用	
*/
var login={
		check : function(){
			//获取登录页面中的用户名 密码
			var username = $('input[name="username"]').val();
			var password = $('input[name="password"]').val();

			if(!username){
				dialog.error('用户名不能为空！');
			}
			if(!password){
				dialog.error('密码不能为空！');
			}

			var url = "/admin.php?m=admin&c=login&a=check";
			var data = {'username':username,'password':password};
			// 以post方式提交数据data到url，结果返回到result
			// 执行异步请求
			$.post(url,data,function(result){
                dumpObj(result);
				if(result.status == 0){
					return dialog.error(result.message);
				}else if(result.status == 	1){
					return dialog.success(result.message,'/admin.php?m=admin&c=index');
				}
			},'JSON');
		}
}

// 打印对象object 用于调试
function dumpObj(myObject) {
    var s = "";
    for (var property in myObject) {
        s = s + "\n "+property +": " + myObject[property] ;
    }
    alert(s);
}